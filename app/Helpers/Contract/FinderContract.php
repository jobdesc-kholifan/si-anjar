<?php


namespace App\Helpers\Contract;


interface FinderContract
{
    public function setFillable($array = []);

    public function setKey($key);

    public function in($code);
}
