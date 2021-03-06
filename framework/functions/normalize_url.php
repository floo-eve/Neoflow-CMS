<?php

/**
 * Normalize URL
 *
 * @param string $url
 * @return string
 */
function normalize_url($url)
{
    return rtrim(preg_replace('/([^:])(\/{2,})/', '$1/', $url), '/');
}
