<?php

namespace App\Controller\Hotel;

use App\ExceptionListener\ErrorException;
use App\Service\Hotel\HotelZoneService;
use App\Trait\RequestTrait;
use App\Validator\Hotel\HotelZone\HotelZoneStoreValidate;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/hotel-zone')]
class HotelZoneController extends AbstractController
{
    use RequestTrait;

    /**
     * @var HotelZoneService
     */
    public HotelZoneService $hotelZoneService;

    /**
     * @param HotelZoneService $hotelZoneService
     */
    public function __construct(HotelZoneService $hotelZoneService)
    {
        $this->hotelZoneService = $hotelZoneService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'api_hotel_zone_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $requestAll = $request->query->all();
        if ($content = $request->getContent()) {
            $requestAll = json_decode($content, true);
        }

        $response = $this->hotelZoneService->index($requestAll);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/create', name: 'api_hotel_zone_create', methods: ['GET'])]
    public function create(): JsonResponse
    {
        $response = $this->hotelZoneService->create();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param HotelZoneStoreValidate $hotelZoneStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/store', name: 'api_hotel_zone_store', methods: ['POST'])]
    public function store(Request $request, HotelZoneStoreValidate $hotelZoneStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $hotelZoneStoreValidate->validate($request);

        $response = $this->hotelZoneService->store($request);

        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/{id}', name: 'api_hotel_zone_show', methods: ['GET'])]
    public function show($id): JsonResponse
    {
        $response = $this->hotelZoneService->show($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/edit/{id}', name: 'api_hotel_zone_edit', methods: ['GET'])]
    public function edit($id): JsonResponse
    {
        $response = $this->hotelZoneService->edit($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @param Request $request
     * @param HotelZoneStoreValidate $hotelZoneStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/update/{id}', name: 'api_hotel_zone_update', methods: ['PATCH'])]
    public function update($id, Request $request, HotelZoneStoreValidate $hotelZoneStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $hotelZoneStoreValidate->validate($request);

        $response = $this->hotelZoneService->update($id, $request);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/destroy/{id}', name: 'api_hotel_zone_destroy', methods: ['DELETE'])]
    public function destroy($id): JsonResponse
    {
        $response = $this->hotelZoneService->destroy($id);

        return $this->json($response, Response::HTTP_OK);
    }
}
