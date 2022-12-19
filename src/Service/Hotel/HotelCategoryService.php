<?php

namespace App\Service\Hotel;

use App\Entity\HotelCategory;
use App\ExceptionListener\ErrorException;
use App\Repository\Hotel\HotelCategoryRepository;
use App\Service\General\PredisService;
use App\Trait\StatusTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class HotelCategoryService
{
    use StatusTrait;

    /**
     * @var string
     */
    private string $predis = 'hotel:hotel-category:hotel-category';

    /**
     * @var HotelCategoryRepository
     */
    private HotelCategoryRepository $hotelCategoryRepository;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var PredisService
     */
    private PredisService $predisService;

    /**
     * @param HotelCategoryRepository $hotelCategoryRepository
     * @param TranslatorInterface $translator
     * @param HotelService $hotelService
     * @param PredisService $predisService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        HotelCategoryRepository       $hotelCategoryRepository,
        TranslatorInterface           $translator,
        private readonly HotelService $hotelService,
        PredisService                 $predisService,
        EntityManagerInterface        $em
    )
    {
        $this->hotelCategoryRepository = $hotelCategoryRepository;
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

            $data = $this->hotelCategoryRepository->findAll();

            $this->predisService->set($this->predis, $data);
        }

        return $this->serviceStatus($data);
    }

    /**
     * @return true[]
     */
    public function create(): array
    {
        return $this->serviceStatus([], ['hotelZone' => $this->hotelService->index()['data']]);
    }

    /**
     * @param $request
     * @return array
     * @throws ErrorException
     * @throws Exception
     */
    public function store($request): array
    {
        $hotelCategory = new HotelCategory();
        return $this->extracted($hotelCategory, $request);
    }

    /**
     * @param $id
     * @return array
     * @throws ErrorException
     */
    public function show($id): array
    {
        $this->predisService->set($this->predis, $this->hotelCategoryRepository->findAll());

        if (!$hotelCategory = $this->predisService->getById($this->predis, $id)) {
            $hotelCategory = $this->hotelCategoryRepository->find($id);
        }

        if (!$hotelCategory) {
            throw new ErrorException(
                $this->translator->trans('error.notFound'),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->serviceStatus($hotelCategory);
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
            ],
        );
    }

    /**
     * @param $id
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function update($id, $request): array
    {
        $hotelCategory = $this->show($id)['data'];
        return $this->extracted($hotelCategory, $request);
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
            $this->hotelCategoryRepository->destroy($hotel, true);

            $this->predisService->set($this->predis, $this->hotelCategoryRepository->findAll());
        } catch (Throwable) {
            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus();
    }

    /**
     * @param HotelCategory $hotelCategory
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function extracted(HotelCategory $hotelCategory, $request): array
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $hotelCategory->setName($request['name']);

            $hotelArray = array_combine($request['hotel'], $request['hotel']);
            foreach ($hotelArray as $hotelId) {
                $hotelCategory->addHotel($this->hotelService->show($hotelId)['data']);
            }
            foreach ($hotelCategory->getHotel()->getValues() as $getHotel) {
                if (!isset($hotelArray[$getHotel->getId()])) {
                    $hotelCategory->removeHotel($this->hotelService->show($getHotel->getId())['data']);
                }
            }

            $this->hotelCategoryRepository->storeUpdate($hotelCategory, true);

            $this->predisService->set($this->predis, $this->hotelCategoryRepository->findAll());
            dd('asd');
            $this->em->getConnection()->commit();
        } catch (Throwable $exception) {
            dd($exception->getMessage());
            $this->em->getConnection()->rollBack();

            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus($hotelCategory);
    }
}