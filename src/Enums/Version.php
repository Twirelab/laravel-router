<?php

declare(strict_types=1);

namespace Twirelab\LaravelRouter\Enums;

enum Version: int
{
    case NEUTRAL = 0;
    case V1 = 1;
    case V2 = 2;
    case V3 = 3;

    public function isNeutral(): bool
    {
        return $this === self::NEUTRAL;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function fromInt(int $version): self
    {
        return self::from($version);
    }
}
