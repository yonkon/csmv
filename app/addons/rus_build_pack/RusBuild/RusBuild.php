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

// rus_build_install

namespace RusBuild;

use Tygh\Http;
use Tygh\Settings;
use Tygh\Registry;

class RusBuild
{
    public $response_doc;
    public $response_data;
    public $errors;

    private $service_url;
    private $license_key;

    public function __construct($service_url = '', $license_key = '')
    {
        $this->service_url = $service_url;
        $this->license_key = $license_key;
    }

    public function sendRequest($params, $method, $lang_code = CART_LANGUAGE)
    {
        $url = $this->service_url;

        if (empty($url)) {
            return false;
        }

        $params_default = array(
            'lang_code' => $lang_code
        );

        fn_rus_log_cut();

        $params = array_merge($params_default, $params);
        $response = Http::post($url, $params);

        $this->response_doc = json_decode($response,true);

        if ($this->response_doc['status'] == 200) {
            $this->response_data['access'] = "Y";
            $this->response_data['message'] = $this->response_doc['message'];
        } else {
            $this->response_data['access'] = "N";
            $this->response_data['message'] = $this->response_doc['message'];

            return false;
        }

        return true;
    }

    /**
     * Get license text from simtechdev.com
     * @param $edition
     * @param $lang_code
     * @return string
     */
    public static function getLicenseText($edition, $lang_code = CART_LANGUAGE)
    {

        $url = RUS_SERVER . '/index.php?dispatch=license.get_license_agreemnet';
        $data = array(
            'edition' => $edition,
            'lang_code' => $lang_code
        );

        $license_agreement = Http::post($url, $data);

        return $license_agreement;
    }

    /**
     * Get license info from simtechdev.com
     * @param $license_key
     * @return array
     */
    public static function getLicenseInfo($license_key)
    {
        static $license_info;

        fn_rus_log_cut();

        if (!isset($license_info)) {
            $license_json = fn_get_storage_data('rbp');
            $license_info = array();

            if (empty($license_json) && !empty($license_key)) {
                $request = array(
                    'license_key' => md5($license_key),
                );

                $license_json = Http::post(RUS_SERVER . '/index.php?dispatch=license.get_license_info', $request);
                fn_set_storage_data('rbp', $license_json);

            } elseif (empty($license_key)) {
                fn_set_storage_data('rbp', null);
            }

            $license_info = json_decode($license_json);
        }

        if (isset($license_info->license_data) && $license_info->license_data->status == 'D') {
            fn_set_notification('N', __('notice'), __('rus_connect.end_trial_notify'));
        }

        if (isset($license_info->status)) {
            return $license_info;
        } else {
            return false;
        }
    }

    public static function updateRusOptions($options, $addon = "rus_build_pack", $company_id = null)
    {
        $section = Settings::instance()->getSectionByName($addon,Settings::ADDON_SECTION);

        if (!$section) {
            $section = Settings::instance()->updateSection(array(
                'parent_id' =>      0,
                'edition_type' =>   'ROOT,ULT:VENDOR',
                'name' =>           $addon,
                'type' =>           Settings::ADDON_SECTION
            ));
        }

        foreach ($options as $option_name => $option_value) {
            $setting_id = Settings::instance()->getId($option_name,$addon);

            if (!$setting_id) {
                $setting_id = Settings::instance()->update(array(
                    'name' =>           $option_name,
                    'section_id' =>     $section['section_id'],
                    'edition_type' =>   'ROOT,ULT:VENDOR',
                    'section_tab_id' => 0,
                    'type' =>           'A',
                    'position' =>       0,
                    'is_global' =>      'N',
                    'handler' =>        ''
                ), null, null, true);
            }

            Settings::instance()->updateValueById($setting_id, $option_value, $company_id ? $company_id : null);
        }

        return;
    }
}
