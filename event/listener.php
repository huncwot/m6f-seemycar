<?php

namespace huncwot\seemycar\event;

use huncwot\seemycar\service\main;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    /**
     * @var template
     */
    protected $template;

    /**
     * @var language
     */
    protected $language;

    /**
     * @var main
     */
    protected $main;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var array
     */
    protected $forums_ids;

    /**
     * @var array
     */
    protected $profile_fields;

    /**
     * @param template $template
     */
    public function __construct(template $template, language $language, main $main)
    {
        $this->template = $template;
        $this->language = $language;
        $this->main = $main;
    }

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup' => 'user_setup',
            'core.grab_profile_fields_data' => 'grab_profile_fields_data',
            'core.memberlist_view_profile' => 'memberlist_view_profile',
            'core.viewtopic_get_post_data' => 'viewtopic_get_post_data',
            'core.viewtopic_modify_post_row' => 'viewtopic_modify_post_row',
            'core.viewtopic_modify_post_action_conditions' => 'viewtopic_modify_post_action_conditions',
            'core.posting_modify_cannot_edit_conditions' => 'posting_modify_cannot_edit_conditions',
        );
    }

    /**
     * @param data $event
     */
    public function user_setup(data $event)
    {
        $this->user_id = (int) $event['user_data']['user_id'];
    }

    /**
     * @param data $event
     */
    public function grab_profile_fields_data(data $event)
    {
        $this->profile_fields = array();

        $field_data = $event['field_data'];

        foreach ($field_data as $user_id => &$fields) {
            if (false === array_key_exists('pf_seemycar_data', $fields)) {
                continue;
            }
            if (false === empty($fields['pf_seemycar_data'])) {
                $this->profile_fields[(int) $user_id] = $fields['pf_seemycar_data'];
            }
            unset($fields['pf_seemycar_data']);
        }
        unset($fields);

        $event['field_data'] = $field_data;
    }

    /**
     * @param data $event
     */
    public function memberlist_view_profile(data $event)
    {
        $member_id = (int) $event['member']['user_id'];
        if (true === isset($this->profile_fields[$member_id])) {
            $this->load_language();
            $this->template->assign_var('U_SEEMYCAR', $this->profile_fields[$member_id]);
        }
    }

    public function viewtopic_get_post_data()
    {
        $this->load_language();
        $this->load_forums_ids();
    }

    /**
     * @param data $event
     */
    public function viewtopic_modify_post_row(data $event)
    {
        $poster_id = (int) $event['poster_id'];
        if (true === isset($this->profile_fields[$poster_id])) {
            $post_row = $event['post_row'];
            $post_row['U_SEEMYCAR'] = $this->profile_fields[$poster_id];
            $event['post_row'] = $post_row;
        }
    }

    /**
     * @param data $event
     */
    public function viewtopic_modify_post_action_conditions(data $event)
    {
        if ((int) $event['row']['user_id'] !== $this->user_id) {
            return;
        }

        if ((int) $event['row']['post_id'] !== (int) $event['topic_data']['topic_first_post_id']) {
            return;
        }

        if (false === in_array((int) $event['row']['forum_id'], $this->forums_ids)) {
            return;
        }

        $event['force_edit_allowed'] = true;
    }

    /**
     * @param data $event
     */
    public function posting_modify_cannot_edit_conditions(data $event)
    {
        $this->load_forums_ids();

        if ((int) $event['post_data']['poster_id'] !== $this->user_id) {
            return;
        }

        if ((int) $event['post_data']['post_id'] !== (int) $event['post_data']['topic_first_post_id']) {
            return;
        }

        if (false === in_array((int) $event['post_data']['forum_id'], $this->forums_ids)) {
            return;
        }

        $event['force_edit_allowed'] = true;
    }

    protected function load_language()
    {
        $this->language->add_lang('common', 'huncwot/seemycar');
    }

    protected function load_forums_ids()
    {
        if (null === $this->forums_ids) {
            $this->forums_ids = $this->main->get_forums_ids();
        }
    }
}
