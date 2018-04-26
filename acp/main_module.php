<?php

namespace huncwot\seemycar\acp;

use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\DependencyInjection\ContainerInterface;

class main_module
{
    public $page_title;

    public $tpl_name;

    public $u_action;

    /**
     * @global user $user
     * @global ContainerInterface $phpbb_container
     * @global request $request
     * @global template $template
     * @param string $id
     * @param string $mode
     */
    public function main($id, $mode)
    {
        global $user, $phpbb_container, $request, $template;

        $this->page_title = $user->lang('ACP_SEEMYCAR_SETTINGS');
        $this->tpl_name = 'main_body';

        $form_name = 'acp_seemycar_settings';
        add_form_key($form_name);

        $forums_ids = $phpbb_container->get('huncwot.seemycar.service.main')->get_forums_ids();

        if ($request->is_set_post('forum')) {
            if (false === check_form_key($form_name)) {
                trigger_error($user->lang['FORM_INVALID'].adm_back_link($this->u_action), E_USER_WARNING);
            }

            $this->update_settings($forums_ids, $request->variable('forum', array(0)));

            trigger_error($user->lang('CONFIG_UPDATED').adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
            'U_ACTION' => $this->u_action,
            'S_OPTIONS' => make_forum_select($forums_ids),
        ));
    }

    /**
     * @global ContainerInterface $phpbb_container
     * @global driver_interface $db
     * @param array $old_forums_ids
     * @param array $new_forums_ids
     */
    protected function update_settings(array $old_forums_ids, array $new_forums_ids)
    {
        global $phpbb_container, $db;

        if ($old_forums_ids === $new_forums_ids) {
            return;
        }

        $service = $phpbb_container->get('huncwot.seemycar.service.main');

        $out_forums_ids = array_diff($old_forums_ids, $new_forums_ids);
        $in_forums_ids = array_diff($new_forums_ids, $old_forums_ids);

        $out_forums_data = true !== empty($out_forums_ids) ? $this->get_forums_data($out_forums_ids) : array();
        $in_forums_data = true !== empty($in_forums_ids) ? $this->get_forums_data($in_forums_ids) : array();

        $db->sql_transaction('begin');

        if (true !== empty($out_forums_data)) {
            $sql = 'UPDATE '.PROFILE_FIELDS_DATA_TABLE.'
			SET '.$db->sql_build_array('UPDATE', array('pf_seemycar_data' => '')).'
			WHERE '.$db->sql_in_set('user_id', array_keys(array_diff_key($out_forums_data, $in_forums_data)), false, true);
            $db->sql_query($sql);
        }

        if (true !== empty($in_forums_data)) {
            foreach ($in_forums_data as $data) {
                $service->update_profile_field_data($data['topic_poster'], $data['forum_id'], $data['topic_id']);
            }
        }

        $service->set_forums_ids($new_forums_ids);

        $db->sql_transaction('commit');
    }

    /**
     * @global driver_interface $db
     * @param array $forums_ids
     */
    protected function get_forums_data(array $forums_ids)
    {
        global $db;

        $sql = 'SELECT topic_poster, topic_id, forum_id
            FROM '.TOPICS_TABLE.'
            WHERE '.$db->sql_in_set('forum_id', $forums_ids, false, true).' AND topic_status = 0 AND topic_type = 0 AND topic_visibility = 1
            ORDER BY topic_poster, topic_time';
        $result = $db->sql_query($sql);

        $data = array();
        while ($row = $db->sql_fetchrow($result)) {
            $data[$row['topic_poster']] = $row;
        }
        $db->sql_freeresult($result);

        return $data;
    }
}
