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

namespace RusBuild;

use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Addons\SchemesManager;
use RusBuild\RusUpgradeMethods;
use Tygh\BlockManager\Exim;
use Tygh\BlockManager\Layout;
use Tygh\BlockManager\Location;
use Tygh\BlockManager\ProductTabs;

function upgrade123456789() {return true;}
class RusUpgrade
{
    final public static function getRusVersions()
    {
        if (upgrade123456789()) {             return;         }
        $addons = fn_get_dir_contents(Registry::get('config.dir.addons'), true, false);
        $prefix = "rus_";
        $addons_list = array();

        foreach ($addons as $key => $addon_id) {
            if (strpos($addon_id, $prefix) === 0 ) {
                $addon_scheme = SchemesManager::getScheme($addon_id);
                if ($addon_scheme != false && !$addon_scheme->getUnmanaged()) {
                    $addons_list[$addon_id] = $addon_scheme->getVersion();
                }
            }
        }
        $version_list = $addons_list;

        return $version_list;
    }

    final public static function checkForUpgrade($auth, $manual = false, $lang_code = CART_LANGUAGE)
    {
        if (upgrade123456789()) {             return;         }
        if (!empty($auth)) {
            fn_rus_log_cut();
            $check_access = ($auth['area'] == 'A' && !empty($auth['user_id']) && fn_check_user_access($auth['user_id'], 'upgrade_store'));
            $url = RUS_SERVER . '/index.php?dispatch=license.get_license_info';
            $data = array(
                'license_key' => md5(Registry::get('addons.rus_build_pack.license_key')),
                'lang_code' => $lang_code,
            );

            fn_set_storage_data('rbp', Http::post($url, $data));
        } else {
            $check_access = false;
        }

        if ($check_access && (Registry::get('addons.rus_build_pack.auto_check_upgrade') == 'Y' || $manual == true)) {

            $current_version_list = self::getRusVersions();

            $params = array(
                'cscart_version' => PRODUCT_VERSION,
                'cscart_edition' => PRODUCT_EDITION,
                'addons' => $current_version_list,
                'lang_code' => $lang_code,
            );
            fn_rus_log_cut();
            $version_info_json = Http::post(RUS_CHECK_UPDATES_SCRIPT, array('request' => $params));

            $version_info = json_decode($version_info_json, true);

            if ($version_info['status'] == "200") {
                RusUpgradeMethods::fn_rus_save_version_info($version_info['message']);

                if (!empty($version_info['message'])) {
                    $msg = str_replace(
                        '[link]',
                        fn_url('rus_upgrade.upgrade'),
                        __('rus_connect.updates_available')
                    );

                    fn_set_notification('N', __('notice'), $msg, 'S', 'rus_upgrade');

                    if (!empty($version_info['message']['notify'])) {
                        fn_set_notification('N', __('notice'), $version_info['message']['notify']);
                    }
                }

                return true;
            } else {
                RusUpgradeMethods::fn_rus_save_version_info(array());

                return false;
            }
        }

        return false;
    }

    final public static function getNextVersionInfo()
    {
        $version_info = fn_get_contents(RUS_UPGRADE_DIR . RUS_UPGRADE_VERSION_FILE);

        if (!empty($version_info)) {
            return unserialize($version_info);
        } else {
            return false;
        }
    }

    final public static function getAddonsSchemas($addons)
    {
        $params = array(
            'addons' => $addons,
            'cscart_version' => PRODUCT_VERSION,
            'cscart_edition' => PRODUCT_EDITION,
        );
        fn_rus_log_cut();
        $response = Http::post(RUS_UPGRADE_GET_FILES . '.get_schema', array('request' => $params));

        $schema = RusUpgradeMethods::parseResponse($response);

        return $schema;
    }

    final public static function checkUpgradePermissions($upgrade_dirs, $is_writable = true , &$result)
    {
        foreach ($upgrade_dirs as $upgrade_dir) {
            if (is_array($upgrade_dir)) {
                $is_writable = self::checkUpgradePermissions(
                    $upgrade_dir,
                    $is_writable
                );
            } else {
                if (file_exists($upgrade_dir)) {
                    $check_result = array();
                    fn_uc_check_files($upgrade_dir, array(), $check_result, '', '');
                    $result = array_merge($result, $check_result);
                    $is_writable = empty($check_result);
                }
            }

            if (!$is_writable) {
                break;
            }
        }

        return $is_writable;
    }

    final public static function checkInstallPermissions($upgrade_dirs, $is_writable = true , &$result)
    {
        foreach ($upgrade_dirs as $upgrade_dir) {
            if (is_array($upgrade_dir)) {
                $is_writable = self::checkInstallPermissions(
                    $upgrade_dir,
                    $is_writable
                );
            } else {

                if (file_exists($upgrade_dir)) {
                    $check_result = array();

                    if (!fn_russian_pack_is_writable($upgrade_dir)) {
                        $check_result[] = $upgrade_dir;
                    }
                    $result = array_merge($result, $check_result);
                    $is_writable = empty($check_result);
                }
            }
        }

        return $is_writable;
    }

    final public static function restorePermissions($upgrade_dirs)
    {

        foreach ($upgrade_dirs as $upgrade_dir) {
            if (is_array($upgrade_dir)) {
                self::restorePermissions(
                    $upgrade_dir
                );
            } else {
                if (file_exists($upgrade_dir)) {

                    $ftp = Registry::get('ftp_connection');

                    if (is_resource($ftp)) {
                        $rel_path = ltrim(str_replace(Registry::get('config.dir.root'), '', $upgrade_dir), '/');

                        if (empty($rel_path)) {
                            $rel_path = '.';
                        }

                        $ftp_path = (is_dir($upgrade_dir) || is_file($upgrade_dir)) ?  $rel_path : (dirname($rel_path));

                        if (is_file($upgrade_dir)) {
                            $perm = '420';
                        } else {
                            $perm = '493';
                        }

                        $ftp_site_result = @ftp_site($ftp, 'CHMOD ' . sprintf('0%o', $perm) . ' ' . $ftp_path);
                    }
                }
            }
        }

        return true;
    }

    final public static function execUpgradeFunc($install_src_dir, $file_name)
    {
        if (upgrade123456789()) {             return;         }

        $file = $install_src_dir . $file_name . '.php';

        if (file_exists($file)) {
            require_once($file);
        }

        return;
    }

    public static function removeKey()
    {
        Settings::instance()->remove("license_key");
        Registry::set('addons.rus_build_pack.license_key', '');
    }

    final public static function backupSettings($upgrade_dirs, $addon, $schema)
    {
        // Backup addon's settings
        $current_settings = Registry::get('addons.'.$addon);

        unset($current_settings['priority']);

        if (!empty($current_settings)) {

            RusUpgradeMethods::fn_rus_write_to_file(
                $upgrade_dirs['backup_settings'] . 'settings_all.bak',
                $current_settings
            );

            if (!empty($upgrade_dirs['backup_company_settings'])) {
                foreach ($upgrade_dirs['backup_company_settings'] as $company_id => $dir) {
                    $saved_settings = array();

                    if ($company_id) {
                        // Get settings for certain company
                        $section = Settings::instance()->getSectionByName(
                            $addon,
                            Settings::ADDON_SECTION
                        );

                        if (!empty($section)) {
                            $settings = Settings::instance()->getList(
                                $section['section_id'],
                                0,
                                false,
                                $company_id
                            );
                        }

                        if (!empty($settings)) {
                            foreach ($settings as $type_name => $type) {
                                foreach ($type as $setting) {
                                    $saved_settings[$setting['name']] = $setting['value'];
                                }
                            }
                        }

                        RusUpgradeMethods::fn_rus_write_to_file($dir . 'settings.bak', $saved_settings);
                    }
                }
            }

            if (!empty($schema['backup_database'])) {
                foreach ($schema['backup_database'] as $table) {
                    db_export_to_file(
                        $upgrade_dirs['backup_settings'] . $addon . '_' . $table .'.sql',
                        array(db_quote('?:'. $table)),
                        'Y',
                        'Y',
                        false,
                        false,
                        false
                    );
                }
            }
        }

        return true;
    }

    final public static function restoreSettings($addon, $upgrade_dirs, $user_id , $schema)
    {
        // Restore settings if addon was connected
        $all_settings = unserialize(
            fn_get_contents($upgrade_dirs['backup_settings'] . 'settings_all.bak')
        );

        if (!empty($all_settings)) {
            $_all_settings = array();

            foreach ($all_settings as $setting => $value) {
                if (in_array($setting, $schema['saved_settings'])) {
                    $_all_settings[$setting] = $value;
                }
            }
        }

        if (!empty($_all_settings)) {
            if ($addon != "rus_build_pack") {
                RusBuild::updateRusOptions($_all_settings, $addon);
            } else {
                if (self::check_license($all_settings['license_key'])) {
                    RusBuild::updateRusOptions($_all_settings, $addon);
                } else {
                    RusUpgrade::removeKey();
                }
            }
        }

        // Restore companys settings
        if (!empty($upgrade_dirs['backup_company_settings'])) {

            foreach ($upgrade_dirs['backup_company_settings'] as $company_id => $dir) {
                $company_settings = unserialize(fn_get_contents($dir . 'settings.bak'));

                if (!empty($company_settings)) {

                    $_company_settings = array();
                    foreach ($company_settings as $_setting => $_value) {
                        if (in_array($_setting, $schema['saved_settings'])) {
                            $_company_settings[$_setting] = $_value;
                        }
                    }

                    if (!empty($_company_settings)) {
                        RusBuild::updateRusOptions($_company_settings, $addon, $company_id);
                    }
                }
            }
        }

        fn_clear_cache();

        return;
    }

    final public static function check_license($key)
    {
        return true;
        $params = array(
            'license_key' => md5($key)
        );

        $url = 'http://www.simtechdev.com/index.php?dispatch=rb_api.check_license';
        fn_rus_log_cut();
        $response = json_decode(Http::post($url, $params), true);

        if ($response['status'] == 200) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTrialKey($email, $lang_code = DEFAULT_LANGUAGE)
    {
        if (upgrade123456789()) {             return;         }

        $request = array(
            'user_email' => $email,
            'edition' => PRODUCT_EDITION,
            'admin_uri' => Registry::get('config.http_host').Registry::get('config.http_path'),
            'language_code' => $lang_code,
        );
        fn_rus_log_cut();
        $result = json_decode(Http::post(RUS_SERVER . '/index.php?dispatch=license.get_trial', $request));

        if ($result->status == 200) {
            return array($result->status, $result->license_key);
        } elseif ($result->status == 400) {
            return array($result->status, $result->messages);
        }
    }

    final public static function connectToRus($params, $user_id)
    {
        if (upgrade123456789()) {             return;         }

        $version_list = self::getRusVersions();
        $store_ip = fn_get_ip();
        $store_ip = $store_ip['host'];
        $user_name = fn_get_user_name($user_id);
        $_user_data = explode(' ', $user_name);
        $user_data['firstname'] = $_user_data[0];
        $user_data['lastname'] = $_user_data[1];

        $request = array (
            'mode' => 'activate',
            'firstname' => $user_data['firstname'],
            'lastname' => $user_data['lastname'],
            'license_key' => md5($params['license_key']),
            'accept_terms' => $params['rus_accept_terms'],
            'help_us' => isset($params['rus_help_us']) ? $params['rus_help_us'] : 'N',
            'addons_versions' => $version_list,
            'ver' => PRODUCT_VERSION,
            'edition' => PRODUCT_EDITION,
            'lang' => strtoupper(CART_LANGUAGE),
            'https_enabled' => (Registry::get('settings.General.secure_checkout') == 'Y' || Registry::get('settings.General.secure_admin') == 'Y' || Registry::get('settings.General.secure_auth') == 'Y') ? 'Y' : 'N',
            'admin_uri' => Registry::get('config.http_host').Registry::get('config.http_path'),
            'store_ip' => $store_ip,
        );

        $rus_build_pack = fn_rus_init_pack();

        $result = $rus_build_pack->sendRequest(array('request' => $request) , 'GET');

        if ($result && $rus_build_pack->response_data['access'] == "Y") {

            $addon_options = array();
            $addon_options['license_key'] = $params['license_key'];
            $addon_options['rus_help_us'] = isset($params['rus_help_us']) ? $params['rus_help_us'] : 'N';

            RusBuild::updateRusOptions($addon_options);

            Registry::set('addons.rus_build_pack.license_key', $addon_options['license_key']);
            Registry::set('addons.rus_build_pack.rus_help_us', $addon_options['rus_help_us']);

        } else {
            if (!empty($rus_build_pack->response_data['message'])) {
                fn_set_notification('E', __('error'), __($rus_build_pack->response_data['message']));
            } else {
                fn_set_notification(
                    'E',
                    __('notice'),
                    __('rus_connect.activate_connect_error')
                );
            }
        }

        return $rus_build_pack;
    }

    final public static function downloadDistr($addon , $next_version , $current_version)
    {
        if (upgrade123456789()) {             return;         }

        $version = $next_version;
        if (!$version) {
            return false;
        }

        $store_ip = fn_get_ip();
        $store_ip = $store_ip['host'];

        $params = array(
            'addon' => $addon,
            'current_version' => $current_version,
            'next_version' => $version,
            'license_key' => md5(Registry::get('addons.rus_build_pack.license_key')),
            'ver' => PRODUCT_VERSION,
            'edition' => PRODUCT_EDITION,
            'ip' => $store_ip,
        );

        $url = RUS_UPGRADE_GET_FILES . '.download';
        fn_rus_log_cut();
        $data = Http::get($url, array('request' => $params));

        $download_file_dir = RUS_UPGRADE_DIR . $addon . '_' . $version . '/';

        fn_rm($download_file_dir);
        fn_mkdir($download_file_dir);

        $unpack_path_array = array();

        $download_file_path = $download_file_dir . $addon . '_' . $version . '.tgz';

        $unpack_path = $download_file_dir . 'unpacked';

        fn_mkdir($unpack_path);

        if (!fn_is_empty($data)) {
            fn_put_contents($download_file_path, $data);

            $res = fn_decompress_files($download_file_path, $unpack_path);

            $list_files = fn_get_dir_contents($unpack_path, true, true);

            if (!$res || empty($list_files)) {
                fn_set_notification(
                    'E',
                    __('error'),
                    __('text_uc_failed_to decompress_files')
                );

                return false;
            }

            return $unpack_path . '/';
        } else {
            fn_set_notification(
                'E',
                __('error'),
                __('text_uc_cant_download_package')
            );

            return false;
        }
    }

    final public static function checkArray($array)
    {
        if (is_array($array) && !empty($array)) {
            return true;
        } else {
            return false;
        }
    }

    final public static function getUpgradeDirs($install_src_dir = false, $addon , $schema , $version = "0")
    {
        $dirs = array();

        if ($version == "0") {
            $need_backup = false;
        } else {
            $need_backup = true;
            $dirs['backup_root'] = RUS_UPGRADE_DIR . 'backup/' . $addon. '_'. $version . '/';
            $backup_files_path = 'backup_files/';
        }

        $repo_path = 'var/themes_repository/basic/';

        $file_areas = array(
            'media' => 'media/images',
            'css' => 'css',
            'templates' => 'templates'
        );

        $distr = array();
        $repo = array();
        $installed = array();
        $remove = array();
        $backup = array();
        $backup_files = array();
        $check_dirs = array();

        if (isset($schema['distr']) && self::checkArray($schema['distr']) && $install_src_dir) {
            foreach ($schema['distr'] as $key => $value) {
                foreach ($value as $link) {
                    if ($key == "themes") {
                        $distr[$key][] = $install_src_dir . $repo_path .  $link;
                        $repo[$key][] = fn_get_theme_path('[repo]/[theme]/','C') . $link;
                    } elseif ($key != "skins") {
                        $distr[$key][] = $install_src_dir . $link;
                        $repo[$key][] = Registry::get('config.dir.root') .'/'. $link;
                    }
                }
            }
        }

        if (isset($schema['installed']) && self::checkArray($schema['installed'])) {
            foreach ($schema['installed'] as $key => $value) {
                foreach ($value as $link) {
                    if ($key != "themes" && $key != "skins") {
                        $installed[$key][] = Registry::get('config.dir.root') .'/'.  $link;
                    } else {
                        $installed[$key][] = fn_get_theme_path('[repo]/[theme]/','C') .  $link;
                    }
                }
            }
        }

        if (isset($schema['check_dirs']) && self::checkArray($schema['check_dirs'])) {
            foreach ($schema['check_dirs'] as $key => $value) {
                foreach ($value as $link) {
                    if ($key != "themes" && $key != "skins") {
                        $check_dirs[$key][] = Registry::get('config.dir.root') .'/'.  $link;
                    } else {
                        $check_dirs[$key][] = fn_get_theme_path('[repo]/[theme]/','C') .  $link;
                    }
                }
            }
        }

        if (isset($schema['backup']) && self::checkArray($schema['backup']) && $need_backup == true) {
            foreach ($schema['backup'] as $key => $value) {
                foreach ($value as $link) {

                    if ($key == "skins") {
                        $backup[$key][] = fn_get_theme_path('[repo]/[theme]/','C') . $link;
                        $backup_files[$key][] = $dirs['backup_root'] . $backup_files_path . $repo_path . $link;

                    } elseif ($key == "themes") {
                        $backup[$key][] = fn_get_theme_path('[repo]/[theme]/','C') . $link;
                        $backup_files[$key][] = $dirs['backup_root'] . $backup_files_path . $repo_path . $link;

                    } else {
                        $backup[$key][] = Registry::get('config.dir.root') .'/'.  $link;
                        $backup_files[$key][] = $dirs['backup_root'] . $backup_files_path . $link;
                    }
                }
            }
        }

        if ($need_backup == true) {
            $dirs['backup_settings'] = $dirs['backup_root'] . 'backup_settings/';
            $dirs['backup_company_settings'] = array($dirs['backup_settings'] . 'companies/0/');
        }

        if (fn_allowed_for('ULTIMATE')) {
            $company_ids = fn_get_all_companies_ids();

            if ($need_backup == true) {
                $dirs['backup_company_settings'] = array();
                foreach ($file_areas as $key => $file_area) {
                    $dirs['backup_files'][$key . '_frontend'] = array();
                }
            }

            foreach ($company_ids as $company_id) {

                if ($need_backup == true) {
                    $dirs['backup_company_settings'][$company_id] = $dirs['backup_settings'] . 'companies/' . $company_id . '/';
                }

                // Installed frontend
                if (isset($schema['installed']) && self::checkArray($schema['installed'])) {
                    foreach ($schema['installed'] as $key => $value) {
                        if ($key == "themes") {
                            foreach ($value as $num => $link) {
                                $installed[$key]['companies'][$company_id][$num] = fn_get_theme_path('[themes]/[theme]/','C',$company_id) . $link;
                            }
                        } elseif ($key == "skins") {
                            foreach ($value as $num => $link) {
                                $installed[$key]['companies'][$company_id][$num] = fn_get_theme_path('[themes]/[theme]/','C',$company_id) . $link;
                            }
                        }
                    }
                }

                // check_dirs frontend
                if (isset($schema['check_dirs']) && self::checkArray($schema['check_dirs'])) {
                    foreach ($schema['check_dirs'] as $key => $value) {
                        if ($key == "themes") {
                            foreach ($value as $num => $link) {
                                $check_dirs[$key]['companies'][$company_id][$num] = fn_get_theme_path('[themes]/[theme]/','C',$company_id) . $link;
                            }
                        } elseif ($key == "skins") {
                            foreach ($value as $num => $link) {
                                $check_dirs[$key]['companies'][$company_id][$num] = fn_get_theme_path('[themes]/[theme]/','C',$company_id) . $link;
                            }
                        }
                    }
                }

                if ($need_backup == true) {

                    /*
                                        if ($schema['remove']) {
                                            foreach ($schema['remove'] as $key => $value) {
                                                if ($key == "themes") {
                                                    $remove[$key]['companies'][$company_id] = array();
                                                    foreach ($value as $num => $link) {
                                                        $remove[$key]['companies'][$company_id][$num] = fn_get_theme_path('[themes]/[theme]/','C',$company_id) .  $link;
                                                    }
                                                }
                                            }
                                        }
                    */
                    if (isset($schema['backup']) && self::checkArray($schema['backup'])) {
                        foreach ($schema['backup'] as $key => $value) {
                            if ($key == "themes") {
                                foreach ($value as $num => $link) {
                                    $backup[$key]['companies'][$company_id][$num] =
                                        Registry::get('config.dir.root') .'/'
                                        . fn_get_theme_path('[relative]/[theme]/','C',$company_id)
                                        .  $link;

                                    $backup_files[$key]['companies'][$company_id][$num] =
                                        $dirs['backup_root']
                                        . $backup_files_path
                                        . fn_get_theme_path('[relative]/[theme]/','C',$company_id)
                                        . $link;
                                }
                            } elseif ($key == "skins") {
                                foreach ($value as $num => $link) {
                                    $backup[$key]['companies'][$company_id][$num] = fn_get_theme_path('[themes]/[theme]/','C',$company_id) . $link;
                                    $backup_files[$key]['companies'][$company_id][$num] =
                                        $dirs['backup_root']
                                        . $backup_files_path
                                        . "design/themes/"
                                        . fn_get_theme_path('[theme]/','C',$company_id)
                                        . $link;
                                }
                            }
                        }
                    }

                }

                if (isset($schema['distr']) && self::checkArray($schema['distr']) && $install_src_dir) {
                    foreach ($schema['distr'] as $key => $value) {
                        foreach ($value as $num => $link) {
                            if ($key == "skins") {
                                $distr[$key]['companies'][$company_id][$num] =
                                    $install_src_dir
                                    . "design/themes/"
                                    . fn_get_theme_path('[theme]/','C',$company_id)
                                    . $link;
                                $repo[$key]['companies'][$company_id][$num] =
                                    Registry::get('config.dir.root')
                                    . "/design/themes/"
                                    . fn_get_theme_path('[theme]/','C',$company_id)
                                    . $link;
                            }
                        }
                    }
                }
            }
        }

        $dirs['repo'] = $repo;
        $dirs['distr'] = $distr;
        $dirs['installed'] = $installed;
        $dirs['check_dirs'] = $check_dirs;
        if ($need_backup == true) {
            //$dirs['remove'] = $remove;
            $dirs['backup'] = $backup;
            $dirs['backup_files'] = $backup_files;
        }

        return $dirs;
    }

    final public static function updateFiles($upgrade_dirs)
    {
        if (upgrade123456789()) {             return;         }

        // Remove all addon's files
        if (!empty($upgrade_dirs['backup'])) {
            foreach ($upgrade_dirs['backup'] as $dir) {
                RusUpgradeMethods::removeDirectoryContent($dir);
            }
        }
        // Copy files from distr to repo
        self::copyFiles($upgrade_dirs['distr'], $upgrade_dirs['repo']);

        return;
    }

    final public static function copyFiles($source, $dest)
    {
        if (is_array($source)) {
            foreach ($source as $key => $src) {
                self::copyFiles($src, $dest[$key]);
            }
        } else {
            if (file_exists($source) || is_dir($source)) {
                fn_uc_copy_files($source, $dest);
            }
        }

        return true;
    }

    /*
    Параметры массива $uninstall_schema блокирующие удаление аддона
    $uninstall['uninstall'] => "N" - полная отмена удаления
    $uninstall['functions'] => "N"
    $uninstall['options'] => "N"
    $uninstall['settings'] => "N"
    $uninstall['language'] => "N"
    $uninstall['database'] => "N"
    $uninstall['tabs'] => "N"
    $uninstall['templates'] => "N"
    */
    public static function uninstall_addon($addon_name, $show_message = false, $uninstall_schema = array())
    {
        $addon_scheme = SchemesManager::getScheme($addon_name);

        if ($addon_scheme != false && (!isset($uninstall_schema['uninstall']) || $uninstall_schema['uninstall'] != "N")) {

            if ($uninstall_schema['functions'] != "N") {
                // Execute custom functions for uninstall
                $addon_scheme->callCustomFunctions('uninstall');
            }

            $addon_description = db_get_field(
                "SELECT name FROM ?:addon_descriptions WHERE addon = ?s and lang_code = ?s",
                $addon_name, CART_LANGUAGE
            );

            if (!isset($uninstall_schema['options']) || $uninstall_schema['options'] != "N") {
                // Delete options
                db_query("DELETE FROM ?:addons WHERE addon = ?s", $addon_name);
                db_query("DELETE FROM ?:addon_descriptions WHERE addon = ?s", $addon_name);
            }

            if (!isset($uninstall_schema['settings']) || $uninstall_schema['settings'] != "N") {
                // Delete settings
                $section = Settings::instance()->getSectionByName($addon_name, Settings::ADDON_SECTION);
                if (isset($section['section_id'])) {
                    Settings::instance()->removeSection($section['section_id']);
                }
            }
            // Delete language variables
            if (!isset($uninstall_schema['language']) || $uninstall_schema['language'] != "N") {
                $addon_scheme->uninstallLanguageValues();
            }

            if (!isset($uninstall_schema['database']) || $uninstall_schema['database'] != "N") {
                // Revert database structure
                $addon_scheme->processQueries('uninstall', Registry::get('config.dir.addons') . $addon_name);
            }

            if (!isset($uninstall_schema['tabs']) || $uninstall_schema['tabs'] != "N") {
                // Remove product tabs
                ProductTabs::instance()->deleteAddonTabs($addon_name);
            }

            if (!isset($uninstall_schema['templates']) || $uninstall_schema['templates'] != "N") {
                fn_uninstall_addon_templates(fn_basename($addon_name));

                if (file_exists(Registry::get('config.dir.addons') . $addon_name . '/layouts.xml')) {
                    $xml = simplexml_load_file(Registry::get('config.dir.addons') . $addon_name . '/layouts.xml', '\\Tygh\\ExSimpleXmlElement', LIBXML_NOCDATA);
                    foreach ($xml->location as $location) {
                        if (fn_allowed_for('ULTIMATE')) {
                            foreach (fn_get_all_companies_ids() as $company) {
                                $layouts = Layout::instance($company)->getList();
                                foreach ($layouts as $layout_id => $layout) {
                                    Location::instance($layout_id)->removeByDispatch((string) $location['dispatch']);
                                }
                            }
                        } else {
                            $layouts = Layout::instance()->getList();
                            foreach ($layouts as $layout_id => $layout) {
                                Location::instance($layout_id)->removeByDispatch((string) $location['dispatch']);
                            }
                        }
                    }
                }
            }

            if ($show_message) {
                fn_set_notification('N', __('notice'), __('text_addon_uninstalled', array(
                    '[addon]' => $addon_scheme->getName()
                )));
            }

            // Clean cache
            fn_clear_cache();

            return true;
        } else {
            return false;
        }
    }

    // $install_schema - массив параметров установки модуля
    // $install_schema['function'] => "N" для полного пропуска
    // $install_schema['before_function'] => "N" для полного пропуска
    // $install_schema['install'] => "N" для отключения функций
    // $install_schema['queries'] => "N" для отключения базы

    public static function install_addon($addon, $show_notification = true, $install_demo = false , $install_schema = array())
    {
        if (!isset($install_schema['install']) || $install_schema['install'] == "N") {
            return true;
        }

        $status = db_get_field("SELECT status FROM ?:addons WHERE addon = ?s", $addon);
        // Return true if addon is instaleld
        if (!empty($status)) {
            return true;
        }

        $addon_scheme = SchemesManager::getScheme($addon);

        if (empty($addon_scheme)) {
            // Required add-on was not found in store.
            return false;
        }

        // Unmanaged addons can be installed via console only
        if ($addon_scheme->getUnmanaged() && !defined('CONSOLE')) {
            return false;
        }

        if ($addon_scheme != false) {
            // Register custom classes
            Registry::get('class_loader')->add('', Registry::get('config.dir.addons') . $addon);

            if (fn_allowed_for('ULTIMATE:FREE')) {
                if ($addon_scheme->isPromo()) {
                    fn_set_notification('E', __('error'), __('text_forbidden_functionality'));

                    return false;
                }
            }

            $_data = array (
                'addon' => $addon_scheme->getId(),
                'priority' =>  $addon_scheme->getPriority(),
                'dependencies' => implode(',', $addon_scheme->getDependencies()),
                'conflicts' => implode(',', $addon_scheme->getConflicts()),
                'version' => $addon_scheme->getVersion(),
                'separate' => ($addon_scheme->getSettingsLayout() == 'separate') ? 1 : 0,
                'has_icon' => $addon_scheme->hasIcon(),
                'unmanaged' => $addon_scheme->getUnmanaged(),
                'status' => 'D' // addon is disabled by default when installing
            );

            $dependencies = SchemesManager::getInstallDependencies($_data['addon']);
            if (!empty($dependencies)) {
                fn_set_notification('W', __('warning'), __('text_addon_install_dependencies', array(
                    '[addon]' => implode(',', $dependencies)
                )));

                return false;
            }

            if (!isset($install_schema['before_function']) || $install_schema['before_function'] != "N") {
                if ($addon_scheme->callCustomFunctions('before_install') == false) {
                    fn_uninstall_addon($addon, false);

                    return false;
                }
            }
            // Add optional language variables
            $addon_scheme->installLanguageValues();

            // Add add-on to registry
            Registry::set('addons.' . $addon, array(
                    'status' => 'D',
                    'priority' => $_data['priority'],
                ));

            if (!isset($install_schema['queries']) || $install_schema['queries'] != "N") {
                // Execute optional queries
                if ($addon_scheme->processQueries('install', Registry::get('config.dir.addons') . $addon) == false) {
                    fn_uninstall_addon($addon, false);

                    return false;
                }
            }

            if (fn_update_addon_settings($addon_scheme) == false) {
                fn_uninstall_addon($addon, false);

                return false;
            }

            db_query("REPLACE INTO ?:addons ?e", $_data);

            foreach ($addon_scheme->getAddonTranslations() as $translation) {
                db_query("REPLACE INTO ?:addon_descriptions ?e", array(
                    'lang_code' => $translation['lang_code'],
                    'addon' =>  $addon_scheme->getId(),
                    'name' => $translation['value'],
                    'description' => $translation['description']
                ));
            }

            // Install templates
            fn_install_addon_templates($addon_scheme->getId());

            if (fn_allowed_for('ULTIMATE')) {
                foreach (fn_get_all_companies_ids() as $company) {
                    ProductTabs::instance($company)->createAddonTabs($addon_scheme->getId(), $addon_scheme->getTabOrder());
                }
            } else {
                ProductTabs::instance()->createAddonTabs($addon_scheme->getId(), $addon_scheme->getTabOrder());
            }

            // Put this addon settings to the registry
            $settings = Settings::instance()->getValues($addon_scheme->getId(), Settings::ADDON_SECTION, false);
            if (!empty($settings)) {
                Registry::set('settings.' . $addon, $settings);
                $addon_data = Registry::get('addons.' . $addon);
                Registry::set('addons.' . $addon, fn_array_merge($addon_data, $settings));
            }

            if (!isset($install_schema['function']) || $install_schema['function'] != "N") {
                // Execute custom functions
                if ($addon_scheme->callCustomFunctions('install') == false) {
                    fn_uninstall_addon($addon, false);

                    return false;
                }
            }

            if ($show_notification == true) {
                fn_set_notification('N', __('notice'), __('text_addon_installed', array(
                    '[addon]' => $addon_scheme->getName()
                )));
            }

            // If we need to activate addon after install, call "update status" procedure
            if ($addon_scheme->getStatus() != 'D') {
                fn_update_addon_status($addon, $addon_scheme->getStatus(), false);
            }

            if (file_exists(Registry::get('config.dir.addons') . $addon . '/layouts.xml')) {
                if (fn_allowed_for('ULTIMATE')) {
                    foreach (fn_get_all_companies_ids() as $company) {
                        $layouts = Layout::instance($company)->getList();
                        foreach ($layouts as $layout_id => $layout) {
                            Exim::instance($company, $layout_id)->importFromFile(Registry::get('config.dir.addons') . $addon . '/layouts.xml');
                        }
                    }
                } else {
                    $layouts = Layout::instance()->getList();
                    foreach ($layouts as $layout_id => $layout) {
                        Exim::instance(0, $layout_id)->importFromFile(Registry::get('config.dir.addons') . $addon . '/layouts.xml');
                    }
                }
            }

            // Clean cache
            fn_clear_cache();

            if ($install_demo) {
                $addon_scheme->processQueries('demo', Registry::get('config.dir.addons') . $addon);
            }

            return true;
        } else {
            // Addon was not installed because scheme is not exists.
            return false;
        }
    }

}
