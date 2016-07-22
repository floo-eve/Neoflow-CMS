<?php

/**
 * Normalize directory
 *
 * @param string $directory
 * @return string
 */
function normalize_directory($directory)
{
    return rtrim(preg_replace('/[\\|\/|\\\\|\/\/]+/', DIRECTORY_SEPARATOR, $directory), DIRECTORY_SEPARATOR);
}
