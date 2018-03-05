<?php


namespace Morgriem\Dancer;


final class DancerException extends \LogicException
{
    public const
        GENERIC_ERROR = 0,
        NOT_COMPATIBLE_TYPE = 1,
        PROPERTY_NOT_DEFINED = 2,
        PROPERTY_EXCLUDED = 3
    ;

    public static function genericError(\Throwable $previous = null): self
    {
        return new self("Error occured", self::GENERIC_ERROR, $previous);
    }

    public static function notCompatibleType(string $class): self
    {
        return new self("Given DTO class $class must be child of " . Dto::class, self::NOT_COMPATIBLE_TYPE);
    }

    public static function propertyNotDefined(string $property): self
    {
        return new self("Property $property is undefined in underlying entity.", self::PROPERTY_NOT_DEFINED);
    }

    public static function propertyExcluded(string $property): self
    {
        return new self("Property $property is excluded from DTO", self::PROPERTY_EXCLUDED);
    }
}