<?php

namespace App\Service\Page;

use App\Entity\Page;
use App\ExceptionListener\ErrorException;
use App\Repository\Page\PageRepository;
use App\Service\General\PredisService;
use App\Service\Hotel\HotelCategoryService;
use App\Service\Hotel\HotelService;
use App\Service\Hotel\HotelZoneService;
use App\Trait\StatusTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class PageService
{
    use StatusTrait;

    /**
     * @var string
     */
    private string $predis = 'page:page';

    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var PredisService
     */
    private PredisService $predisService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param PageRepository $pageRepository
     * @param TranslatorInterface $translator
     * @param HotelService $hotelService
     * @param HotelZoneService $hotelZoneService
     * @param HotelCategoryService $hotelCategoryService
     * @param PredisService $predisService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        PageRepository                        $pageRepository,
        TranslatorInterface                   $translator,
        private readonly HotelService         $hotelService,
        private readonly HotelZoneService     $hotelZoneService,
        private readonly HotelCategoryService $hotelCategoryService,
        PredisService                         $predisService,
        EntityManagerInterface                $em
    )
    {
        $this->pageRepository = $pageRepository;
        $this->translator = $translator;
        $this->predisService = $predisService;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function index(): array
    {
        if (!$data = $this->predisService->get($this->predis)) {

            $data = $this->pageRepository->findAll();

            $this->predisService->set($this->predis, $data);
        }

        return $this->serviceStatus($data);
    }

    /**
     * @return true[]
     */
    public function create(): array
    {
        return $this->serviceStatus([], [
            'hotel' => $this->hotelService->index()['data'],
            'zone' => $this->hotelZoneService->index()['data'],
            'category' => $this->hotelCategoryService->index()['data'],
        ]);
    }

    /**
     * @param $request
     * @return array
     * @throws ErrorException
     * @throws Exception
     */
    public function store($request): array
    {
        $page = new Page();
        return $this->extracted($page, $request);
    }

    /**
     * @param $id
     * @return array
     * @throws ErrorException
     */
    public function show($id): array
    {
        if (!$page = $this->predisService->getById($this->predis, $id)) {
            $page = $this->pageRepository->find($id);
        }

        if (!$page) {
            throw new ErrorException(
                $this->translator->trans('error.notFound'),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->serviceStatus($page);
    }

    /**
     * @param $id
     * @return mixed
     * @throws ErrorException
     */
    public function edit($id): array
    {
        return array_merge(
            $this->show($id),
            [
                'hotel' => $this->hotelService->index()['data'],
                'zone' => $this->hotelZoneService->index()['data'],
                'category' => $this->hotelCategoryService->index()['data'],
            ],
        );
    }

    /**
     * @param $id
     * @param $request
     * @return array
     * @throws ErrorException
     * @throws Exception
     */
    public function update($id, $request): array
    {
        $page = $this->show($id)['data'];
        return $this->extracted($page, $request);
    }

    /**
     * @param $id
     * @return true[]
     * @throws ErrorException
     */
    public function destroy($id): array
    {
        $hotel = $this->show($id)['data'];
        try {
            $this->pageRepository->destroy($hotel, true);

            $this->predisService->set($this->predis, $this->pageRepository->findAll());
        } catch (Throwable) {
            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus();
    }

    /**
     * @param Page $page
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function extracted(Page $page, $request): array
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $page->setTitle($request['title']);
            $page->setStarCount($request['starCount']);
            $page->setSlug($request['slug']);
            if ($request['hotel']) {
                $page->setHotel($this->hotelService->show($request['hotel'])['data']);
            } else if ($request['zone']) {
                $page->setZone($this->hotelZoneService->show($request['zone'])['data']);
            } else if ($request['category']) {
                $page->setCategory($this->hotelCategoryService->show($request['category'])['data']);
            }

            $this->pageRepository->storeUpdate($page, true);

            $this->predisService->set($this->predis, $this->pageRepository->findAll());

            $this->em->getConnection()->commit();
        } catch (Throwable) {
            $this->em->getConnection()->rollBack();

            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus($page);
    }
}