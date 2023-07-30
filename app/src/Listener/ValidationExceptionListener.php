<?php

namespace App\Listener;

use App\Exception\ValidationException;
use App\Model\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidationExceptionListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if (!($throwable instanceof ValidationException)){
            return;
        }

        $violations = [];
        /** @var ConstraintViolationInterface $error */
        foreach ($throwable->getErrors() as $error){
            $violations[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        $data = $this->serializer->serialize(
            new ErrorResponse('Validation Failed', ['violations' => $violations]),
            JsonEncoder::FORMAT
        );
        $event->setResponse(new JsonResponse($data, Response::HTTP_BAD_REQUEST, [], true));
    }
}
