<?php

namespace Stats4sd\KoboLink;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stats4sd\KoboLink\KoboLink
 */
class KoboLinkFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-kobo-link';
    }
}
