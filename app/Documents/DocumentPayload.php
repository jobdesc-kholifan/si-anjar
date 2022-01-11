<?php

namespace App\Documents;

class DocumentPayload
{
    protected $payload;

    public function __construct($payload = "")
    {
        $this->payload = json_decode($payload);
    }

    public function has($index)
    {
        return !empty($this->payload->$index);
    }

    public function get($index, $default = null)
    {
        return $this->has($index) ? $this->payload->$index : $default;
    }
}
