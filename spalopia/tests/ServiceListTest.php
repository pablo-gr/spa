<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ServiceListTest extends WebTestCase
{
    /**
     * Tests the unlimited list and translation service
     *
     * @return void
     */
    public function testListSuccess(): void
    {
        $translatedService = [
            'Back massage', 'Led Facial', 'Oxygen Facial', 'Anti-Aging Facial', 'Beer Bath Hydrotherapy', 'Body Mud Mask',
            'Infrared Sauna', 'Zero Gravity Massage', 'Hot Stone Massage', 'Aromatherapy Massage',
        ];

        $client = static::createClient();
        $client->request('GET', '/api/service', ['orders' => ['id' => 'ASC']]);

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(10, $content);
        foreach ($content as $index => $service) {
            $this->assertEquals($translatedService[$index], $service['name'], 'The translation service dont works');
        }
    }

    /**
     * Tests the limit, ordered and paginated list
     *
     * @return void
     */
    public function testPaginationListSuccess(): void {
        $client = static::createClient();
        $client->request('GET', '/api/service', [
            'limit' => 2,
            'page' => 2,
            'orders' => ['price' => 'ASC'],
        ]);

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(2, $content, 'The limit dont works');
        $this->assertEquals(3, $content[0]['id'], 'The orders by price dont works');
        $this->assertEquals('Oxygen Facial', $content[0]['name'], 'The translation service dont works');
        $this->assertEquals(5.77, $content[0]['price'], 'The orders by price dont works');
        $this->assertEquals(6, $content[1]['id'], 'The orders by price dont works');
        $this->assertEquals('Body Mud Mask', $content[1]['name'], 'The translation service dont works');
        $this->assertEquals(8.66, $content[1]['price'], 'The orders by price dont works');
    }
}
