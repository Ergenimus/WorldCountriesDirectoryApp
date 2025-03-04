<?php

namespace App\Rdb;

use mysqli;
use RuntimeException;
use Exception;

use App\Model\Country;
use App\Model\CountryRepository;
use App\Rdb\SqlHelper;

// СountryStorage - имплементация хранилища стран, работающая с БД
class CountryStorage implements CountryRepository{
    
    private $sqlHelper;

    public function __construct(SqlHelper $sqlHelper) {
        $this->sqlHelper = $sqlHelper;
    }

    // ПЕРЕОПРЕДЕЛЕНИЕ МЕТОДОВ ИНТЕРФЕЙСА CountryRepository
    public function getAll(): array {
        try {
            // создать подключение к БД
            $connection = $this->sqlHelper->openDbConnection();
            // подготовить строку запроса
            $queryStr = 'SELECT name_f, name_s, iso_alpha2, iso_alpha3, iso_numeric, popul, square FROM country_t;';
            // выполнить запрос
            $rows = $connection->query(query: $queryStr);
            // считать результаты запроса в цикле 
            $countries = [];
            while ($row = $rows->fetch_array()) {
                // каждая строка считается в тип массива
                $country = new Country(
                    fullName: $row[0],
                    shortName: $row[1],
                    isoAlpha2: $row[2],
                    isoAlpha3: $row[3],
                    isoNumeric: $row[4],
                    population: $row[5],
                    square: $row[6],
                );
                array_push($countries, $country);
            }
            // вернуть результат
            return $countries;
        } finally {
            // в конце в любом случае закрыть соединение с БД если оно было открыто
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function get(string $code): ?Country {
        try {
            // создать подключение к БД
            $connection = $this->sqlHelper->openDbConnection();
            // подготовить строку запроса
            $queryStr = 'SELECT name_f, name_s, iso_alpha2, iso_alpha3, iso_numeric, popul, square 
                     FROM country_t
                     WHERE iso_alpha2 = ? 
                     OR iso_alpha3 = ? 
                     OR iso_numeric = ?';
            // подготовить запрос
            $query = $connection->prepare(query: $queryStr);
            // Проверка на ошибки при подготовке
            if ($query === false) {
                throw new Exception($connection->error);
            }

            $query->bind_param("sss", $code, $code, $code);
            // выполнить запрос
            $query->execute();
            $rows = $query->get_result();
            $row = $rows->fetch_assoc();
            // считать результаты запроса в цикле 
            while ($row) {
                // если есть результат - вернем его
                return new Country(
                    fullName: $row['name_f'],  // используем ключи по имени
                    shortName: $row['name_s'],
                    isoAlpha2: $row['iso_alpha2'],
                    isoAlpha3: $row['iso_alpha3'],
                    isoNumeric: $row['iso_numeric'],
                    population: $row['popul'],
                    square: $row['square']
                );
            }
            // иначе вернуть null
            return null;
        } finally {
            // в конце в любом случае закрыть соединение с БД если оно было открыто
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function store(Country $country): void {
        try {
            // создать подключеник к БД
            $connection = $this->sqlHelper->openDbConnection();
            // подготовить запрос INSERT
            $queryStr = 'INSERT INTO country_t (name_f, name_s, iso_alpha2, iso_alpha3, iso_numeric, popul, square)
                VALUES (?, ?, ?, ?, ?, ?, ?);';
            // подготовить запрос
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param(
                'sssssii', 
                $country->fullName, 
                $country->shortName,
                 $country->isoAlpha2, 
                 $country->isoAlpha3, 
                 $country->isoNumeric,
                 $country->population,
                 $country->square,
            );
            // выполнить запрос
            if (!$query->execute()) {
                throw new Exception(message: 'Добавление провалилось.');
            }
        } finally {
            // в конце в любом случае закрыть соединение с БД если оно было открыто
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function delete(string $code):void {
        try {
            // создать подключение к БД
            $connection = $this->sqlHelper->openDbConnection();
            // подготовить запрос DELETE
            $queryStr = 'DELETE FROM country_t 
            WHERE iso_alpha2 = ? 
            OR iso_alpha3 = ? 
            OR iso_numeric = ?';
            // подготовить запрос
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param('sss', $code, $code, $code);
            // выполнить запрос
            if (!$query->execute()) {
                throw new Exception(message: 'Удаление провалилось.');
            }
        } finally {
            // в конце в любом случае закрыть соединение с БД если оно было открыто
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    function edit(string $code, Country $country) : void {
        try {
            // создать подключение к БД
            $connection = $this->sqlHelper->openDbConnection();
            // подготовить запрос UPDATE
            $queryStr = 'UPDATE country_t SET 
                    name_f = ?,
                    name_s = ?,
                    iso_alpha2 = ?,
                    iso_alpha3 = ?,
                    iso_numeric = ?,
                    popul = ?,
                    square = ?
                WHERE iso_alpha2 = ? 
                OR iso_alpha3 = ? 
                OR iso_numeric = ?';
            // подготовить запрос
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param(
                'sssssiisss', 
                $country->fullName, 
                $country->shortName,
                $country->isoAlpha2, 
                $country->isoAlpha3, 
                $country->isoNumeric,
                $country->population,
                $country->square,
                $code,
                $code,
                $code
            );
            // выполнить запрос
            if (!$query->execute()) {
                throw new Exception(message: 'Обновление провалилось.');
            }
        } finally {
            // в конце в любом случае закрыть соединение с БД если оно было открыто
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

}