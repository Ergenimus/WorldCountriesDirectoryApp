<?php

namespace App\Controller;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

use App\Model\Country;
use App\Model\CountryScenarios;
use App\Model\Exceptions\InvalidCountryCodeException;
use App\Model\Exceptions\NotFoundCountryException;
use App\Model\Exceptions\DuplicateCountryException;

#[Route(path: 'api/country', name: 'app_api_country')]
final class CountryController extends AbstractController
{
    public function __construct(
        private readonly CountryScenarios $countries
    ) {

    }

    // получение всех стран
    #[Route(path: '', name: 'app_api_country_root', methods: ['GET'])]
    public function GetAll(Request $request): JsonResponse
    {
            $countries = $this->countries->getAll();
        $countriesPreview = [];
        foreach ($this->countries->getAll() as $country) {
            $countryPreview = $this->buildCountryPreview(country: $country, request: $request);
            array_push($countriesPreview, $countryPreview);
        }
        return $this->json(data: $countries, status: 200); 
    }

    // получение страны по коду
    #[Route(path:'/{code}', name:'app_api_country_code', methods: ['GET'])] 
    public function Get(string $code): JsonResponse {
        try {
            $country = $this->countries->get($code);
            return $this->json(data: $country);
        } catch (InvalidCountryCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (NotFoundCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        }
    }

    // добавление страны
    #[Route(path: '', name: 'app_api_country_add', methods: ['POST'])]
    public function Store(Request $request, #[MapRequestPayload] Country $country) : JsonResponse {
        try {
            $this->countries->store(country: $country);
            $countryPreview = $this->buildCountryPreview(country: $country, request: $request);
            return $this->json(data: $countryPreview, status: 200);
        } catch (InvalidCountryCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicateCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        }
    }

    // удаление страны
    #[Route(path: '/{code}', name: 'app_api_country_remove', methods: ['DELETE'])]
    public function Delete(string $code) : JsonResponse {
        try {
            $this->countries->delete($code);
            return $this->json(data: null, status: 204);
        } catch (InvalidCountryCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (NotFoundCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        }
    }

    // редактирование страны
    #[Route(path: '/{code}', name: 'app_api_country_edit', methods: ['PATCH'])]
    public function Edit(Request $request, string $code, #[MapRequestPayload] Country $country) : JsonResponse {
        try {
            $this->countries->edit(code: $code, country: $country);
            $countryPreview = $this->buildCountryPreview(country: $country, request: $request);
            return $this->json(data: $countryPreview, status: 200);
        } catch (InvalidCountryCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (NotFoundCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        } catch (DuplicateCountryException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        }
    }

    // вспомогательный метод формирования ошибки
    private function buildErrorResponse(Exception $ex): JsonResponse {
        return $this->json(data: [
            'errorCode' => $ex->getCode(),
            'errorMessage' => $ex->getMessage(),
        ]);
    } 

    private function buildCountryPreview(Country $country, Request $request) : CountryPreview {
        $uri = sprintf(
            '%s://%s/api/country/%s', 
            $request->getScheme(), 
            $request->getHttpHost(),
            $country->isoAlpha2
        );
        return new CountryPreview(
            name: $country->fullName, 
            uri: $uri,
        );

    }
}