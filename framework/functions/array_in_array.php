<?php

/**
 * Check wether array is in array
 *
 * @param array $needle
 * @param array $haystack
 *
 * @return bool
 */
function array_in_array(array $needle, array $haystack)
{
    $difference = array_diff($needle, $haystack);
    return count($difference) === 0;
}
