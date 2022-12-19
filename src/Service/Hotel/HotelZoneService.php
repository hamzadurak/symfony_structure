<?php

namespace App\Service\Hotel;

use App\Entity\HotelZone;
use App\ExceptionListener\ErrorException;
use App\Repository\Hotel\HotelZoneRepository;
use App\Service\General\PredisService;
use App\Trait\StatusTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class HotelZoneService
{
    use StatusTrait;

    /**
     * @var string
     */
    private string $predis = 'hotel:hotel-zone:hotel-zone';

    /**
     * @var HotelZoneRepository
     */
    private HotelZoneRepository $hotelZoneRepository;

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
     * @param HotelZoneRepository $hotelZoneRepository
     * @param TranslatorInterface $translator
     * @param PredisService $predisService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        HotelZoneRepository    $hotelZoneRepository,
        TranslatorInterface    $translator,
        PredisService          $predisService,
        EntityManagerInterface $em
    )
    {
        $this->hotelZoneRepository = $hotelZoneRepository;
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

            $data = $this->hotelZoneRepository->findAll();

            $this->predisService->set($this->predis, $data);
        }

        return $this->serviceStatus($data);
    }

    /**
     * @return true[]
     */
    public function create(): array
    {
        return $this->serviceStatus();
    }

    /**
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function store($request): array
    {
        $hotelZone = new HotelZone();
        return $this->extracted($hotelZone, $request);
    }

    /**
     * @param $id
     * @return array
     * @throws ErrorException
     */
    public function show($id): array
    {
        if (!$hotelZone = $this->predisService->getById($this->predis, $id)) {
            $hotelZone = $this->hotelZoneRepository->find($id);
        }

        if (!$hotelZone) {
            throw new ErrorException(
                $this->translator->trans('error.notFound'),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->serviceStatus($hotelZone);
    }

    /**
     * @param $id
     * @return mixed
     * @throws ErrorException
     */
    public function edit($id): array
    {
        return $this->show($id);
    }

    /**
     * @param $id
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function update($id, $request): array
    {
        $hotelZone = $this->show($id)['data'];

        return $this->extracted($hotelZone, $request);
    }

    /**
     * @param $id
     * @return true[]
     * @throws ErrorException
     */
    public function destroy($id): array
    {
        $hotelZone = $this->show($id)['data'];
        try {
            $this->hotelZoneRepository->destroy($hotelZone, true);

            $this->predisService->set($this->predis, $this->hotelZoneRepository->findAll());
        } catch (Throwable) {
            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus();
    }

    /**
     * @param mixed $hotelZone
     * @param $request
     * @return array
     * @throws ErrorException|Exception
     */
    public function extracted(HotelZone $hotelZone, $request): array
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $hotelZone->setCountry($request['country']);
            $hotelZone->setCity($request['city']);
            $this->hotelZoneRepository->storeUpdate($hotelZone, true);

            $this->predisService->set($this->predis, $this->hotelZoneRepository->findAll());

            $this->em->getConnection()->commit();
        } catch (Throwable) {
            $this->em->getConnection()->rollBack();
            throw new ErrorException(
                $this->translator->trans('error.store'),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->serviceStatus($hotelZone);
    }
}