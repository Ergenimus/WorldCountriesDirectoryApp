<?php

namespace App\Controller;

class CountryPreview {

    public function __construct(
        public string $name,
        public string $uri,
    ) {}
}