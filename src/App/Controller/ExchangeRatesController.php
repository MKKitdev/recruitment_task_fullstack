<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\NBP\ExchangeRates\RequestDTO;
use App\Exception\NBPException;
use App\Service\NBP\ExchangeRate\GetServiceInterface;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExchangeRatesController extends AbstractController
{
    /** @var GetServiceInterface */
    private $getService;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        GetServiceInterface   $getService,
        ParameterBagInterface $parameterBag,
        LoggerInterface $logger
    )
    {
        $this->getService = $getService;
        $this->parameterBag = $parameterBag;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function index(Request $request): Response
    {
        try {
            $exchangeRatesDTO = $this->getService
                ->getExchangeRateDTOByRequestDTO(
                    $this->prepareRequestDTO($request)
                );

            return new Response(
                json_encode($exchangeRatesDTO),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
        } catch (NBPException $exception) {
            return new Response(
                json_encode(['message' => $exception->getMessage()]),
                $exception->getCode(),
                ['Content-type' => 'application/json']
            );
        } catch (Throwable $exception) {
            $this->logger->error(
                'Unknown error exchange rates',
                [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );

            return new Response(
                json_encode(['message' => 'Unknown error']),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-type' => 'application/json']
            );
        }
    }

    /**
     * @param Request $request
     *
     * @return RequestDTO
     *
     * @throws NBPException
     */
    private function prepareRequestDTO(Request $request): RequestDTO
    {
        return (new RequestDTO())
            ->setDate($this->getDateQueryParam($request));
    }

    /**
     * @param Request $request
     *
     * @return DateTime
     *
     * @throws NBPException
     */
    private function getDateQueryParam(Request $request): DateTime
    {
        $date = $request->query->get('date');

        if (!$date) {
            return new DateTime();
        }

        $date = DateTime::createFromFormat(RequestDTO::DATE_FORMAT, $date);

        if (!$date) {
            throw new NBPException(
                'Invalid date format',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($date > (new DateTime())) {
            throw new NBPException(
                'The date cannot be later than today',
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $configDateFrom = DateTime::createFromFormat(
            RequestDTO::DATE_FORMAT,
            $this->parameterBag->get('nbp')['exchangeRates']['fromDate']
        );

        if ($date < $configDateFrom ) {
            throw new NBPException(
                sprintf(
                    'The date cannot be earlier than %s',
                    $configDateFrom->format(RequestDTO::DATE_FORMAT)
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $date;
    }
}
