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

// rus_build_install dbazhenov

use Tygh\Registry;
use Tygh\Addons\SchemesManager;
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'manage') {

    if (Registry::get('addons.rus_build_pack.license_key')) {
        $prefix = "rus_";

        $params = $_REQUEST;

        $addons_list = Registry::get('view')->getTemplateVars('addons_list');

        $rus_addons = array();
        foreach ($addons_list as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $rus_addons[$key] = $value;
                unset($addons_list[$key]);
            }
        }

        if (isset($params['rus_build']) && $params['rus_build'] == 'Y') {
            $addons_list = $rus_addons;
        } else {
            $addons_list = array_merge($rus_addons, $addons_list);
        }

        Registry::get('view')->assign('addons_list', $addons_list);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $mode == 'update') {

    $params = $_REQUEST;
    $prefix = "rus_";

    if ($params['addon'] == 'rus_build_pack') {
        $options = Registry::get('view')->getTemplateVars('options');

        if (isset($options['main'])) {
            unset($options['main']);
            Registry::get('view')->assign('options', $options);
        }
    }

    if (strpos($params['addon'], $prefix) === 0) {
        $addon_version = SchemesManager::getScheme($params['addon'])->getVersion();


        $url = 'http://updates.simtechdev.com/index.php?dispatch=rus_upgrade.get_instruction';
        $post_data = array(
            'addon' => $params['addon'],
            'addon_version' => $addon_version,
            'good' => md5(Registry::get('addons.rus_build_pack.license_key')),
        );

        $addon_instruction = Http::get($url, $post_data);

        Registry::get('view')->assign('addon_instruction', $addon_instruction);
        Registry::get('view')->assign('addon_version', $addon_version);
    }
}
