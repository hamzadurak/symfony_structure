<?php

namespace App\Controller\Hotel;

use App\ExceptionListener\ErrorException;
use App\Service\Hotel\HotelCategoryService;
use App\Trait\RequestTrait;
use App\Validator\Hotel\HotelCategory\HotelCategoryStoreValidate;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/hotel-category')]
class HotelCategoryController extends AbstractController
{
    use RequestTrait;

    /**
     * @var HotelCategoryService
     */
    public HotelCategoryService $hotelCategoryService;

    /**
     * @param HotelCategoryService $hotelCategoryService
     */
    public function __construct(HotelCategoryService $hotelCategoryService)
    {
        $this->hotelCategoryService = $hotelCategoryService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'api_hotel_category_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $response = $this->hotelCategoryService->index();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/create', name: 'api_hotel_category_create', methods: ['GET'])]
    public function create(): JsonResponse
    {
        $response = $this->hotelCategoryService->create();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param HotelCategoryStoreValidate $hotelZoneStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/store', name: 'api_hotel_category_store', methods: ['POST'])]
    public function store(Request $request, HotelCategoryStoreValidate $hotelZoneStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $hotelZoneStoreValidate->validate($request);

        $response = $this->hotelCategoryService->store($request);

        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/{id}', name: 'api_hotel_category_show', methods: ['GET'])]
    public function show($id): JsonResponse
    {
        $response = $this->hotelCategoryService->show($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/edit/{id}', name: 'api_hotel_category_edit', methods: ['GET'])]
    public function edit($id): JsonResponse
    {
        $response = $this->hotelCategoryService->edit($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @param Request $request
     * @param HotelCategoryStoreValidate $hotelZoneStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/update/{id}', name: 'api_hotel_category_update', methods: ['PATCH'])]
    public function update($id, Request $request, HotelCategoryStoreValidate $hotelZoneStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $hotelZoneStoreValidate->validate($request);

        $response = $this->hotelCategoryService->update($id, $request);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/destroy/{id}', name: 'api_hotel_category_destroy', methods: ['DELETE'])]
    public function destroy($id): JsonResponse
    {
        $response = $this->hotelCategoryService->destroy($id);

        return $this->json($response, Response::HTTP_OK);
    }
}
