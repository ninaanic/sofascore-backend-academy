<?php

declare(strict_types=1);

namespace SimpleFW\ORM\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(
        public readonly ?string $tableName = null,
    ) {
    }
}
