<?php

namespace Sdfcloud\Langpo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Langpo Facade
 * 
 * 
 * 
 * PHP 5.0 / Laravel 4.0
 * 
 * @author        Mithun Das (mithundas79) on behalf of Pinpoint Media Design (pinpointgraphics)
 * @copyright     Copyright 2014, Pinpoint Media Design
 * @package       Sdfcloud.Langpo
 * @property      Lang $Lang
 * @since         SDFCloud 3.0
 * 
 */
class Langpo extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'langpo';
    }

}
