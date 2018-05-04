<?php

namespace huncwot\seemycar\acp;

use huncwot\seemycar\service\main;
use phpbb\config\config;
use phpbb\language\language;
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
     * @global config $config
     * @param string $id
     * @param string $mode
     */
    public function main($id, $mode)
    {
        global $user, $phpbb_container, $request, $template, $config;

        $this->page_title = $user->lang('ACP_SEEMYCAR_SETTINGS');
        $this->tpl_name = 'main_body';

        $form_name = 'acp_seemycar_settings';
        add_form_key($form_name);

        /* @var $main main */
        $main = $phpbb_container->get('huncwot.seemycar.service.main');

        $current_forums_ids = $main->get_forums_ids();
        $current_update_frequency = (int) $config['seemycar_update'];

        if ($request->is_set_post('submit')) {
            if (false === check_form_key($form_name)) {
                trigger_error($user->lang['FORM_INVALID'].adm_back_link($this->u_action), E_USER_WARNING);
            }

            $new_forums_ids = $request->variable('forum', array(0));
            $new_frequency = $request->variable('frequency', 14400);
            $update_now = $request->variable('update_now', 0);

            if ($current_forums_ids !== $new_forums_ids) {
                $config->set('seemycar_data', json_encode($new_forums_ids));
            }

            if ($current_update_frequency !== $new_frequency) {
                $config->set('seemycar_update', $new_frequency);
            }

            if (1 === $update_now) {
                $main->update_links();
            }

            trigger_error($user->lang('CONFIG_UPDATED').adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
            'U_ACTION' => $this->u_action,
            'S_FORUM_OPTIONS' => make_forum_select($current_forums_ids),
            'S_FREQUENCY_OPTIONS' => $this->make_frequency_select($current_update_frequency),
        ));
    }

    /**
     * @global ContainerInterface $phpbb_container
     * @param integer $update_frequency
     * @return string
     */
    function make_frequency_select($update_frequency)
    {
        global $phpbb_container;

        /* @var $language language */
        $language = $phpbb_container->get('language');

        $options = '';
        foreach (array(1 => 3600, 2 => 7200, 3 => 10800, 4 => 14400, 6 => 21600, 12 => 43200, 24 => 86400) as $hour => $seconds) {
            $selected = $update_frequency === $seconds ? ' selected="selected"' : '';
            $options .= "<option value=\"{$seconds}\"{$selected}>{$language->lang_array('HOURLY_INTERVALS', array($hour))}</option>";
        }

        return $options;
    }
}
