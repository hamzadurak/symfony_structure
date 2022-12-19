<?php

namespace App\ExceptionListener;

use App\Trait\StatusTrait;
use Exception;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * custom error exception
 */
class ErrorException extends Exception
{
    use StatusTrait;

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $getThrowable = $event->getThrowable();

        if (!$getThrowable instanceof ErrorException) {
            return;
        }

        $getMessage = $getThrowable->getMessage();
        $message = json_decode($getMessage, true);

        if (!$message) {
            $message = $getMessage;
        }

        $event->setResponse($this->errorStatus($message, false, $event->getThrowable()->getCode()));
    }
}