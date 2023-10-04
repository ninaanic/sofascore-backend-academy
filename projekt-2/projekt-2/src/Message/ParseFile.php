<?php

namespace App\Message;

class ParseFile
{
    public function __construct(
        public readonly string $filename,
    ) {
    }
}