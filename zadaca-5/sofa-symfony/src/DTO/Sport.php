<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Sport
{
    public function __construct(
        #[SerializedName('Name')]
        public string $name,

        public ?string $slug,

        #[SerializedName('Id')]
        public string $id,

        /** @var Tournament[] */
        #[SerializedName('Tournaments')]
        public array $tournaments,
    ) {
    }
}