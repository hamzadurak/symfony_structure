<?php

namespace App\Service\General;

use App\ExceptionListener\ErrorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws ErrorException
     */
    public function validate($constraints, $request): void
    {
        $errorValidate = [];
        $constraints = $this->validator->validate($request, $constraints);
        foreach ($constraints as $constraint) {
            $errorValidate[str_replace(['[', ']'], '', $constraint->getPropertyPath())][] = $constraint->getMessage();
        }

        if (count($errorValidate) > 0) {
            throw new ErrorException(json_encode($errorValidate), 400);
        }
    }
}