<?php

namespace huncwot\seemycar\service;

use phpbb\config\config;

class main
{
    /**
     * @var config
     */
    protected $config;

    public function __construct(config $config)
    {
        $this->config = $config;
    }

    public function get_forums_ids()
    {
        $forums_ids = json_decode($this->config['seemycar_data'], true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $forums_ids = array();
        }

        return $forums_ids;
    }
}
