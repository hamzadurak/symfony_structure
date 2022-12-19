<?php

namespace App\Interface\Hotel;

use App\Entity\HotelZone;

interface HotelZoneInterface
{
    /**
     * @param HotelZone $entity
     * @param bool $flush
     * @return void
     */
    public function storeUpdate(HotelZone $entity, bool $flush = false): void;

    /**
     * @param HotelZone $entity
     * @param bool $flush
     * @return void
     */
    public function destroy(HotelZone $entity, bool $flush = false): void;

    /**
     * @param $requestAll
     * @return mixed
     */
    public function getHotels($requestAll): mixed;
}