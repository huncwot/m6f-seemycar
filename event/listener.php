<?php

namespace huncwot\seemycar\event;

use phpbb\event\data;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    /**
     * @var template
     */
    protected $template;

    /**
     * @var array
     */
    protected $profile_fields;

    /**
     * @param template $template
     */
    public function __construct(template $template)
    {
        $this->template = $template;
        $this->profile_fields = array();
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
            'core.viewtopic_modify_post_row' => 'viewtopic_modify_post_row',
        );
    }

    /**
     * @param data $event
     */
    public function user_setup(data $event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'huncwot/seemycar',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    /**
     * @param data $event
     */
    public function grab_profile_fields_data(data $event)
    {
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
            $this->template->assign_var('U_SEEMYCAR', $this->profile_fields[$member_id]);
        }
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
}
