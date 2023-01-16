<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ServiceAvailableHoursListTest extends WebTestCase
{
    /**
     * Test the available hours for first service
     *
     * @return void
     */
    public function testAvailableHoursSuccess(): void {
        $expectedAvailableDates = [
            ['start_at' => '2023-01-10 09:00', 'end_at' => '2023-01-10 10:00'],
            ['start_at' => '2023-01-10 10:00', 'end_at' => '2023-01-10 11:00'],
            ['start_at' => '2023-01-10 11:00', 'end_at' => '2023-01-10 12:00'],
            /** keep 1 hour for launch! */
            ['start_at' => '2023-01-10 13:00', 'end_at' => '2023-01-10 14:00'],
            ['start_at' => '2023-01-10 14:00', 'end_at' => '2023-01-10 15:00'],
        ];

        $client = static::createClient();
        $client->request('GET', '/api/service/1/available-hours/2023-01-10');

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(count($expectedAvailableDates), $content);
        foreach ($content as $index => $scheduler) {
            $this->assertArrayHasKey('start_at', $scheduler);
            $this->assertArrayHasKey('date', $scheduler['start_at']);
            $this->assertArrayHasKey('end_at', $scheduler);
            $this->assertArrayHasKey('date', $scheduler['end_at']);
            $this->assertTrue(str_contains($scheduler['start_at']['date'], $expectedAvailableDates[$index]['start_at']));
            $this->assertTrue(str_contains($scheduler['end_at']['date'], $expectedAvailableDates[$index]['end_at']));
        }
    }

    /**
     * Test the available hours for second service: it has a booking as reservation in range of 10:00am to 11:00am
     *
     * @return void
     */
    public function testAvailableHoursWithBookingSuccess(): void {
        $expectedAvailableDates = [
            ['start_at' => '2023-01-10 09:00', 'end_at' => '2023-01-10 10:00'],
            ['start_at' => '2023-01-10 11:00', 'end_at' => '2023-01-10 12:00'],
            /** keep 1 hour for launch! */
            ['start_at' => '2023-01-10 13:00', 'end_at' => '2023-01-10 14:00'],
            ['start_at' => '2023-01-10 14:00', 'end_at' => '2023-01-10 15:00'],
        ];

        $client = static::createClient();
        $client->request('GET', '/api/service/2/available-hours/2023-01-10');

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(count($expectedAvailableDates), $content);
        foreach ($content as $index => $scheduler) {
            $this->assertArrayHasKey('start_at', $scheduler);
            $this->assertArrayHasKey('date', $scheduler['start_at']);
            $this->assertArrayHasKey('end_at', $scheduler);
            $this->assertArrayHasKey('date', $scheduler['end_at']);
            $this->assertTrue(str_contains($scheduler['start_at']['date'], $expectedAvailableDates[$index]['start_at']));
            $this->assertTrue(str_contains($scheduler['end_at']['date'], $expectedAvailableDates[$index]['end_at']));
        }
    }

    /**
     * Test the expected exception when the service not found
     *
     * @return void
     */
    public function testAvailableHoursServiceNotFound(): void {
        $client = static::createClient();
        $client->request('GET', '/api/service/99/available-hours/2023-01-10');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Service not found', $content['error']);
    }

    /**
     * Test the expected exception when the service do not have a scheduler for queried date
     *
     * @return void
     */
    public function testAvailableHoursSchedulerNotFound(): void {
        $client = static::createClient();
        $client->request('GET', '/api/service/1/available-hours/2022-12-31');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('The service is not scheduled for this date', $content['error']);
    }
}
