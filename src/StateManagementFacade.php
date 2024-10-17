<?php

namespace MKD\StateManagement;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mkdev\LaravelAdvancedOTP\Skeleton\SkeletonClass
 */
class StateManagementFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'state-management';
    }
}
