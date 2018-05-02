<?php

namespace huncwot\seemycar\migrations;

class install_cron extends \phpbb\db\migration\migration
{
    static public function depends_on()
    {
        return array('\huncwot\seemycar\migrations\install_module');
    }

    public function update_data()
    {
        return array(
            array('config.add', array('seemycar_update', 14400, 0)),
            array('config.add', array('seemycar_last_update', 0, 1)),
        );
    }
}
