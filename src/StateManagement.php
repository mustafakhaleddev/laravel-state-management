<?php

namespace MKD\StateManagement;


use MKD\StateManagement\Contracts\StoreContract;


class StateManagement
{

    /**
     * Return Store instance
     * @param string $store
     * @return StoreContract
     * @throws \Exception
     */
    public static function use(string $store): StoreContract
    {
        $store = new $store();
        if (!$store instanceof StoreContract) {
            throw new \Exception("Provided Store {$store} is not a valid StoreContract");
        }

        return app(StateManagement::class)->store($store::class);

    }

    /**
     * Register and return store instance
     * @param string $store
     * @return StoreContract
     */
    public function store(string $store): StoreContract
    {
        if (!app()->bound($store)) {
            app()->singleton($store, function () use ($store) {
                return new $store;
            });
        }
        return app($store);
    }
}
