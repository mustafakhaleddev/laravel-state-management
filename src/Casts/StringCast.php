<?php

namespace MKD\StateManagement\Casts;

class StringCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        return (string) $value;
    }

    public function set(string $key, mixed $value)
    {
        return (string) $value;

    }
}
