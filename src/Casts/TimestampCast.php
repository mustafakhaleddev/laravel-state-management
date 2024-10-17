<?php

namespace MKD\StateManagement\Casts;

class TimestampCast extends DateTimeCast
{


    public function get(string $key, mixed $value)
    {
      return $this->asDateTime($value)->getTimestamp();
    }

    public function set(string $key, mixed $value)
    {
        $this->asDateTime($value)->getTimestamp();

    }
}
