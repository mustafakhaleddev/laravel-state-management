<?php

namespace MKD\StateManagement\Contracts;

use Illuminate\Support\Facades\Cache;
use MKD\StateManagement\traits\StoreAttributes;

abstract class StoreContract implements StoreInterface
{

    use StoreAttributes;

    protected array $state;
    protected int|string|null $key;

    protected bool $customPersist;
    protected bool $customRehydrate;

    /**
     * Set Primary Key for Current Store
     * @param string $key
     * @return void
     */
    final public function setKey(int|string|null $key): void
    {
        $this->key = $key;
    }


    /**
     * Return Primary Key to store current state
     * @return string
     */
    final public function getKey(): string
    {
        return 'store_state_' . self::class . ':' . ($this->key ?? '0');
    }


    /**
     * Override default caching persist
     * @return void
     */
    public function persistUsing(): void
    {
    }

    /**
     * Override default hydrate from caching
     * @return void
     */
    public function rehydrateUsing(): void
    {
    }


    /**
     * Set flag to use custom persist
     * @return void
     */
    final public function initCustomPersist(): void
    {
        $this->customPersist = true;
    }


    /**
     * Set flag to use custom rehydrate
     * @return void
     */
    final public function initCustomRehydrate(): void
    {
        $this->customRehydrate = true;
    }

    /**
     * Override Default State if rehydrate failed to return data
     * @return array
     */
    abstract public function default(): array;

    /**
     * Presist State using primary key
     * @return void
     */
    final public function persist(): void
    {
        $this->persistUsing();
        if ($this->customPersist) {
            return;
        }
        Cache::set($this->getKey(), json_encode($this->state));
    }

    /**
     * Presist State using default state
     * @return void
     */
    final public function persistFromDefault(): void
    {
        $this->setState($this->default());
        $this->persist();
    }

    /**
     * Rehydrate State using Cache , or other methods
     * @return void
     */
    final public function rehydrate(): void
    {
        $this->rehydrateUsing();
        if ($this->customRehydrate) {
            return;
        }
        if (!Cache::has($this->getKey())) {
            $this->persistFromDefault();
        } else {
            $storedState = Cache::get($this->getKey());
            $this->setState($storedState ? json_decode($storedState, true) : []);
        }
    }


    /**
     * Method to set the entire state
     * @param array $state
     * @return void
     */
    private function setState(array $state): void
    {
        foreach ($state as $key => $value) {
            if (in_array($key, $this->attributes)) {
                $this->setAttribute($key, $value);
            }
        }
    }

    /**
     * Method to return the entire state
     * @return array
     */
    final public function getState(): array
    {
        return $this->state;
    }


    /**
     * Return State Value
     * @param $key
     * @return \BackedEnum|mixed|null
     */
    final public function getStateValue(string $key): mixed
    {
        return $this->getAttribute($key);
    }
}
