<?php

namespace App\Interface\Hotel;

use App\Entity\Hotel;

interface HotelInterface
{
    /**
     * @param Hotel $entity
     * @param bool $flush
     * @return void
     */
    public function storeUpdate(Hotel $entity, bool $flush = false): void;

    /**
     * @param Hotel $entity
     * @param bool $flush
     * @return void
     */
    public function destroy(Hotel $entity, bool $flush = false): void;
}