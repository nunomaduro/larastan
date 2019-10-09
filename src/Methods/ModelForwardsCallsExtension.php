<?php

declare(strict_types=1);

/**
 * This file is part of Larastan.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Larastan\Methods;

use PHPStan\Type\Type;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IterableType;
use NunoMaduro\Larastan\Concerns;
use PHPStan\Type\IntersectionType;
use Illuminate\Database\Eloquent\Model;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use PHPStan\Reflection\BrokerAwareExtension;
use Illuminate\Contracts\Pagination\Paginator;
use PHPStan\Reflection\ParametersAcceptorSelector;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use NunoMaduro\Larastan\Reflection\EloquentBuilderMethodReflection;

final class ModelForwardsCallsExtension implements  MethodsClassReflectionExtension, BrokerAwareExtension
{
    use Concerns\HasBroker;

    /** @var string[] */
    private $modelRetrievalMethods = ['first', 'find', 'findMany', 'findOrFail'];

    /** @var string[] */
    private $modelCreationMethods = ['make', 'create', 'forceCreate', 'findOrNew', 'firstOrNew', 'updateOrCreate'];

    /**
     * @return ClassReflection
     * @throws \PHPStan\Broker\ClassNotFoundException
     */
    protected function getBuilderReflection(): ClassReflection
    {
        return $this->broker->getClass(Builder::class);
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (! $classReflection->isSubclassOf(Model::class)) {
            return false;
        }

        if (in_array($methodName, ['increment', 'decrement', 'paginate', 'simplePaginate'], true)) {
            return true;
        }

        if ($classReflection->hasNativeMethod('scope' . ucfirst($methodName))) {
            // scopes handled later
            return false;
        }

        return $this->getBuilderReflection()->hasNativeMethod($methodName) || $this->broker->getClass(QueryBuilder::class)->hasNativeMethod($methodName);
    }

    public function getMethod(ClassReflection $originalModelReflection, string $methodName): MethodReflection
    {
        $returnType = null;
        $methodReflection = null;
        $queryBuilderReflection = $this->broker->getClass(QueryBuilder::class);

        if (in_array($methodName, ['increment', 'decrement'], true)) {
            $methodReflection = $this->getBuilderReflection()->getNativeMethod($methodName);

            $returnType = new IntegerType();
        } elseif (in_array($methodName, ['paginate', 'simplePaginate'], true)) {
            $methodReflection = $queryBuilderReflection->getNativeMethod($methodName);

            $returnType = new ObjectType($methodName === 'paginate' ? LengthAwarePaginator::class : Paginator::class);
        } elseif (in_array($methodName, array_merge($this->modelRetrievalMethods, $this->modelCreationMethods), true)) {
            $methodReflection = $this->getBuilderReflection()->getNativeMethod($methodName);

            $returnType = $this->getReturnTypeFromMap($methodName, $originalModelReflection->getName());
        }

        if ($this->getBuilderReflection()->hasNativeMethod($methodName)) {
            $methodReflection = $methodReflection === null ? $this->getBuilderReflection()->getNativeMethod($methodName) : $methodReflection;

            return new EloquentBuilderMethodReflection(
                $methodName, $originalModelReflection,
                ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getParameters(),
                $returnType
            );
        }

        return new EloquentBuilderMethodReflection(
            $methodName, $originalModelReflection,
            ParametersAcceptorSelector::selectSingle($queryBuilderReflection->getNativeMethod($methodName)->getVariants())->getParameters(),
            $returnType
        );
    }

    private function getReturnTypeFromMap(string $methodName, string $className) : Type
    {
        return [
            'first' => new IntersectionType([
                new ObjectType($className), new NullType()
            ]),
            'find' => new IntersectionType([
                new IterableType(new IntegerType(), new ObjectType($className)), new ObjectType($className), new NullType()
            ]),
            'findMany' => new ObjectType(Collection::class),
            'findOrFail' => new IntersectionType([
                new IterableType(new IntegerType(), new ObjectType($className)), new ObjectType($className)
            ]),
            'make' => new ObjectType($className),
            'create' => new ObjectType($className),
            'forceCreate' => new ObjectType($className),
            'findOrNew' => new ObjectType($className),
            'firstOrNew' => new ObjectType($className),
            'updateOrCreate' => new ObjectType($className),
        ][$methodName];
    }
}