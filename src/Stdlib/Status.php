<?php

namespace Omekalia\Stdlib;

class Status
{
    protected $omekaliaData;

    public function __construct(array $omekaliaData)
    {
        $this->omekaliaData = $omekaliaData;
    }

    public function getOmekaliaData()
    {
        return $this->omekaliaData;
    }
}
