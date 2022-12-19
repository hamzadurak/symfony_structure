<?php

namespace App\Service\General;

use Doctrine\ORM\Mapping\Entity;
use Predis\Client;

class PredisService
{
    /**
     * @return Client
     */
    public function predisConnect(): Client
    {
        return new Client('%env(PREDIS_CONNECTION)%');
    }

    /**
     * @param $predis
     * @return false|mixed
     */
    public function get($predis): mixed
    {
        if ($this->predisConnect()->get($predis)) {
            return unserialize(base64_decode($this->predisConnect()->get($predis)));
        }

        return false;
    }

    /**
     * @param $predis
     * @param $data
     * @return void
     */
    public function set($predis, $data): void
    {
        if (count($data) > 0) {
            $this->predisConnect()->set(
                $predis, base64_encode(serialize($data))
            );
        }
    }

    /**
     * @param string $predis
     * @param int $id
     * @return false|mixed
     */
    public function getById(string $predis, int $id): mixed
    {
        if ($data = $this->get($predis)) {
            foreach ($data as $d) {
                if ($d->getId() === $id) {
                    return $d;
                }
            }
        }

        return false;
    }
}