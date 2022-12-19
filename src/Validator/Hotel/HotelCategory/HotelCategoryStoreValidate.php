<?php

namespace App\Validator\Hotel\HotelCategory;

use App\ExceptionListener\ErrorException;
use App\Service\General\ValidatorService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HotelCategoryStoreValidate
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var ValidatorService
     */
    private ValidatorService $service;

    /**
     * @param TranslatorInterface $translator
     * @param ValidatorService $service
     */
    public function __construct(TranslatorInterface $translator, ValidatorService $service)
    {
        $this->translator = $translator;
        $this->service = $service;
    }


    /**
     * @param $request
     * @return void
     * @throws ErrorException
     */
    public function validate($request): void
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank([
                    'message' => $this->translator->trans('validator.notBlank')
                ]),
                new Assert\NotNull([
                    'message' => $this->translator->trans('validator.notBlank')
                ]),
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => $this->translator->trans('validator.max')
                ]),
            ],
            'hotel' => [
                new Assert\NotBlank([
                    'message' => $this->translator->trans('validator.notBlank')
                ]),
                new Assert\NotNull([
                    'message' => $this->translator->trans('validator.notBlank')
                ]),
            ],
        ]);
        $this->service->validate($constraints, $request);
    }
}