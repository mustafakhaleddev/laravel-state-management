<?php

namespace MKD\StateManagement\traits;


use function Illuminate\Support\enum_value;

trait StoreAttributes
{

    protected $attributes = [];

    protected $casts = [];
    protected $enums = [];


    private function getAttribute($key)
    {
        if ($this->hasAttribute($key)) {
            if ($this->hasEnum($key)) {

                return $this->getEnumAttribute($key, $this->state[$key] ?? null);
            }

            if ($this->hasCast($key)) {

                return $this->castAttribute($key, $this->state[$key] ?? null);
            }
            return $this->state[$key] ?? null;
        }
        return null;
    }


    private function hasCast($key, $types = null)
    {


        return array_key_exists($key, $this->getCasts());
    }

    private function hasEnum($key, $types = null)
    {


        return array_key_exists($key, $this->getEnums());
    }


    private function hasAttribute($key)
    {
        if (!$key) {
            return false;
        }

        return in_array($key, $this->attributes) || array_key_exists($key, $this->attributes) ||
            array_key_exists($key, $this->casts);
    }


    /**
     * Set the value of a class castable attribute.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setClassCastableAttribute($key, $value)
    {
        $caster = $this->resolveCasterClass($key);

        $this->state[$key] = $caster->set(
            $key, $value, $this->attributes
        );

    }


    private function setAttribute($key, $value)
    {
        if ($this->hasEnum($key)) {
            return $this->setEnumAttribute($key, $value);
        }


        if ($this->isClassCastable($key)) {
            $this->setClassCastableAttribute($key, $value);

            return $this;
        }

        $this->state[$key] = $value;
    }


    private function getCasts()
    {
        return $this->casts;
    }

    private function getEnums()
    {
        return $this->enums;
    }


    protected function castAttribute($key, $value)
    {

        if ($this->isClassCastable($key)) {
            return $this->getClassCastableAttributeValue($key, $value);
        }

        return $value;
    }

    protected function getEnumAttribute($key, $value)
    {

        $enumClass = $this->getEnums()[$key] ?? null;
        if ($value && $enumClass && enum_exists($enumClass)) {
            return $this->getEnumCaseFromValue($enumClass, $value);
        }

        return $value;
    }

    protected function setEnumAttribute($key, $value)
    {
        $enumClass = $this->getEnums()[$key] ?? null;
        if ($enumClass && enum_exists($enumClass)) {

            if (!isset($value)) {
                $this->state[$key] = null;
            } elseif (is_object($value)) {
                $this->state[$key] = $this->getStorableEnumValue($enumClass, $value);
            } else {
                $this->state[$key] = $this->getStorableEnumValue(
                    $enumClass, $this->getEnumCaseFromValue($enumClass, $value)
                );
            }
        }

        return $value;
    }

    /**
     * Get the storable value from the given enum.
     *
     * @param string $expectedEnum
     * @param \UnitEnum|\BackedEnum $value
     * @return string|int
     */
    protected function getStorableEnumValue($expectedEnum, $value)
    {
        if (!$value instanceof $expectedEnum) {
            throw new \ValueError(sprintf('Value [%s] is not of the expected enum type [%s].', var_export($value, true), $expectedEnum));
        }

        return enum_value($value);
    }


    protected function getEnumCaseFromValue($enumClass, $value)
    {
        return is_subclass_of($enumClass, \BackedEnum::class)
            ? $enumClass::from($value)
            : constant($enumClass . '::' . $value);
    }

    /**
     * Determine if the given key is cast using a custom class.
     *
     * @param string $key
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\InvalidCastException
     */
    protected function isClassCastable($key)
    {
        $casts = $this->getCasts();

        if (!array_key_exists($key, $casts)) {
            return false;
        }

        $castType = $this->parseCasterClass($casts[$key]);


        if (class_exists($castType)) {
            return true;
        }

        return false;
    }

    /**
     * Parse the given caster class, removing any arguments.
     *
     * @param string $class
     * @return string
     */
    protected function parseCasterClass($class)
    {
        return !str_contains($class, ':')
            ? $class
            : explode(':', $class, 2)[0];
    }

    /**
     * Cast the given attribute using a custom cast class.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function getClassCastableAttributeValue($key, $value)
    {
        $caster = $this->resolveCasterClass($key);
        $value = $caster->get($key, $value, $this->attributes);
        return $value;
    }


    /**
     * Resolve the custom caster class for a given key.
     *
     * @param string $key
     * @return mixed
     */
    protected function resolveCasterClass($key)
    {
        $castType = $this->getCasts()[$key];

        $arguments = [];

        if (is_string($castType) && str_contains($castType, ':')) {
            $segments = explode(':', $castType, 2);

            $castType = $segments[0];
            $arguments = explode(',', $segments[1]);
        }

        if (is_object($castType)) {
            return $castType;
        }

        return new $castType(...$arguments);
    }




    /**
     * Magic method for handling dynamic method calls
     * @param $method
     * @param $arguments
     * @return $this|\BackedEnum|mixed|null
     * @throws \Exception
     */
    final public function __call(string $method, array $arguments): mixed
    {
        if (str_starts_with($method, 'set')) {
            $attribute = lcfirst(substr($method, 3));
            if ($this->hasAttribute($attribute)) {
                $this->setAttribute($attribute, ...$arguments);
            } else {
                throw new \Exception("Property {$attribute} is not defined in Store Attributes");
            }
            return $this;
        }

        // Handle getX() methods
        if (str_starts_with($method, 'get')) {
            $attribute = lcfirst(substr($method, 3)); // Get attribute name (e.g., 'Color' -> 'color')
            if ($this->hasAttribute($attribute)) {
                return $this->getAttribute($attribute);
            } else {
                throw new \Exception("Property {$attribute} is not defined in Store Attributes");
            }
        }

        throw new \Exception("Method {$method} is not defined in this Store");

    }

}
