<?php

declare(strict_types=1);

namespace App\Entity;

enum EventStatusEnum: string
{
    case NotStarted = 'not-started';
    case InProgress = 'in-progress';
    case Finished = 'finished';
    case Canceled = 'canceled';
}
