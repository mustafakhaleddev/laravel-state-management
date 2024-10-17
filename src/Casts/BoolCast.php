<?php

namespace MKD\StateManagement\Casts;

class BoolCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        return (bool) $value;
    }

    public function set(string $key, mixed $value)
    {
        return (bool) $value;

    }
}
