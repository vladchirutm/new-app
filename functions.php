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

/**
 * @param string $var
 * @param bool $addSlashes
 * @return string
 */
function sanitizeItem(string $var, bool $addSlashes = false): string
{
    $var = htmlspecialchars($var);
    if($addSlashes){
        $var = htmlentities($var, ENT_QUOTES);
    }

    $var = htmlentities($var);

    return $var;
}