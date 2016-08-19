<?php
use Neoflow\Framework\App;

/**
 * Translate key and values
 *
 * @param string $key
 * @param array  $values
 * @param string $errorPrefix
 *
 * @return string
 */
function translate($key, array $values = array())
{
    return App::instance()->get('translator')->translate($key, $values);
}
