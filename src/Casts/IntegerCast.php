<?php

namespace MKD\StateManagement\Casts;

class IntegerCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        return (int) $value;
    }

    public function set(string $key, mixed $value)
    {
        return (int) $value;

    }
}
