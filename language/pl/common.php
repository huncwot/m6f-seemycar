<?php

if (false === defined('IN_PHPBB')) {
    exit;
}

if (true === empty($lang) || false === is_array($lang)) {
    $lang = array();
}

$lang = array_merge($lang, array(
    'MY_CAR' => 'Moja Mazda',
    'SEE_MY_CAR' => 'Zobacz moją Mazdę',
    ));
