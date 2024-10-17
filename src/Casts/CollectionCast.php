<?php

namespace MKD\StateManagement\Casts;

use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Collection as BaseCollection;

class CollectionCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        return new BaseCollection($this->fromJsonData($value));
    }

    public function set(string $key, mixed $value)
    {
        return new BaseCollection($this->fromJsonData($value));

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

    private function fromJsonData($value, $asObject = false)
    {
        if (is_array($value)) {
            return $this->fromJson(json_encode($value), $asObject);
        }
        return $this->fromJson($value, $asObject);
    }
}
