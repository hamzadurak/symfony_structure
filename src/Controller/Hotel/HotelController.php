<?php

namespace App\Controller\Hotel;

use App\ExceptionListener\ErrorException;
use App\Service\Hotel\HotelService;
use App\Trait\RequestTrait;
use App\Validator\Hotel\Hotel\HotelStoreValidate;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/hotel')]
class HotelController extends AbstractController
{
    use RequestTrait;

    /**
     * @var HotelService
     */
    public HotelService $hotelService;

    /**
     * @param HotelService $hotelService
     */
    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'api_hotel_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $response = $this->hotelService->index();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/create', name: 'api_hotel_create', methods: ['GET'])]
    public function create(): JsonResponse
    {
        $response = $this->hotelService->create();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param HotelStoreValidate $hotelStoreValidate
     * @return JsonResponse
     * @throws ErrorException
     * @throws Exception
     */
    #[Route('/store', name: 'api_hotel_store', methods: ['POST'])]
    public function store(Request $request, HotelStoreValidate $hotelStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $hotelStoreValidate->validate($request);

        $response = $this->hotelService->store($request);

        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/{id}', name: 'api_hotel_show', methods: ['GET'])]
    public function show($id): JsonResponse
    {
        $response = $this->hotelService->show($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/edit/{id}', name: 'api_hotel_edit', methods: ['GET'])]
    public function edit($id): JsonResponse
    {
        $response = $this->hotelService->edit($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @param Request $request
     * @param HotelStoreValidate $hotelStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/update/{id}', name: 'api_hotel_update', methods: ['PATCH'])]
    public function update($id, Request $request, HotelStoreValidate $hotelStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $hotelStoreValidate->validate($request);

        $response = $this->hotelService->update($id, $request);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/destroy/{id}', name: 'api_hotel_destroy', methods: ['DELETE'])]
    public function destroy($id): JsonResponse
    {
        $response = $this->hotelService->destroy($id);

        return $this->json($response, Response::HTTP_OK);
    }
}
