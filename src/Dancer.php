<?php


namespace Morgriem\Dancer;


final class Dancer
{
    /**
     * Builds DTO of given type from some entity.
     *
     * @param object $entity
     * @param string $dtoClass
     * @return DtoInterface
     */
    public static function buildDtoFor(object $entity, string $dtoClass = Dto::class): DtoInterface
    {
        if (false === is_a($dtoClass, Dto::class, true)) {
            throw DancerException::notCompatibleType($dtoClass);
        }
        return new $dtoClass($entity);
    }
}