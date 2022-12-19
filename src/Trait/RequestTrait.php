<?php

namespace App\Trait;

trait RequestTrait
{
    /**
     * @param $request
     * @return mixed
     */
    public function request($request): mixed
    {
        $requestAll = $request->query->all();
        if ($content = $request->getContent()) {
            $requestAll = json_decode($content, true);
        }

        return $requestAll;
    }
}