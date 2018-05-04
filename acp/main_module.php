<?php

namespace huncwot\seemycar\acp;

use huncwot\seemycar\service\main;
use phpbb\config\config;
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

        $forums_ids = $main->get_forums_ids();

        if ($request->is_set_post('forum')) {
            if (false === check_form_key($form_name)) {
                trigger_error($user->lang['FORM_INVALID'].adm_back_link($this->u_action), E_USER_WARNING);
            }

            $new_forums_ids = $request->variable('forum', array(0));

            if ($forums_ids !== $new_forums_ids) {
                $config->set('seemycar_data', json_encode($new_forums_ids));
                $main->update_links();
            }

            trigger_error($user->lang('CONFIG_UPDATED').adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
            'U_ACTION' => $this->u_action,
            'S_OPTIONS' => make_forum_select($forums_ids),
        ));
    }
}
