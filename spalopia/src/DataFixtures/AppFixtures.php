<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Scheduler;
use App\Entity\Service;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class AppFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->createServices($manager);

        $manager->flush();
    }

    /**
     * @throws Exception
     */
    protected function createServices(ObjectManager $manager): void {
        $services = [
            ['name' => 'Masaje de espalda', 'price' => 9.99],
            ['name' => 'Led facial', 'price' => 11.33],
            ['name' => 'Facial de oxígeno', 'price' => 5.77],
            ['name' => 'Facial Antienvejecimiento', 'price' => 3.55],
            ['name' => 'Hidroterapia de baño de cerveza', 'price' => 15.29],
            ['name' => 'Máscara de barro corporal', 'price' => 8.66],
            ['name' => 'Sauna de infrarrojos', 'price' => 12.49],
            ['name' => 'Masaje gravedad cero', 'price' => 9.33],
            ['name' => 'Masaje con piedras calientes', 'price' => 4.99],
            ['name' => 'Masaje de aromaterapia', 'price' => 9.99],
        ];

        foreach ($services as $data) {
            $service = new Service();
            $service->setName($data['name']);
            $service->setPrice($data['price']);
            $manager->persist($service);

            $this->createSchedulers($manager, $service);
        }
    }

    /**
     * @throws Exception
     */
    protected function createSchedulers(ObjectManager $manager, Service $service): void {
        for ($i = 2; $i <= 31; $i++) {
            $startAt = (new DateTime('2023-01-' . ($i)))->setTime(9, 0);
            $endAt = (clone $startAt)->setTime(12, 0);
            $this->createScheduler($manager, $service, $startAt, $endAt);

            if ($service->getName() === 'Led facial') {
                $this->createBooking($manager, $service, (clone $startAt)->setTime(10, 30));
            }

            /** keep 1 hour for launch! */
            $startAt = (new DateTime('2023-01-' . ($i)))->setTime(13, 0);
            $endAt = (clone $startAt)->setTime(15, 0);
            $this->createScheduler($manager, $service, $startAt, $endAt);
        }
    }

    protected function createScheduler(ObjectManager $manager, Service $service, DateTime $startAt, DateTime $endAt) {
        $scheduler = new Scheduler();
        $scheduler->setService($service);
        $scheduler->setStartAt($startAt);
        $scheduler->setEndAt($endAt);
        $manager->persist($scheduler);
    }

    protected function createBooking(ObjectManager $manager, Service $service, DateTime $date): void {
        $booking = new Booking();
        $booking->setService($service);
        $booking->setDate($date);
        $booking->setClientName('John Doe');
        $booking->setClientEmail('john-doe@testemail.com');
        $booking->setPrice($service->getPrice());
        $manager->persist($booking);
    }
}
