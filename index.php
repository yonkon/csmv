<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

$php_value = phpversion();
if (version_compare($php_value, '5.3.0') == -1) {
    echo 'Currently installed PHP version (' . $php_value . ') is not supported. Minimal required PHP version is  5.3.0.';
    die();
}

define('AREA', 'C');
define('_ROOT_DIR_', dirname(__FILE__));
try {
//    require(_ROOT_DIR_ . '/app/classes/ActiveRecord/ActiveRecord.php');
//
//    ActiveRecord\Config::initialize(function($cfg)
//    {
//        $cfg->set_model_directory(_ROOT_DIR_ . '/app/classes/ActiveRecord/models');
//        $cfg->set_connections(array('development' => 'mysql://root:root@127.0.0.1'));
//
//        // you can change the default connection with the below
//        //$cfg->set_default_connection('production');
//    });

	require(dirname(__FILE__) . '/init.php');
	fn_dispatch();
} catch (Tygh\Exceptions\AException $e) {
	$e->output();
}
