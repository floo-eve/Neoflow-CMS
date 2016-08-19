<?php
use Neoflow\Framework\App;

/**
 * Generate url of route.
 *
 * @param string $routeKey
 * @param array  $args
 * @param string $languageCode
 *
 * @return string
 */
function generate_url($routeKey, $args = array(), $languageCode = '')
{
    return App::instance()->get('router')->generateUrl($routeKey, $args, $languageCode);
}
