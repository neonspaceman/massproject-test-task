<?php

namespace App\ArgumentResolver;

use App\Attribute\QueryString;
use App\Exception\QueryConvertException;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class QueryArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (count($argument->getAttributes(QueryString::class, ArgumentMetadata::IS_INSTANCEOF)) === 0){
            return [];
        }

        try {
            $model = $this->denormalizer->denormalize($request->query->all(), $argument->getType());
        } catch (Throwable $e) {
            throw new QueryConvertException($e);
        }

        $errors = $this->validator->validate($model);
        if (count($errors) > 0){
            throw new ValidationException($errors);
        }

        return [$model];
    }
}
