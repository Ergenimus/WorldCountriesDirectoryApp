<?php

namespace App\Model;

    // Country - класс страны
    class Country{

        public function __construct(
            public string $fullName,
            public string $shortName,
            public string $isoAlpha2, //двухбуквенный код страны 
            public string $isoAlpha3, //трехбуквенный код страны 
            public string $isoNumeric, //числовой код страны 
            public int $population, //население страны
            public int $square //площадь страны
        ) {
            
        }
    }
?>