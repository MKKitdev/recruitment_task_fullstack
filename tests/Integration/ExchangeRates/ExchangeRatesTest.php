<?php

namespace Integration\ExchangeRates;

use DateTime;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRatesTest extends WebTestCase
{
    private const FILEPATH_EXAMPLE_NBP_API_RESPONSE_SUCCESS = '/NBP/ExchangeRates/mock_nbp_api_response_success.json';
    private const FILEPATH_EXAMPLE_ENDPOINT_RESPONSE_SUCCESS = '/NBP/ExchangeRates/mock_endpoint_response_success.json';
    private const API_RESOuRCE_ENDPOINT = '/api/exchange-rates';

    /** @var FilesystemAdapter */
    private $cache;

    /** @var MockObject|HttpClientInterface */
    private $httpClientMock;

    /** @var MockObject|ResponseInterface */
    private $responseMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->responseMock = $this->createMock(ResponseInterface::class);

        $this->cache = new FilesystemAdapter();
    }

    public function testGetWhenDateIsNotSetAndIsErrorResponse(): void
    {
        $client = static::createClient();

        $this->cache->clear();

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new Exception('Test exception', Response::HTTP_INTERNAL_SERVER_ERROR));

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"Unknown error"}', $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetWhenDateIsSetAndIsErrorResponse(): void
    {
        $client = static::createClient();

        $this->cache->clear();

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new Exception('Test exception', Response::HTTP_INTERNAL_SERVER_ERROR));

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $client->request(
            Request::METHOD_GET,
            '/api/exchange-rates',
            ['date' => '2024-08-08']
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"Unknown error"}', $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetWhenDateIsSetAndDateIsToEarly(): void
    {
        $client = static::createClient();

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => '2000-08-08']
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"The date cannot be earlier than 2023-01-01"}', $response->getContent());
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testGetWhenDateIsSetAndDateIsLaterThanCurrent(): void
    {
        $dateNow = new DateTime('now +1 day');

        $client = static::createClient();

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => $dateNow->format('Y-m-d')]
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"The date cannot be later than today"}', $response->getContent());
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testGetWhenDateIsSetAndDateIsInWrongFormat(): void
    {
        $client = static::createClient();

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => '01-01-2024']
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"Invalid date format"}', $response->getContent());
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testGetWhenNotFoundDataInNBPApi(): void
    {
        $client = static::createClient();

        $this->responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_NOT_FOUND);

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->responseMock);

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => '2024-08-09',]
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"No NBP data found for 2024-08-09"}', $response->getContent());
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetWhenResponseDataFromNBPApiIsWrong(): void
    {
        $client = static::createClient();

        $this->responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->responseMock
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('{"wrong": true, "is": "this"}');

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->responseMock);

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => '2024-08-09',]
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame('{"message":"Bad response data from NBP API"}', $response->getContent());
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetWhenDateIsNotSetAndIsSuccessResponse(): void
    {
        $client = static::createClient();

        $this->responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->responseMock
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(
                $this->getTestFileContent(self::FILEPATH_EXAMPLE_NBP_API_RESPONSE_SUCCESS)
            );

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->responseMock);

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => '2024-08-08',]
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame(
            $this->getTestFileContent(self::FILEPATH_EXAMPLE_ENDPOINT_RESPONSE_SUCCESS),
            $response->getContent()
        );
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetWhenDateIsSetAndIsSuccessResponse(): void
    {
        $client = static::createClient();

        $this->responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->responseMock
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(
                $this->getTestFileContent(self::FILEPATH_EXAMPLE_NBP_API_RESPONSE_SUCCESS)
            );

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->responseMock);

        $container = static::$container;
        $container->set(HttpClientInterface::class, $this->httpClientMock);

        $this->cache->clear();

        $client->request(
            Request::METHOD_GET,
            self::API_RESOuRCE_ENDPOINT,
            ['date' => '2024-08-08']
        );

        $response = $client->getResponse();

        $this->assertJson($response->getContent());
        $this->assertSame(
            $this->getTestFileContent(self::FILEPATH_EXAMPLE_ENDPOINT_RESPONSE_SUCCESS),
            $response->getContent()
        );
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    protected function getTestFileContent(string $filepath): string
    {
        return file_get_contents(
            static::$kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'tests/File' . $filepath
        );
    }
}