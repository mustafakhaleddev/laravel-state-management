<?php

namespace MKD\StateManagement\Casts;


interface StateCastAttribute
{

    /**
     * Transform the attribute from the underlying  values.
     *
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return TGet|null
     */
    public function get(string $key, mixed $value);

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param string $key
     * @param TSet|null $value
     * @param array<string, mixed> $attributes
     * @return mixed
     */
    public function set(string $key, mixed $value);

}
