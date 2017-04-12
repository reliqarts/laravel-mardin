<?php

namespace ReliQArts\Mardin\Helpers;

use Config;

class RouteHelper
{
    /**
     * Get route prefix for mardin routes.
     *
     * @return string Prefix.
     */
    public static function getRoutePrefix()
    {
        return Config::get('mardin.routes.prefix', 'messages');
    }

    /**
     * Get controller for mardin routes.
     *
     * @return string Controller.
     */
    public static function getMessagesController()
    {
        return Config::get('mardin.routes.controller', 'ReliQArts\\Mardin\\Http\\Controllers\\MessagesController');
    }

    /**
     * Get bindings for public routes.
     */
    public static function getRouteGroupBindings($bindings = [], $groupKey = 'public')
    {
        $defaults = ($groupKey == 'public') ? ['prefix' => self::getRoutePrefix()] : [];
        $bindings = array_merge(Config::get("mardin.routes.bindings.{$groupKey}", []), $bindings);

        return array_merge($defaults, $bindings);
    }
}
