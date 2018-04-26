<?php

namespace huncwot\seemycar\event;

use phpbb\event\data;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

    static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup' => 'user_setup',
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
}
