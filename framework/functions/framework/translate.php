<?php
use Neoflow\Framework\App;

function translate($key, array $values = array())
{
    return App::instance()->get('translator')->translate($key, $values);
}
