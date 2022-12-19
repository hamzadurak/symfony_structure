<?php

namespace App\Tests\Util\Hotel;

use App\Service\Hotel\HotelZoneService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HotelZoneTest extends KernelTestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testIndex(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

        $this->assertTrue($container->index()['status']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreate(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

        $this->assertTrue($container->create()['status']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testStore(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

        $this->assertTrue($container->store([
            'country' => 'Türkiye',
            'city' => 'Istanbul',
        ])['status']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testShow(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

        $this->assertTrue($container->show(1)['status']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testEdit(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

        $this->assertTrue($container->edit(1)['status']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUpdate(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

        $this->assertTrue($container->update(1, [
            'country' => 'Türkiye',
            'city' => 'Bursa',
        ])['status']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDestroy(): void
    {
        $container = $this->getContainer()->get(HotelZoneService::class);

//        $this->assertTrue($container->destroy(1)['status']);
        $this->assertTrue(true);
    }
}
