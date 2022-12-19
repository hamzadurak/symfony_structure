<?php

namespace App\Interface\Page;

use App\Entity\Page;

interface PageInterface
{
    /**
     * @param Page $entity
     * @param bool $flush
     * @return void
     */
    public function storeUpdate(Page $entity, bool $flush = false): void;

    /**
     * @param Page $entity
     * @param bool $flush
     * @return void
     */
    public function destroy(Page $entity, bool $flush = false): void;
}