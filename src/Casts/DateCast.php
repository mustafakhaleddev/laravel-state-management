<?php

namespace MKD\StateManagement\Casts;

class DateCast extends DateTimeCast
{


    public function get(string $key, mixed $value)
    {
      return $this->asDateTime($value)->startOfDay();
    }

    public function set(string $key, mixed $value)
    {
        $this->asDateTime($value)->startOfDay();

    }
}
