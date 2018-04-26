<?php

namespace huncwot\seemycar\service;

use phpbb\config\config;
use phpbb\profilefields\manager;

class main
{
    /**
     * @var config
     */
    protected $config;

    /**
     * @var manager
     */
    protected $manager;

    public function __construct(config $config, manager $manager)
    {
        $this->config = $config;
        $this->manager = $manager;
    }

    public function set_forums_ids(array $forums_ids)
    {
        $this->config->set('seemycar_data', json_encode($forums_ids));
    }

    public function get_forums_ids()
    {
        $forums_ids = json_decode($this->config['seemycar_data'], true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $forums_ids = array();
        }

        return $forums_ids;
    }

    public function update_profile_field_data($user_id, $forum_id, $topic_id)
    {
        static $phpbb_root_path = null;
        static $phpEx = null;

        if (null === $phpbb_root_path) {
            $phpbb_root_path = true === defined('PHPBB_ROOT_PATH') ? PHPBB_ROOT_PATH : './';
        }
        if (null === $phpEx) {
            $phpEx = substr(strrchr(__FILE__, '.'), 1);
        }

        $topic_url = "{$phpbb_root_path}viewtopic.{$phpEx}?f={$forum_id}&amp;t={$topic_id}";

        $this->manager->update_profile_field_data($user_id, array('pf_seemycar_data' => $topic_url));
    }
}
