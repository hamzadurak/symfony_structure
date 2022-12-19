<?php

namespace App\Interface\Hotel;

use App\Entity\HotelCategory;

interface HotelCategoryInterface
{
    /**
     * @param HotelCategory $entity
     * @param bool $flush
     * @return void
     */
    public function storeUpdate(HotelCategory $entity, bool $flush = false): void;

    /**
     * @param HotelCategory $entity
     * @param bool $flush
     * @return void
     */
    public function destroy(HotelCategory $entity, bool $flush = false): void;
}