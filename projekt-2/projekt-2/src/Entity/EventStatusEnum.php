<?php

declare(strict_types=1);

namespace App\Entity;

enum EventStatusEnum: string
{
    case NotStarted = 'notstarted';
    case InProgress = 'inprogress';
    case Finished = 'finished';
    case Canceled = 'canceled';
}