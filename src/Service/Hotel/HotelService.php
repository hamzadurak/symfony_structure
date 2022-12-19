<?php

namespace App\Service\Hotel;

use App\Entity\Hotel;
use App\ExceptionListener\ErrorException;
use App\Repository\Hotel\HotelRepository;
use App\Service\General\PredisService;
use App\Trait\StatusTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * hotel service
 */
class HotelService
{
    use StatusTrait;

    /**
     * @var string
     */
    private string $predis = 'hotel:hotel:hotel';

    /**
     * @var HotelRepository
     */
    private HotelRepository $hotelRepository;

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
     * @param HotelRepository $hotelRepository
     * @param TranslatorInterface $translator
     * @param HotelZoneService $hotelZoneService
     * @param PredisService $predisService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        HotelRepository                   $hotelRepository,
        TranslatorInterface               $translator,
        private readonly HotelZoneService $hotelZoneService,
        PredisService                     $predisService,
        EntityManagerInterface            $em
    )
    {
        $this->hotelRepository = $hotelRepository;
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

            $data = $this->hotelRepository->findAll();

            $this->predisService->set($this->predis, $data);
        }

        return $this->serviceStatus($data);
    }

    /**
     * @return true[]
     */
    public function create(): array
    {
        return $this->serviceStatus([], ['hotelZone' => $this->hotelZoneService->index()['data']]);
    }

    /**
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function store($request): array
    {
        $hotel = new Hotel();
        return $this->extracted($hotel, $request);
    }

    /**
     * @param $id
     * @return array
     * @throws ErrorException
     */
    public function show($id): array
    {
        if (!$hotel = $this->predisService->getById($this->predis, $id)) {
            $hotel = $this->hotelRepository->find($id);
        }

        if (!$hotel) {
            throw new ErrorException(
                $this->translator->trans('error.notFound'),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->serviceStatus($hotel);
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
                'hotelZone' => $this->hotelZoneService->index()['data'],
            ]
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
        $hotel = $this->show($id)['data'];

        return $this->extracted($hotel, $request);
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
            $this->hotelRepository->destroy($hotel, true);

            $this->predisService->set($this->predis, $this->hotelRepository->findAll());
        } catch (Throwable) {
            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus();
    }

    /**
     * @param mixed $hotel
     * @param $request
     * @return array
     * @throws ErrorException
     * @throws Exception
     */
    public function extracted(Hotel $hotel, $request): array
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $hotel->setName($request['name']);
            $hotel->setStarCount($request['starCount']);
            $hotel->setHotelZone($this->hotelZoneService->show($request['hotelZone'])['data']);

            $this->hotelRepository->storeUpdate($hotel, true);

            $this->predisService->set($this->predis, $this->hotelRepository->findAll());

            $this->em->getConnection()->commit();
        } catch (Throwable) {
            $this->em->getConnection()->rollBack();

            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus($hotel);
    }
}