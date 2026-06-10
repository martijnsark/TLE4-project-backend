<?php

namespace App\Enums;

enum TagCategory: string
{
    case Navigation = 'navigation';
    case Topic = 'topic';
    case Flag = 'flag';

    public function label(): string
    {
        return match ($this) {
            self::Navigation => 'Navigation (hoofdcategorie in RN-app)',
            self::Topic      => 'Topic (inhoudelijk onderwerp)',
            self::Flag       => 'Flag (Trending, Goed nieuws, …)',
        };
    }
}
