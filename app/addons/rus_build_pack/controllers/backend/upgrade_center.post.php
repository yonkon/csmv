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

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

use Tygh\Registry;
use Tygh\Settings;
use RusBuild\RusUpgrade;
use RusBuild\RusCheckConflicts;
use RusBuild\RusUpgradeMethods;

$rbpu = array(
    'reload' 					=>	isset($_SESSION['rbpu']['reload']) ? 			       $_SESSION['rbpu']['reload'] : '',
    'addons' 					=>	isset($_SESSION['rbpu']['addons']) ? 			       $_SESSION['rbpu']['addons'] : array(),
    'schemas'                   =>  isset($_SESSION['rbpu']['schemas']) ?                  $_SESSION['rbpu']['schemas'] : array(),
    'conflict_addons' 			=>	isset($_SESSION['rbpu']['conflict_addons']) ? 	       $_SESSION['rbpu']['conflict_addons'] : array(),
    'conflict_schema'           =>  isset($_SESSION['rbpu']['conflict_schema']) ?          $_SESSION['rbpu']['conflict_schema'] : array(),
    'step'						=>	isset($_SESSION['rbpu']['step']) ? 				       $_SESSION['rbpu']['step'] : 'rus_step_1',
    'conflict_upgrade_dirs' 	=>	isset($_SESSION['rbpu']['conflict_upgrade_dirs']) ?    $_SESSION['rbpu']['conflict_upgrade_dirs'] : array(),
    'check_permissions_result' 	=>	isset($_SESSION['rbpu']['check_permissions_result']) ? $_SESSION['rbpu']['check_permissions_result'] : '',
    'upgrade_dirs'				=>	isset($_SESSION['rbpu']['upgrade_dirs']) ? 		       $_SESSION['rbpu']['upgrade_dirs'] : array(),
    'check_permission_list' 	=>	isset($_SESSION['rbpu']['check_permission_list']) ?    $_SESSION['rbpu']['check_permission_list'] : array(),
    'check_dirs_list' 			=>	isset($_SESSION['rbpu']['check_dirs_list']) ? 	       $_SESSION['rbpu']['check_dirs_list'] : array(),
    'check_permissions' 		=>	isset($_SESSION['rbpu']['check_permissions']) ?        $_SESSION['rbpu']['check_permissions'] : '',
    'addons_versions'			=>	isset($_SESSION['rbpu']['addons_versions']) ? 	       $_SESSION['rbpu']['addons_versions'] : array(),
    'addons_list'				=>	isset($_SESSION['rbpu']['addons_list']) ?		       $_SESSION['rbpu']['addons_list'] : array(),
);

$step = $rbpu['step'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'upgrade_rus') {

        if ($step == "rus_step_1" && $action != "rus_step_2") {

            if ($action == "reload") {
                $addons = $rbpu['addons'];

            } else {

                fn_echo('<b>' . __('rus_connect.upgrade.addons') . ' </b><br/>');

                $addons = array();
                $addons_versions = array();
                $addons_list = fn_get_dir_contents(Registry::get('config.dir.addons'), true, false);

                fn_rus_build_get_update_addons($addons_list, $_REQUEST['addons'], $addons, $addons_versions);

                if (empty($addons)) {
                    fn_set_notification('E', __('error'), __('rus_connect.upgrade.no_addons'));

                    return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
                }

                fn_echo('<b>' . __('rus_connect.upgrade.download_distr') . ' </b><br/>');

                $_SESSION['rbpu'] = array();
                $_SESSION['rbpu']['addons'] = $addons;
                $_SESSION['rbpu']['addons_versions'] = $addons_versions;
                $_SESSION['rbpu']['addons_list'] = $addons_list;
            }

            $reload = false;
            $action_reload = isset($_SESSION['rbpu']['reload']) && $_SESSION['rbpu']['reload'] == "Y" && $action == "reload";

            foreach ($addons as $addon => $addon_info) {

                if ($action_reload && $_SESSION['rbpu']['addons'][$addon]['check_download'] == "Y") {
                    continue;
                }

                fn_echo( ' <br/>' . __('rus_connect.upgrade.download_distr') . ' '. $addon_info['addon_name'] . ' <br/>');
                $install_src_dir = RusUpgrade::downloadDistr($addon, $addon_info['next_version'], $addon_info['current_version']);

                if (!$install_src_dir) {
                    $error_string = __('text_uc_cant_download_package') . " - " . $addon_info['addon_name'];
                    fn_echo('<span style="color:red">' . $error_string . '</span><br>');
                    fn_set_notification('E', __('error'), $error_string);
                    $reload = true;
                    $_SESSION['rbpu']['addons'][$addon]['check_download'] = "N";
                } else {
                    $_SESSION['rbpu']['addons'][$addon]['check_download'] = "Y";
                    $_SESSION['rbpu']['addons'][$addon]['install_src_dir'] = $install_src_dir;
                }

            }

            $_SESSION['rbpu']['step'] = "rus_step_1";

            if ($reload) {
                $_SESSION['rbpu']['reload'] = "Y";
                fn_stop_scroller();

                return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.rus_step_1");

            } elseif (isset($_SESSION['rbpu']['reload'])) {
                unset($_SESSION['rbpu']['reload']);
            }

            return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.rus_step_1");

        } elseif ($action == "rus_step_2" || $step == "rus_step_2" && $action != "rus_step_3") {

            if ($action != "repeat_check_perm") {
                $addons_list = $rbpu['addons_list'];
                $addons = $rbpu['addons'];
                $addons_versions = $rbpu['addons_versions'];

                if (empty($addons)) {
                    fn_set_notification('E', __('error'), __('rus_connect.upgrade.no_addons'));

                    return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
                }

                $_SESSION['rbpu']['step'] == "rus_step_2";

                fn_echo(__('rus_connect.upgrade.get_schemas')  . '<br/>');

                $schemas = RusUpgrade::getAddonsSchemas($addons_versions);

                fn_echo('<b>' . __('rus_connect.upgrade.check_conflict_addons') . ' </b><br/>');
                $conflict_addons = array();

                if (!empty($schemas) && is_array($schemas)) {
                    foreach ($addons as $addon => $info) {

                        if ($info['check_download'] != "Y") {
                            unset($addons[$addon]);
                            unset($_SESSION['rbpu']['addons'][$addon]);
                            continue;
                        }

                        if (isset($schemas[$addon])) {
                            $schema = $schemas[$addon];
                            fn_echo(__('rus_connect.upgrade.check_conflict_addons') . ' ' . $info['addon_name'] . '<br/>');
                            if (!empty($schema['conflict']['addons'])) {
                                $result = RusCheckConflicts::checkAddons($addons_list, $schema['conflict']['addons']);

                                if ($result != false) {
                                    $conflict_addons[$addon] = $result;
                                }
                            }
                        } else {
                            fn_set_notification('E', __('error'), __('rus_connect.upgrade.no_schema') . $info['addon_name']);
                            unset($addons[$addon]);
                        }

                    }
                } else {
                    unset($_SESSION['rbpu']);

                    return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
                }

                if (empty($addons)) {
                    unset($_SESSION['rbpu']);
                    fn_set_notification('E', __('error'), __('rus_connect.upgrade.no_addons'));

                    return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
                }

                if (!empty($conflict_addons)) {
                    fn_echo(__('rus_connect.upgrade.get_conflicts_schema')  . '<br/>');
                    $conflict_schema = RusCheckConflicts::getSchemas($conflict_addons);
                }

                fn_echo('<b>' . __('rus_connect.upgrade.generate_dirs') . ' </b><br/>');

                $all_upgrade_dirs = array();
                $conflict_upgrade_dirs = array();
                foreach ($addons as $addon => $info) {

                    fn_echo(__('rus_connect.upgrade.generate_dirs') . ' ' . $info['addon_name'] . '<br/>');

                    $unpack_path = RUS_UPGRADE_DIR . $addon . '_' . $info['next_version'] . '/' . 'unpacked/';

                    $schemas[$addon]['install_src_dir'] = $unpack_path;

                    $_upgrade_dirs = RusUpgrade::getUpgradeDirs(
                        $info['install_src_dir'],
                        $addon ,
                        $schemas[$addon],
                        $info['current_version']
                    );

                    $all_upgrade_dirs[$addon] = $_upgrade_dirs;

                    if (!empty($conflict_addons[$addon]) && !empty($conflict_schema[$addon])) {

                        foreach ($conflict_addons[$addon] as $conflict_addon => $conflict_addon_version) {
                            fn_echo( __('rus_connect.upgrade.generate_dirs') . ' ' . $conflict_addon . ' '.$conflict_addon_version. '<br/>');
                            $__upgrade_dirs = RusUpgrade::getUpgradeDirs(
                                '',
                                $conflict_addon ,
                                $conflict_schema[$addon][$conflict_addon],
                                $conflict_addon_version
                            );

                            $conflict_upgrade_dirs[$addon][$conflict_addon] = $__upgrade_dirs;
                        }
                    }
                }

                $check_permission_list = array();
                $check_dirs_list = array();
                foreach ($all_upgrade_dirs as $addon => $dirs) {
                    RusUpgradeMethods::uniqArray($check_permission_list , $dirs['installed']);
                    RusUpgradeMethods::uniqArray($check_dirs_list , $dirs['check_dirs']);
                    if (!empty($conflict_upgrade_dirs[$addon])) {
                        foreach ($conflict_upgrade_dirs[$addon] as $conflict_addon => $_schema) {
                            RusUpgradeMethods::uniqArray($check_permission_list , $_schema['installed']);
                        }
                    }
                }

                $_SESSION['rbpu']['addons'] = $addons;
                $_SESSION['rbpu']['schemas'] = $schemas;
                $_SESSION['rbpu']['upgrade_dirs'] = $all_upgrade_dirs;
                $_SESSION['rbpu']['conflict_addons'] = $conflict_addons;
                $_SESSION['rbpu']['conflict_schema'] = isset($conflict_schema) ? $conflict_schema : '';
                $_SESSION['rbpu']['conflict_upgrade_dirs'] = $conflict_upgrade_dirs;
            } else {
                $check_permission_list = $rbpu['check_permission_list'];
                $check_dirs_list = $rbpu['check_dirs_list'];
            }

            $_SESSION['rbpu']['step'] = "rus_step_2";

            $uc_settings = Settings::instance()->getValues('Upgrade_center');

            fn_ftp_connect($uc_settings);

            fn_echo('<b>' . __('rus_connect.upgrade.checking_permissions') . ' </b><br/>');

            $_SESSION['rbpu']['check_permissions'] = "Y";
            $_SESSION['rbpu']['check_dirs_list'] = $check_dirs_list;
            $_SESSION['rbpu']['check_permission_list'] = $check_permission_list;

            $no_premission = array();
            $check_dirs_result = array();
            if (!empty($check_dirs_list)) {
                RusUpgrade::checkInstallPermissions($check_dirs_list, true, $check_dirs_result);
            }
            if (!empty($check_dirs_result)) {
                $_SESSION['rbpu']['check_permissions'] = "N";
                $no_premission = $check_dirs_result;
            }

            $check_installed_result = array();
            if (!empty($check_permission_list)) {
                if (!RusUpgrade::checkUpgradePermissions($check_permission_list, true, $check_installed_result)) {
                    $_SESSION['rbpu']['check_permissions'] = "N";
                    $no_premission = array_merge($no_premission, $check_installed_result['non_writable']);
                }
            }

            if ($_SESSION['rbpu']['check_permissions'] == "N") {
                $_SESSION['rbpu']['check_permissions_result'] = $no_premission;
                RusUpgradeMethods::responseNoPermissions();
            }

            fn_redirect('rus_upgrade.rus_step_2');

        } elseif ($step == "rus_step_3" || $action == "rus_step_3") {

            $uc_settings = Settings::instance()->getValues('Upgrade_center');

            fn_ftp_connect($uc_settings);

            if ($action == "rus_step_3") {
                $_SESSION['rbpu']['step'] = "rus_step_3";
            }

            $addons = $rbpu['addons'];
            $schemas = $rbpu['schemas'];
            $all_upgrade_dirs = $rbpu['upgrade_dirs'];
            $conflict_addons = $rbpu['conflict_addons'];
            $conflict_schema = $rbpu['conflict_schema'];
            $conflict_upgrade_dirs = $rbpu['conflict_upgrade_dirs'];

            fn_echo('<b>' . __('rus_connect.upgrade.backup_files') . ' </b><br/>');

            if (!empty($addons)) {
                foreach ($addons as $addon => $info) {

                    // backup files
                    $upgrade_dirs = $all_upgrade_dirs[$addon];
                    $conflict_dirs = isset($conflict_upgrade_dirs[$addon]) ? $conflict_upgrade_dirs[$addon] : '';

                    if (!empty($upgrade_dirs['backup']) && !empty($upgrade_dirs['backup_files'])) {
                        fn_echo(  ' <br/>' . __('rus_connect.upgrade.backup_files'). ' ' . $info['addon_name'] .  ' <br/>');
                        RusUpgrade::copyFiles(
                            $upgrade_dirs['backup'],
                            $upgrade_dirs['backup_files']
                        );
                    }

                    if (!empty($conflict_addons[$addon])) {

                        foreach ($conflict_addons[$addon] as $conflict_addon => $version) {

                            if (!empty($conflict_dirs[$conflict_addon]['backup'])
                                && !empty($conflict_dirs[$conflict_addon]['backup_files'])
                            )
                            {
                                fn_echo(  ' <br/>' . __('rus_connect.upgrade.backup_files_conflict'). ' ' . $conflict_addon . ' ' . $version .  ' <br/>');
                                RusUpgrade::copyFiles(
                                    $conflict_dirs[$conflict_addon]['backup'],
                                    $conflict_dirs[$conflict_addon]['backup_files']
                                );
                            }
                        }
                    }
                }
            } else {
                unset($_SESSION['rbpu']);
                fn_set_notification('E', __('error'), __('rus_connect.upgrade.no_addons'));

                return array(CONTROLLER_STATUS_REDIRECT, "rus_upgrade.upgrade");
            }

            foreach ($addons as $addon => $info) {

                RusUpgrade::execUpgradeFunc($schemas[$addon]['install_src_dir'], 'pre_upgrade');

                if ($info['backup'] == "Y" || !empty($all_upgrade_dirs[$addon]['backup_settings'])) {
                    fn_echo('<br>' . __('rus_connect.upgrade.backup_settings') . ' ' . $info['addon_name'] .  '<br>');
                    RusUpgrade::backupSettings($all_upgrade_dirs[$addon], $addon, $schemas[$addon]);
                }

                if (!empty($conflict_upgrade_dirs[$addon])) {
                    foreach ($conflict_upgrade_dirs[$addon] as $conflict_addon => $dirs) {

                        if (!empty($dirs['backup_settings'])) {
                            fn_echo(__('rus_connect.upgrade.backup_settings') . ' ' . $conflict_addon . '<br>');

                            RusUpgrade::backupSettings($dirs, $conflict_addon , $conflict_schema[$addon][$conflict_addon]);
                        }

                        fn_echo(__('rus_connect.upgrade.uninstall_conflict_addon') . ' ' . $conflict_addon . '<br>');

                        RusUpgrade::uninstall_addon(
                            $conflict_addon,
                            false ,
                            $conflict_schema[$addon][$conflict_addon]['uninstall']
                        );

                        fn_echo(__('rus_connect.upgrade.remove_conflict_files') . ' ' . $conflict_addon . '<br>');

                        if (!empty($dirs['backup'])) {
                            RusUpgradeMethods::removeDirectoryContent($dirs['backup']);
                        }

                    }
                }

                fn_echo(__('rus_connect.upgrade.uninstall_addon') . ' ' . $info['addon_name'] . '<br>');
                RusUpgrade::uninstall_addon($addon, false , $schemas[$addon]['uninstall']);

                if (!empty($all_upgrade_dirs[$addon]['distr']) && !empty($all_upgrade_dirs[$addon]['repo'])) {
                    fn_echo(__('rus_connect.upgrade.update_files') . ' ' . $info['addon_name'] . '<br>');
                    RusUpgrade::updateFiles($all_upgrade_dirs[$addon]);
                }

                if (!empty($all_upgrade_dirs[$addon]['check_dirs']['app'])) {
                    RusUpgrade::restorePermissions($all_upgrade_dirs[$addon]['check_dirs']['app']);
                }

                fn_echo(__('rus_connect.upgrade.install_addon') . ' ' . $info['addon_name'] . '<br>');
                RusUpgrade::install_addon($addon, false, false, $schemas[$addon]['install']);
            }

            $_SESSION['rbpu']['step'] = "final_step";
            fn_redirect('upgrade_center.upgrade_rus.final_step');
        }
    }
}

if ($mode == 'upgrade_rus' and $step == 'final_step' and isset($_SESSION['rbpu'])) {

    $addons = $rbpu['addons'];
    $schemas = $rbpu['schemas'];
    $all_upgrade_dirs = $rbpu['upgrade_dirs'];
    $conflict_addons = $rbpu['conflict_addons'];
    $conflict_schema = $rbpu['conflict_schema'];
    $conflict_upgrade_dirs = $rbpu['conflict_upgrade_dirs'];
    $message_addons = '<br/>';
    $redirect = false;
    if (!empty($addons)) {
        foreach ($addons as $addon => $info) {

            fn_start_scroller();
            fn_echo(__('rus_connect.upgrade.restore_settings') . ' ' . $info['addon_name'] . '<br>');
            fn_ftp_connect(Settings::instance()->getValues('Upgrade_center'));
            fn_echo('.');

            $upgrade_dirs = $all_upgrade_dirs[$addon];
            $schema = $schemas[$addon];

            fn_echo('.');
            // Uninstal addon
            fn_echo(__('rus_connect.upgrade.uninstall_addon') . ' ' . $info['addon_name'] . '<br>');
            RusUpgrade::uninstall_addon($addon, false , isset($schema['uninstall']) ? $schema['uninstall'] : array());

            fn_echo('.');
            // Instal addon
            fn_echo(__('rus_connect.upgrade.install_addon') . ' ' . $info['addon_name'] . '<br>');

            if (!isset($schema['install']['install']) || $schema['install']['install'] != "N") {
                fn_install_addon($addon, false, false);
                fn_echo('.');

                // Restore settings
                if (!empty($all_upgrade_dirs[$addon]['backup_settings'])) {
                    RusUpgrade::restoreSettings($addon, $all_upgrade_dirs[$addon], $auth['user_id'], $schemas[$addon]);
                }

                if (!empty($conflict_upgrade_dirs[$addon])) {
                    foreach ($conflict_upgrade_dirs[$addon] as $conflict_addon => $dirs) {
                        if (!empty($dirs['backup_settings'])) {
                            RusUpgrade::restoreSettings($addon, $conflict_upgrade_dirs[$addon][$conflict_addon], $auth['user_id'], $schema);
                        }
                    }
                }
            }

            if ($addon == "rus_build_pack" && Registry::get('addons.rus_build_pack.check_install') != "Y") {
                $redirect = true;
                $redirect_url = "addons.update&addon=rus_build_pack";
            }

            fn_echo('.');
            fn_echo('<br><b>' . __('congratulations') . '<b><br>');
            $message_addons .= $info['addon_name'] ."<br/>";
            fn_stop_scroller();
        }
    }

    unset($_SESSION['rbpu']);

    $title = __('congratulations');
    $message = __('installer_complete_title').  $message_addons;
    fn_set_notification('I', $title, $message);

    if ($redirect) {
        fn_redirect($redirect_url);
    } else {
        return array(
            CONTROLLER_STATUS_REDIRECT,
            "rus_upgrade.upgrade"
        );
    }
}

function fn_rus_build_get_update_addons($current_addons, $update_addon_selects, &$update_addons, &$addons_versions)
{
    if (!empty($update_addon_selects)) {
        $next_version_info = RusUpgrade::getNextVersionInfo();

        foreach ($update_addon_selects as $addon => $value) {

            if ($value == "N") {
                continue;
            }

            $addons_versions[$addon] = $update_addons[$addon] = array(
                'current_version' => RusUpgradeMethods::checkAddon($current_addons ,$addon),
                'next_version' => $next_version_info['version_list'][$addon],
            );

            $update_addons[$addon]['addon_name'] = $next_version_info['addons_info'][$addon]['name'];
            $update_addons[$addon]['backup'] = $update_addons[$addon]['current_version'] == 0 ? "N" : "Y";
        }
    }

    return true;
}
