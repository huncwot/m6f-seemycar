<?php

namespace huncwot\seemycar\migrations;

use phpbb\db\migration\migration;

class install_module extends migration
{
    static public function depends_on()
    {
        return array('\huncwot\seemycar\migrations\install_profilefield');
    }

    public function update_data()
    {
        return array(
            array('config.add', array('seemycar_data', '[]')),
            array('module.add', array(
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_SEEMYCAR'
            )),
            array('module.add', array(
                'acp',
                'ACP_SEEMYCAR',
                array(
                    'module_basename' => '\huncwot\seemycar\acp\main_module',
                    'modes' => array('settings'),
                ),
            )),
        );
    }
}
