<?php

namespace Nexusvc\Pwned;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use PwnedValidator;

class PwnedServiceProvider extends ServiceProvider
{

    /**
     * Register with the application
     *
     * @return void
     */
    public function boot()
    {
        // Macro mapWithKeys for < Laravel 5.3
        if (!Collection::hasMacro('mapWithKeys')) {
            collect()->macro('mapWithKeys', function ($callback) {
                $result = [];
                foreach ($this->items as $key => $value) {
                    $assoc = $callback($value, $key);
                    foreach ($assoc as $mapKey => $mapValue) {
                        $result[$mapKey] = $mapValue;
                    }
                }
                return new static($result);
            });
        }

        Validator::extend('pwned', PwnedValidator::class);

    }
    
}
