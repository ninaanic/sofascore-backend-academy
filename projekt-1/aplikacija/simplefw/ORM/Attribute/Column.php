<?php

declare(strict_types=1);

namespace SimpleFW\ORM\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public readonly ?string $name = null,
    ) {
    }
}
