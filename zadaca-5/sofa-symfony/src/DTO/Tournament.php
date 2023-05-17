<?php

declare(strict_types=1);

namespace App\DTO;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Tournament
{
    public function __construct(
        #[SerializedName('Name')]
        public ?string $name,

        public ?string $slug,

        #[SerializedName('Id')]
        public ?string $id,

        /** @var Event[] */
        #[SerializedName('Events')]
        public ?array $events,
    ) {
    }
}