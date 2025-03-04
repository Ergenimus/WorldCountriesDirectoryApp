<?php

namespace App\Model;

use App\Model\Exceptions\NotFoundCountryException;
use App\Model\Exceptions\InvalidCountryCodeException;
use App\Model\Exceptions\DuplicateCountryException;
use App\Model\CountryRepository;

// CountryScenarios - класс с методами работы с объектами (странами)
class CountryScenarios {

    public function __construct(
        private readonly CountryRepository $storage
    ) {

    }

    // getAll - получение всех стран
    // вход: -
    // выход: список объектов Country
    public function GetAll(): array {
        return $this->storage->getAll();
    }

    //get - получение списка страны по коду
    // вход: код страны (любой)
    // выход: объект страна, у которого хранится такой же код
    public function Get(string $code): Country {
        if (!$this->validateCode($code)) {
            throw new InvalidCountryCodeException(
                invalidCode: $code, 
                message: 'Валидация провалилась.'
            );
        }
        // если валидация пройдена, то получить страну по данному коду
        $country = $this->storage->Get($code);
        if ($country === null) {
            // если страна не найдена - выбросить ошибку
            throw new NotFoundCountryException($code);
        }
        //  вернуть страну
        return $country;
    }

    //store - сохранение новой страны
    //вход: объект страны
    //выход: -
    //Exceptions: Duplicate, InvalidCode
    public function Store(Country $country): void {
        // Вспомогательная функция для проверки кодов
        $this->checkCountryCodes($country);
    
        // Вспомогательная функция для проверки заполненности и отрицательных значений
        $this->checkCountryDetails($country);
    
        // Проверка уникальности названий 
        $this->checkUniqueCountry($country);

        // если все ок, то сохранить объект страны в БД
        $this->storage->store(country: $country);
    }

    //edit - обновление (редактирование) объекта страны
    //вход: код редактируемой страны (не обновлённый)
    //выход: -
    public function Edit(string $code, Country $country): void {
        // Проверяем, существует ли страна с указанным кодом
        $updatedCountry = $this->storage->get($code);
        if ($updatedCountry === null) {
            throw new NotFoundCountryException(
                notFoundCode: $code
            );
        }
        //проверяем валидацию
        $this->checkCountryCodes($country);

        //проверяем уникальность (чтобы не дублировать страну)
        $this->checkUniqueCountry($country);

        //если всё ок - редактируем
        $this->storage->edit(code: $code, country: $country);
    }

    //delete - удаление объекта страны
    //вход: код страны
    //выход: -
    //Exceptions: InvalidCode, NotFound
    public function Delete(string $code): void {
        // выполнить проверку корректности кода
        if (!$this->validateCode(code: $code)) {
            throw new InvalidCountryCodeException(
                invalidCode: $code, 
                message: 'Валидация провалилась'
            );
        }
        // если валидация пройдена, то получить страну по данному коду
        $country = $this->storage->get(code: $code);
        if ($country === null) {
            // если страна не найдена - выбросить ошибку
            throw new NotFoundCountryException(notFoundCode: $code);
        }
        $this->storage->delete(code: $code);
    }

    // Метод валидации, который проверяет формат кода 
    //(iso-alpha3, iso-alpha2, iso-numeric)
    private function validateCode(string $code): bool {
        // Проверка, что код соответствует одному из форматов: 
        // 1. ISO alpha (2 или 3 буквы)
        // 2. ISO numeric (1 до 3 цифр)
        if (preg_match('/^[A-Za-z]{2,3}$/', $code)) {
            return true;
        } else if (preg_match('/^[0-9]{2,3}$/', $code)) {
            return true;
        } else return false; // Код не соответствует ни одному из форматов
    }

    private function checkCountryCodes(Country $country): void {
        foreach ([$country->isoAlpha2, $country->isoAlpha3, $country->isoNumeric] as $code) {
            if (!$this->validateCode(code: $code)) {
                throw new InvalidCountryCodeException(
                    invalidCode: $code,
                    message: '- код страны не соответствует требованиям валидации.',
                );
            }
        }
    }

    private function checkCountryDetails(Country $country): void {
        if (strlen(trim($country->fullName)) === 0) {
            throw new InvalidCountryCodeException(
                invalidCode: $country->fullName,
                message: 'Полное название страны не может быть пустым.'
            );
        }
        if (strlen(trim($country->shortName)) === 0) {
            throw new InvalidCountryCodeException(
                invalidCode: $country->shortName,
                message: 'Короткое название страны не может быть пустым.'
            );
        }
        if ($country->population < 0 || $country->square < 0) {
            throw new InvalidCountryCodeException(
                invalidCode: $country->shortName,
                message: 'Численность или площадь страны не может быть отрицательной.'
            );
        }
    }

    private function checkUniqueCountry(Country $country): void {
        // Получаем все существующие страны
        $existingCountries = $this->storage->getAll(); 

        foreach ($existingCountries as $existingCountry) {
            if ($existingCountry->shortName === $country->shortName) {
                throw new DuplicateCountryException(
                    duplicatedCode: $country->shortName
                );
            }
            
            if ($existingCountry->fullName === $country->fullName) {
                throw new DuplicateCountryException(
                    duplicatedCode: $country->fullName
                );
            }

            if ($existingCountry->isoAlpha2 === $country->isoAlpha2) {
                throw new DuplicateCountryException(
                    duplicatedCode: $country->isoAlpha2
                );
            }

            if ($existingCountry->isoAlpha3 === $country->isoAlpha3) {
                throw new DuplicateCountryException(
                    duplicatedCode: $country->isoAlpha3
                );
            }

            if ($existingCountry->isoNumeric === $country->isoNumeric) {
                throw new DuplicateCountryException(
                    duplicatedCode: $country->isoNumeric
                );
            }
        }
    }

}
?>