<?php

namespace huncwot\seemycar\migrations;

use phpbb\db\migration\profilefield_base_migration;

class install_profilefield extends profilefield_base_migration
{
    protected $profilefield_name = 'seemycar_data';

    protected $profilefield_database_type = array('VCHAR', '');

    protected $profilefield_data = array(
        'field_name' => 'seemycar_data',
        'field_type' => 'profilefields.type.string',
        'field_ident' => 'seemycar_data',
        'field_length' => '64',
        'field_minlen' => '0',
        'field_maxlen' => '64',
        'field_novalue' => '',
        'field_default_value' => '',
        'field_validation' => '.*',
        'field_active' => 1,
        'field_no_view' => 1,
        'field_hide' => 1,
    );

    static public function depends_on()
    {
        return array(
            '\phpbb\db\migration\data\v320\v320',
        );
    }

    public function update_data()
    {
        return array(
            array('custom', array(array($this, 'create_custom_field'))),
        );
    }
}
