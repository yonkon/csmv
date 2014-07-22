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
use Tygh\Settings;
use RusBuild\RusBuild;
use RusBuild\RusUpgrade;
use Tygh\Addons\SchemesManager;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$view = Registry::get('view');
$license_key = Registry::get('addons.rus_build_pack.license_key');

$rbpu = array(
    'reload' 					=>	isset($_SESSION['rbpu']['reload']) ? $_SESSION['rbpu']['reload'] : '',
    'addons' 					=>	isset($_SESSION['rbpu']['addons']) ? $_SESSION['rbpu']['addons'] : array(),
    'conflict_addons' 			=>	isset($_SESSION['rbpu']['conflict_addons']) ? $_SESSION['rbpu']['conflict_addons'] : array(),
    'step'						=>	isset($_SESSION['rbpu']['step']) ? $_SESSION['rbpu']['step'] : '',
    'conflict_upgrade_dirs' 	=>	isset($_SESSION['rbpu']['conflict_upgrade_dirs']) ? $_SESSION['rbpu']['conflict_upgrade_dirs'] : array(),
    'check_permissions_result' 	=>	isset($_SESSION['rbpu']['check_permissions_result']) ? $_SESSION['rbpu']['check_permissions_result'] : '',
    'upgrade_dirs'				=>	isset($_SESSION['rbpu']['upgrade_dirs']) ? $_SESSION['rbpu']['upgrade_dirs'] : array(),
    'check_permission_list' 	=>	isset($_SESSION['rbpu']['check_permission_list']) ? $_SESSION['rbpu']['check_permission_list'] : array(),
    'check_permissions' 		=>	isset($_SESSION['rbpu']['check_permissions']) ? $_SESSION['rbpu']['check_permissions'] : '',

);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'activate') {

        fn_set_storage_data('rbp', null);

        $rus_register = $_REQUEST['rus_register'];
        unset($_REQUEST['redirect_url']);

        if (empty($rus_register['rus_accept_terms'])) {
            fn_set_notification('E', __('error'), __('checkout_terms_n_conditions_alert'));
            Registry::get('view')->assign('rus_register', $rus_register);

        } elseif ($_REQUEST['selected_section'] == 'license' || !empty($license_key)) {

            fn_rus_build_activate_lic($rus_register, $auth);

        } elseif ($_REQUEST['selected_section'] == 'trial') {

            list($status, $msg) = RusUpgrade::getTrialKey($_REQUEST['rus_register']['email'], DESCR_SL);

            if ($status == 200) {
                $rus_register['license_key'] = $msg;

                fn_rus_build_activate_lic($rus_register, $auth);

            } elseif ($status == 400) {
                fn_set_notification('W', __('error'), $msg);
            }
        }

        return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.activate");
    }

    if ($mode == 'check_upgrade') {

        $update_needed = RusUpgrade::checkForUpgrade($auth, true);

        return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
    }

    if ($mode == 'clear_progress') {

        unset($_SESSION['rbpu']);

        return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
    }

}

if ($mode == 'upgrade') {
    if ($license_key) {

        if (!empty($rbpu['step']) && $rbpu['step'] != 'final_step') {

            fn_redirect('rus_upgrade.' . $rbpu['step']);

        } elseif ($rbpu['step'] == 'final_step') {
            unset($_SESSION['rbpu']);
        }

        RusUpgrade::checkForUpgrade($auth, true);

        $next_version_info = RusUpgrade::getNextVersionInfo();

        $view->assign('next_version_info', $next_version_info);

    } else {
        return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.activate");
    }

} elseif ($mode == 'rus_step_1') {

    $view->assign('check_list', $rbpu['addons']);
    $view->assign('reload', $rbpu['reload']);

} elseif ($mode == 'rus_step_2') {

    $all_conflict_addons = $rbpu['conflict_addons'];
    $all_conflict_dirs = $rbpu['conflict_upgrade_dirs'];
    $upgrade_dirs = $rbpu['upgrade_dirs'];
    $check_permissions_result = $rbpu['check_permissions_result'];
    $check_permission_list = $rbpu['check_permission_list'];
    $check_permissions = $rbpu['check_permissions'];
    $conflict_list = array();

    if (!empty($all_conflict_addons)) {
        foreach ($all_conflict_addons as $addon => $conflict) {

            if (!empty($conflict)) {
                foreach ($conflict as $conflict_addon => $version) {
                    $scheme = SchemesManager::getScheme($conflict_addon);
                    $conflict_list[$conflict_addon]['version'] = $scheme->getVersion();
                    $conflict_list[$conflict_addon]['name'] = $scheme->getName();
                    $conflict_list[$conflict_addon]['status'] = $scheme->getStatus();
                    $conflict_list[$conflict_addon]['backup_root'] = $all_conflict_dirs[$addon][$conflict_addon]['backup_root'];
                }
            }
        }
    }

    $view->assign('check_permission_list', $check_permission_list);
    $view->assign('check_permission_result', $check_permissions_result);
    $view->assign('check_permissions', $check_permissions);
    $view->assign('conflict_list', $conflict_list);

} elseif ($mode == 'clear') {
    RusUpgrade::removeKey();
    fn_set_storage_data('rbp', null);

    return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.activate");

} elseif ($mode == 'activate') {

    if (empty($license_key)) {
        Registry::set('navigation.tabs', array (
            'license' => array (
                'title' => __('rus_connect.license_tab'),
                'href' => "rus_upgrade.activate&selected_section=license",
                'js' => true
            ),
            'trial' => array (
                'title' => __('rus_connect.get_trial_tab'),
                'href' => "rus_upgrade.activate&selected_section=trial",
                'js' => true
            ),
        ));
    }

    $license_info = RusBuild::getLicenseInfo($license_key);

    if ($license_info) {
        if ($license_info->status == 200) {
            $license_info->license_data->created_data = date('d.m.Y', $license_info->license_data->created_data);
            $license_info->license_data->expiration = date('d.m.Y', $license_info->license_data->expiration);
            $view->assign('license_info', $license_info->license_data);
        }
    }

    $view->assign('license_txt', RusBuild::getLicenseText(PRODUCT_EDITION));
    $view->assign('rus_register', Registry::get('addons.rus_build_pack'));
}

function fn_rus_build_activate_lic($rus_register, $auth)
{
    $rus_build_pack = RusUpgrade::connectToRus($rus_register, $auth['user_id']);

    if ($rus_build_pack->response_data['access'] == "Y") {

        fn_set_storage_data('rbp', null);
        $settings = array();
        if (isset($rus_register['rus_help_us']) && $rus_register['rus_help_us'] == "Y") {
            $settings['rus_help_us'] = $rus_register['rus_help_us'];
        }

        $settings['rus_accept_terms'] = $rus_register['rus_accept_terms'];
        RusBuild::updateRusOptions($settings, 'rus_build_pack');

        fn_set_notification('N', __('notice'), __($rus_build_pack->response_data['message']));

        if (defined('AJAX_REQUEST')) {
            Registry::get('ajax')->assign('non_ajax_notifications', true);
            Registry::get('ajax')->assign('force_redirection', fn_url("rus_upgrade.upgrade"));
            exit;
        }
    }

    Registry::get('view')->assign('rus_register', $rus_register);

    if (defined('AJAX_REQUEST')) {

        Registry::set('navigation.tabs', array (
                'license' => array (
                    'title' => __('rus_connect.license_tab'),
                    'href' => "rus_upgrade.activate&selected_section=license",
                    'js' => true
                ),
                'trial' => array (
                    'title' => __('rus_connect.get_trial_tab'),
                    'href' => "rus_upgrade.activate&selected_section=trial",
                    'js' => true
                ),
        ));

        $license_txt = RusBuild::getLicenseText(PRODUCT_EDITION);
        Registry::get('view')->assign('license_txt', $license_txt);

        Registry::get('view')->display('addons/rus_build_pack/views/rus_upgrade/activate.tpl');

        exit;
    }

    return true;
}
