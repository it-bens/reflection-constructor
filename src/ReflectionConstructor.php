<?php

declare(strict_types=1);

namespace ITB\ReflectionConstructor;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

final class ReflectionConstructor
{
    /** @var ReflectionClass $class */
    private ReflectionClass $class;
    /** @var ReflectionParameter[] $constructorParameters */
    private array $constructorParameters;

    /**
     * @param string $className
     * @throws ReflectionException
     */
    public function __construct(string $className)
    {
        $this->class = new ReflectionClass($className);

        $constructor = $this->class->getConstructor();
        if (null === $constructor) {
            throw new ReflectionException(sprintf('The class %s contains no constructor.', $this->class->getName()));
        }
        $this->constructorParameters = $constructor->getParameters();
    }

    /**
     * @param string $className
     * @param string[] $excludedParameters
     * @return string|null
     * @throws ReflectionException
     */
    public function extractParameterNameForClassName(string $className, array $excludedParameters = []): ?string
    {
        $matchingParameters = array_filter(
            $this->constructorParameters,
            static function (ReflectionParameter $parameter) use ($className, $excludedParameters): bool {
                if (in_array($parameter->getName(), $excludedParameters)) {
                    return false;
                }

                $type = $parameter->getType();
                if (null === $type) {
                    return false;
                }

                if (!$type instanceof ReflectionNamedType) {
                    return false;
                }

                return is_a($className, $type->getName(), true);
            }
        );

        if (0 === count($matchingParameters)) {
            return null;
        }
        if (count($matchingParameters) > 1) {
            $parameterNames = array_map(static function (ReflectionParameter $parameter): string {
                return '"' . $parameter->getName() . '"';
            }, $matchingParameters);

            throw new ReflectionException(
                sprintf(
                    'The constructor for class %s contains more than one parameter of type %s: %s',
                    $this->class->getName(),
                    $className,
                    implode(', ', $parameterNames)
                )
            );
        }

        return $matchingParameters[0]->getName();
    }

    /**
     * @param object $object
     * @param string[] $excludedParameters
     * @return string|null
     * @throws ReflectionException
     */
    public function extractParameterNameForObject(object $object, array $excludedParameters = []): ?string
    {
        return $this->extractParameterNameForClassName(get_class($object), $excludedParameters);
    }
}
