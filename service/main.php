<?php

namespace huncwot\seemycar\service;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\profilefields\manager;

class main
{
    /**
     * @var config
     */
    protected $config;

    /**
     * @var driver_interface
     */
    protected $db;

    /**
     * @var manager
     */
    protected $manager;

    /**
     * @param config $config
     * @param driver_interface $db
     * @param manager $manager
     */
    public function __construct(config $config, driver_interface $db, manager $manager)
    {
        $this->config = $config;
        $this->db = $db;
        $this->manager = $manager;
    }

    public function get_forums_ids()
    {
        $forums_ids = json_decode($this->config['seemycar_data'], true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $forums_ids = array();
        }

        return $forums_ids;
    }

    public function update_links()
    {
        $forums_links = $this->get_forums_links($this->get_forums_ids());
        $profiles_links = $this->get_profilefields_links();

        $links = array();

        foreach (array_keys($profiles_links) as $user_id) {
            if (false === isset($forums_links[$user_id])) {
                $links[$user_id] = '';
            }
        }

        foreach ($forums_links as $user_id => $link) {
            if (false === isset($profiles_links[$user_id]) || $link !== $profiles_links[$user_id]) {
                $links[$user_id] = $link;
            }
        }

        $this->db->sql_transaction('begin');

        foreach ($links as $user_id => $link) {
            $this->manager->update_profile_field_data($user_id, array('pf_seemycar_data' => $link));
        }

        $this->config->set('seemycar_last_update', time(), false);

        $this->db->sql_transaction('commit');
    }

    protected function get_forums_links(array $forums_ids)
    {
        $sql = 'SELECT topic_poster, topic_id, forum_id
            FROM '.TOPICS_TABLE.'
            WHERE '.$this->db->sql_in_set('forum_id', $forums_ids, false, true).' AND topic_type = '.POST_NORMAL.' AND topic_visibility = '.ITEM_APPROVED.'
            ORDER BY topic_poster, topic_time';
        $result = $this->db->sql_query($sql);

        $data = array();
        while ($row = $this->db->sql_fetchrow($result)) {
            $data[$row['topic_poster']] = $this->get_topic_link($row['forum_id'], $row['topic_id']);
        }
        $this->db->sql_freeresult($result);

        return $data;
    }

    protected function get_profilefields_links()
    {
        $sql = 'SELECT user_id, pf_seemycar_data
			FROM '.PROFILE_FIELDS_DATA_TABLE.'
			WHERE pf_seemycar_data <> \'\'
            ORDER BY user_id';
        $result = $this->db->sql_query($sql);

        $data = array();
        while ($row = $this->db->sql_fetchrow($result)) {
            $data[$row['user_id']] = $row['pf_seemycar_data'];
        }
        $this->db->sql_freeresult($result);

        return $data;
    }

    protected function get_topic_link($forum_id, $topic_id)
    {
        static $phpbb_root_path = null;
        static $phpEx = null;

        if (null === $phpbb_root_path) {
            $phpbb_root_path = true === defined('PHPBB_ROOT_PATH') ? PHPBB_ROOT_PATH : './';
        }
        if (null === $phpEx) {
            $phpEx = substr(strrchr(__FILE__, '.'), 1);
        }

        return "{$phpbb_root_path}viewtopic.{$phpEx}?f={$forum_id}&amp;t={$topic_id}";
    }
}
