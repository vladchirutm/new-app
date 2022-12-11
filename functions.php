<?php

/**
 * @param $arr
 * @return void
 */
function pr($arr): void
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

/**
 * @param string $file
 * @param array $parameters
 */
function loadTemplate(string $file, array $parameters = array()): void
{
    foreach ($parameters as $k => $v) {
        ${$k} = $v;
    }
    include(APP_PATH . '/templates/' . $file . '.php');
}