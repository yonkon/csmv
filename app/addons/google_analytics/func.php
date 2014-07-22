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

use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Gets Google Analytics tracking code
 *
 * @param mixed $company_id Company identifier to get code for
 * @return string Google Analytics tracking code
 */
function fn_google_analytics_get_tracking_code($company_id = null)
{
    if (!fn_allowed_for('ULTIMATE')) {
        $company_id = null;
    }

    return Settings::instance()->getValue('tracking_code', 'google_analytics', $company_id);
}

function fn_google_analytics_change_order_status(&$status_to, &$status_from, &$order_info)
{
    if (Registry::get('addons.google_analytics.track_ecommerce') == 'N') {
        return false;
    }

    $order_statuses = fn_get_statuses(STATUSES_ORDER, array(), true, true);

    if ($order_statuses[$status_to]['params']['inventory'] == 'D' && $order_statuses[$status_from]['params']['inventory'] == 'I') { // decrease amount
        fn_google_anaylitics_send(fn_google_analytics_get_tracking_code($order_info['company_id']), $order_info, false);

    } elseif ($order_statuses[$status_to]['params']['inventory'] == 'I' && $order_statuses[$status_from]['params']['inventory'] == 'D') { // increase amount

        fn_google_anaylitics_send(fn_google_analytics_get_tracking_code($order_info['company_id']), $order_info, true);

    }
}

function fn_google_anaylitics_send($account, $order_info, $refuse = false)
{
    $url = 'http://www.google-analytics.com/collect';
    $sign = ($refuse == true) ? '-' : '';

    //Common data which should be sent with any request
    $required_data = array(
        'v' => '1',
        'tid' => $account,
        'cid' => md5($order_info['email']),
        'ti' => $order_info['order_id'],
        'cu' => $order_info['secondary_currency']
    );

    $transaction = array(
        't' => 'transaction',
        'tr' => $sign . $order_info['total'],
        'ts' => $sign . $order_info['shipping_cost'],
        'tt' => $sign . $order_info['tax_subtotal'],
    );
    $cookies = !empty($order_info['google_analitycs_info']['ga_cookies']) ? $order_info['google_analitycs_info']['ga_cookies'] : fn_ga_prepare_cookies();

    $result = Http::get($url, fn_array_merge($required_data, $transaction, $cookies));

    foreach ($order_info['products'] as $item) {
        $cat_id = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'M'", $item['product_id']);
        $item = array(
            't' => 'item',
            'in' => $item['product'],
            'ip' => fn_format_price($item['subtotal'] / $item['amount']),
            'iq' => $sign . $item['amount'],
            'ic' => $item['product_code'],
            'iv' => fn_get_category_name($cat_id, $order_info['lang_code']),
        );
        $result = Http::get($url, fn_array_merge($required_data, $item));
    }
}

function fn_ga_prepare_cookies()
{
    $result = array();
    $cookies = fn_ga_parse_cookies();
    if (!empty($cookies['other'])) {
        if (!empty($cookies['other']['utmccn'])) {
            $result['cn'] = $cookies['other']['utmccn'];
        }
        if (!empty($cookies['other']['utmcsr'])) {
            $result['cs'] = $cookies['other']['utmcsr'];
        }
        if (!empty($cookies['other']['utmcmd'])) {
            $result['cm'] = $cookies['other']['utmcmd'];
        }
        if (!empty($cookies['other']['utmctr'])) {
            $result['ck'] = $cookies['other']['utmctr'];
        }
    }

    return $result;
}

function fn_ga_parse_cookies()
{
    $result = array(
        'domain_hash' => '',
        'last_update_date' => '',
        'visits_number' => '',
        'resources_number' => '',
        'other' => array(),
    );
    if (isset($_COOKIE['__utmz'])) {
        //Example: [__utmz] => 141674145.1397025347.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)
        $_result = explode('.', $_COOKIE['__utmz']);
        $other = explode('|', $_result[4]);

        $result['domain_hash'] = $_result[0];
        $result['last_update_date'] = $_result[1];
        $result['visits_number'] = $_result[2];
        $result['resources_number'] = $_result[3];

        foreach ($other as $k => $v) {
            $_other = explode('=', $v);
            $result['other'][$_other[0]] = trim($_other[1], '()');
        }
    }

    return $result;
}

function fn_google_analytics_place_order(&$order_id, &$action, &$order_status, &$cart, &$auth)
{
    $data = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = 'A'", $order_id);
    $data = !empty($data) ? unserialize($data) : array();
    $data['ga_cookies'] = fn_ga_prepare_cookies();

    $_data = array (
        'order_id' => $order_id,
        'type' => 'A', //addons information
        'data' => serialize($data),
    );
    db_query("REPLACE INTO ?:order_data ?e", $_data);
}
