<?php

namespace huncwot\seemycar\acp;

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
     * @param array $old_forums_ids
     * @param array $new_forums_ids
     */
    protected function update_settings(array $old_forums_ids, array $new_forums_ids)
    {
    }
}
