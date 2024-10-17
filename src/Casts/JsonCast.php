<?php

namespace MKD\StateManagement\Casts;

use Illuminate\Database\Eloquent\Casts\Json;

class JsonCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        return $this->fromJson($value);
    }

    public function set(string $key, mixed $value)
    {
        return $this->fromJson($value);

    }

    /**
     * Decode the given JSON back into an array or object.
     *
     * @param string|null $value
     * @param bool $asObject
     * @return mixed
     */
    private function fromJson($value, $asObject = false)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Json::decode($value, !$asObject);
    }
}
