<?php

namespace MKD\StateManagement\Casts;

use Illuminate\Database\Eloquent\Casts\Json;

class ObjectCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        if (is_array($value)) {
            return $this->fromJson(json_encode($value), true);
        }
        return $this->fromJson($value, true);
    }

    public function set(string $key, mixed $value)
    {
        if (is_array($value)) {
            return $this->fromJson(json_encode($value), true);
        }
        return $this->fromJson($value, true);
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
