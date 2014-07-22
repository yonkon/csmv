<?php

// rus_build_install dbazhenov

namespace RusBuild;

use Tygh\Addons\SchemesManager;
use RusBuild\RusUpgrade;

class RusUpgradeMethods
{

    final public static function getBackupDir($addon)
    {
        $_version = RusUpgrade::getNextVersionInfo();
        $version = $_version['version_list'][$addon];

        if (!$version) {
            return false;
        }

        return RUS_UPGRADE_DIR . 'backup/' . $addon. '_'. $version . '/';
    }

    final public static function removeDirectoryContent($path)
    {

        self::removeFiles($path);

        return;
    }

    final private static function removeFiles($source)
    {
        if (is_array($source)) {
            foreach ($source as $src) {
                self::removeFiles($src);
            }
        } else {
            if (is_dir($source)) {
                fn_uc_ftp_rm($source);
            } else {
                fn_uc_rm($source);
            }
        }

        return;
    }

    final public static function fn_rus_save_version_info($version_info)
    {
        self::fn_rus_write_to_file(RUS_UPGRADE_DIR . RUS_UPGRADE_VERSION_FILE, $version_info);
    }

    final public static function fn_rus_write_to_file($file, $data, $serialize = true)
    {
        $dir = dirname($file);
        if (!file_exists($dir)) {
            fn_mkdir($dir);
        }
        $file = fopen($file, 'w');
        fwrite($file, $serialize ? serialize($data) : $data);
        fclose($file);
    }

    public static function responseNoPermissions()
    {
        $error_string = __('rus_connect.upgrade.no_files_permissions');

        fn_echo('<span style="color:red">' . $error_string . '</span><br>');
        $msg = str_replace(
            '[link]',
            fn_url('settings.manage&section_id=Upgrade_center'),
            __('rus_connect.upgrade.no_files_permissions')
        );
        fn_set_notification('N', __('notice'), $msg, 'S', 'rus_upgrade');
        fn_stop_scroller();

        fn_redirect("rus_upgrade.upgrade");
    }

    final public static function checkAddon($addons, $addon_id)
    {
        $result = array();
        if (array_search($addon_id ,$addons)) {
            $addon_scheme = SchemesManager::getScheme($addon_id);
            if ($addon_scheme != false && !$addon_scheme->getUnmanaged()) {
                $result = $addon_scheme->getVersion();

                return $result;
            }
        } else {
            return 0;
        }
    }

    final public static function uniqArray(&$list, $source)
    {
        if (is_array($source)) {
            foreach ($source as $src) {
                self::uniqArray($list, $src);
            }
        } else {
            if (is_array($list)) {
                self::uniq($source, $list);
            }
        }

        return;
    }

    final public static function uniq($value, &$list)
    {
        if (!in_array($value , $list)) {
            $list[] = $value;
        }
    }

    final public static function parseResponse($response)
    {
        if (!empty($response)) {
            $response = json_decode($response,true);

            if ($response['status'] == 200) {
                return $response['message'];
            } else {
                fn_set_notification(
                    'E',
                    __('error'),
                    __($response['message'])
                );

                return false;
            }
        } else {
            fn_set_notification(
                'E',
                __('notice'),
                __('rus_connect.activate_connect_error')
            );

            return false;
        }
    }

}
