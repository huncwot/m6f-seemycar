<?php

namespace huncwot\seemycar\acp;

class main_info
{
    public function module()
    {
        return array(
            'filename' => '\huncwot\seemycar\acp\main_module',
            'title' => 'ACP_SEEMYCAR',
            'modes' => array(
                'settings' => array(
                    'title' => 'ACP_SEEMYCAR_SETTINGS',
                    'auth' => 'ext_huncwot/seemycar && acl_a_board',
                    'cat' => array('ACP_SEEMYCAR')
                ),
            ),
        );
    }
}
