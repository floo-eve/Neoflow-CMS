<?php

/**
 * Check wether string is valid JSON encoded data
 *
 * @param string $string
 *
 * @return bool
 */
function is_json($string)
{
    json_decode($string);

    return (json_last_error() === JSON_ERROR_NONE);
}
