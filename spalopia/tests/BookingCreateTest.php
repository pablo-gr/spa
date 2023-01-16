<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingCreateTest extends WebTestCase
{
    /**
     * Test the booking creation and verify that it is not available for other reservation in same date
     *
     * @return void
     */
    public function testCreateBookingSuccess(): void {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 1,
            'date' => '2023-01-10 10:20:00',
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('id', $content);
        $this->assertNotNull($content['id']);

        /**
         * retry booking create to verify that it already exists
         */
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 1,
            'date' => '2023-01-10 10:40:00',
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Booking date already exists for this service', $content['error']);
    }

    /**
     * Test the expected exception when the service not found
     *
     * @return void
     */
    public function testCreateBookingServiceNotFound(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 11,
            'date' => '2023-01-10 10:30:00',
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Service not found', $content['error']);
    }

    /**
     * Test the expected exception when the service do not have a scheduler for param date
     *
     * @return void
     */
    public function testCreateBookingSchedulerNotFound(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 1,
            'date' => '2022-12-31 10:30:00',
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('The service is not scheduled for this date', $content['error']);
    }

    /**
     * Test expected exception when the id param is missing
     *
     * @return void
     */
    public function testCreateBookingMissingServiceParam(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'date' => '2022-12-31 10:30:00',
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('The service id, booking date client name and client email are required', $content['error']);
    }

    /**
     * Test expected exception when the booking date param is missing
     *
     * @return void
     */
    public function testCreateBookingMissingDateParam(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 1,
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('The service id, booking date client name and client email are required', $content['error']);
    }

    /**
     * Test expected exception when the client name param is missing
     *
     * @return void
     */
    public function testCreateBookingMissingClientNameParam(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 1,
            'date' => '2022-12-31 10:30:00',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('The service id, booking date client name and client email are required', $content['error']);
    }

    /**
     * Test expected exception when the client email param is missing
     *
     * @return void
     */
    public function testCreateBookingMissingClientEmailParam(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 1,
            'date' => '2022-12-31 10:30:00',
            'client-name' => 'John Doe',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('The service id, booking date client name and client email are required', $content['error']);
    }

    /**
     * Test the expected exception when the booking date for queried service already exists
     *
     * @return void
     */
    public function testCreateBookingAlreadyExists(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/booking', [
            'id' => 2,
            'date' => '2023-01-10 10:20:00',
            'client-name' => 'John Doe',
            'client-email' => 'john.doe@testemail.com',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Booking date already exists for this service', $content['error']);
    }
}
