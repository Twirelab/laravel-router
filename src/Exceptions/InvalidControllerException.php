<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Exceptions;

use InvalidArgumentException;

class InvalidControllerException extends InvalidArgumentException
{
    public static function classNotFound(string $className): self
    {
        return new self(sprintf('Class "%s" does not exist.', $className));
    }

    public static function abstractClass(string $className): self
    {
        return new self(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $className));
    }

    public static function invalidGlobPattern(string $pattern): self
    {
        return new self(sprintf('Invalid glob pattern "%s".', $pattern));
    }

    public static function noClassInFile(string $filePath): self
    {
        return new self(sprintf('No class found in file "%s".', $filePath));
    }

    public static function fileReadError(string $filePath): self
    {
        return new self(sprintf('Unable to read file "%s".', $filePath));
    }
}
