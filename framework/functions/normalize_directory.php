<?php

/**
 * Normalize path
 *
 * @param string $path
 * @return string
 */
function normalize_path($path)
{
    return rtrim(preg_replace('/[\\|\/|\\\\|\/\/]+/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
}
