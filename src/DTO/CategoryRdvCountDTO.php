<?php

namespace App\DTO;

final class CategoryRdvCountDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public int $rdvCount
    ) {}
}
