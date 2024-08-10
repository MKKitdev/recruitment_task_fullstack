<?php

namespace App\Service\NBP\ExchangeRate\DTO;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use App\Exception\NBPException;
use App\Service\NBP\ExchangeRate\DTO\Factories\FactoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class FactoryService implements FactoryServiceInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var iterable<FactoryInterface>
     */
    private $factories;

    public function __construct(
        ParameterBagInterface $parameterBag,
        iterable              $factories
    )
    {
        $this->parameterBag = $parameterBag;
        $this->factories = $factories;
    }

    /** {@inheritDoc} */
    public function createExchangeRatesDTO(
        array      $responseData,
        RequestDTO $requestDTO
    ): DTO
    {
        if (!isset($responseData[0]['rates'])) {
            throw new NBPException(
                'Bad response data from NBP API',
                Response::HTTP_NOT_FOUND
            );
        }

        $config = $this->parameterBag->get('nbp')['exchangeRates'];

        $exchangeRatesDTO = (new DTO())
            ->setSupportedCurrenciesConfig($config['supportedCurrencies'])
            ->setBuyableCurrenciesConfig($config['buyableCurrencies'])
            ->setDate($requestDTO->getDate());

        foreach ($responseData[0]['rates'] as $rateData) {
            foreach ($this->factories as $factory) {
                if (!$factory->isSupported($rateData, $exchangeRatesDTO)) {
                    continue;
                }

                $factory->appendCurrencyDTOToDTO($rateData, $exchangeRatesDTO);
            }
        }

        return $exchangeRatesDTO;
    }
}