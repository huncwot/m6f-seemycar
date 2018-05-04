<?php

if (false === defined('IN_PHPBB')) {
    exit;
}

if (true === empty($lang) || false === is_array($lang)) {
    $lang = array();
}

$lang = array_merge($lang, array(
    'ACP_SEEMYCAR' => 'See My Car',
    'ACP_SEEMYCAR_SETTINGS' => 'Ustawienia',
    'ACP_SEEMYCAR_EXPLAIN' => 'Autorowi tematu w wybranych forach zostanie przypisany link do tego tematu. Ten link będzie widoczny w profilu tego użytkownika i przy każdym jego poście. Autor tematu ma prawo dowolnie edytować pierwszy post tego tematu.',
    'SELECT_FORUM_EXPLAIN' => 'Możesz wybrać więcej niż jedno forum.',
));
