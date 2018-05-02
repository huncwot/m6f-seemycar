<?php

namespace huncwot\seemycar\cron\task;

use huncwot\seemycar\service\main;
use phpbb\config\config;
use phpbb\cron\task\base;

class update_forums extends base
{
    /**
     * @var config
     */
    protected $config;

    /**
     * @var main
     */
    protected $main;

    /**
     * @param config $config
     * @param main $main
     */
    public function __construct(config $config, main $main)
    {
        $this->config = $config;
        $this->main = $main;
    }

    public function should_run()
    {
        return $this->config['seemycar_last_update'] < time() - $this->config['seemycar_update'];
    }

    public function run()
    {
        $this->config->set('seemycar_last_update', time(), false);
        $this->main->update_links();
    }
}
