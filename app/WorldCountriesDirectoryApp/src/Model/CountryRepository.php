<?php

namespace App\Model;

// CountryRepository - интерфейс хранилища стран
interface CountryRepository {
    
    // GetAll - получение всех стран
    function getAll(): array;

    // Get - получить страну по коду
    function get(string $code): ?Country;

    // Store - сохранение страны в БД
    function store(Country $country): void;

    // Delete - удаление страны по коду
    function delete(string $code) : void;

    // Edit - обновление данных страны по коду
    function edit(string $code, Country $country) : void;
}