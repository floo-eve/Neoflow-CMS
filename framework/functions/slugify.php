<?php

/**
 * Slugify string
 *
 * @param string $string
 * @return string
 */
function slugify($string)
{
    $string = str_replace(array('ä', 'ü', 'ö', 'ß', 'ç'), array('ae', 'ue', 'oe', 'ss', 'c'), $string);
    $string = preg_replace('~[^\\pL\d]+~u', '-', $string);
    $string = trim($string, '-');
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = strtolower($string);
    $string = preg_replace('~[^-\w]+~', '', $string);

    return $string;
}
