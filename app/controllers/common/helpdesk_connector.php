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

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Helpdesk;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'validate_request' && !empty($_REQUEST['token'])) {
        $result = 'invalid';

        if (fn_get_storage_data('hd_request_code') == trim($_REQUEST['token'])) {
            $result = 'valid';
        }

        echo $result;
        exit(0);
    } elseif ($mode == 'messages') {
        if (!empty($_REQUEST['token'])) {
            $uc_settings = Settings::instance()->getValues('Upgrade_center');

            $is_valid = fn_get_contents(Registry::get('config.updates_server') . '/index.php?dispatch=validators.validate_request&token=' . $_REQUEST['token'] . '&license_key=' . $uc_settings['license_number']);
            if ($is_valid == 'valid') {
                $data = simplexml_load_string(urldecode($_REQUEST['request']));

                Helpdesk::processMessages($data->Messages);

                echo 'OK';
                exit(0);

            } else {
                return array(CONTROLLER_STATUS_NO_PAGE);
            }
        }
    }
}

if ($mode == 'auth') {

    

    if ($_SESSION['auth']['area'] == 'A' && !empty($_SESSION['auth']['user_id'])) {
        $domains = '';
        if (fn_allowed_for('ULTIMATE')) {
            $storefronts = db_get_fields('SELECT storefront FROM ?:companies WHERE storefront != ""');

            if (!empty($storefronts)) {
                $domains = implode(',', $storefronts);
            }
        }

        $extra_fields = array(
            'token' => Helpdesk::token(true),
            'store_key' => Helpdesk::getStoreKey(),
            'domains' => $domains
        );

        $data = Helpdesk::getLicenseInformation('', $extra_fields);
        Helpdesk::parseLicenseInformation($data, $auth);
    }

    exit;
}

return array(CONTROLLER_STATUS_NO_PAGE);
