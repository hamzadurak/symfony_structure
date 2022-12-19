<?php

namespace App\Controller\Page;

use App\ExceptionListener\ErrorException;
use App\Service\Page\PageService;
use App\Trait\RequestTrait;
use App\Validator\Page\PageStoreValidate;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/page')]
class PageController extends AbstractController
{
    use RequestTrait;

    /**
     * @var PageService
     */
    public PageService $pageService;

    /**
     * @param PageService $pageService
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'api_page_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $response = $this->pageService->index();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/create', name: 'api_page_create', methods: ['GET'])]
    public function create(): JsonResponse
    {
        $response = $this->pageService->create();

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param PageStoreValidate $pageStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/store', name: 'api_page_store', methods: ['POST'])]
    public function store(Request $request, PageStoreValidate $pageStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $pageStoreValidate->validate($request);

        $response = $this->pageService->store($request);

        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/{id}', name: 'api_page_show', methods: ['GET'])]
    public function show($id): JsonResponse
    {
        $response = $this->pageService->show($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/edit/{id}', name: 'api_page_edit', methods: ['GET'])]
    public function edit($id): JsonResponse
    {
        $response = $this->pageService->edit($id);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @param Request $request
     * @param PageStoreValidate $pageStoreValidate
     * @return JsonResponse
     * @throws ErrorException|Exception
     */
    #[Route('/update/{id}', name: 'api_page_update', methods: ['PATCH'])]
    public function update($id, Request $request, PageStoreValidate $pageStoreValidate): JsonResponse
    {
        $request = $this->request($request);

        $pageStoreValidate->validate($request);

        $response = $this->pageService->update($id, $request);

        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ErrorException
     */
    #[Route('/destroy/{id}', name: 'api_page_destroy', methods: ['DELETE'])]
    public function destroy($id): JsonResponse
    {
        $response = $this->pageService->destroy($id);

        return $this->json($response, Response::HTTP_OK);
    }
}