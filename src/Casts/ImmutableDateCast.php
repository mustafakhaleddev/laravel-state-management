<?php

namespace MKD\StateManagement\Casts;

class ImmutableDateCast extends DateTimeCast
{


    public function get(string $key, mixed $value)
    {
      return $this->asDateTime($value)->toImmutable();
    }

    public function set(string $key, mixed $value)
    {
        $this->asDateTime($value)->toImmutable();

    }
}
