<?php

namespace MKD\StateManagement\Casts;

class FloatCast implements StateCastAttribute
{


    public function get(string $key, mixed $value)
    {
        return (float) $value;
    }

    public function set(string $key, mixed $value)
    {
        return (float) $value;

    }
}
