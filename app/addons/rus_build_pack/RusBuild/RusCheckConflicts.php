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
use Tygh\Addons\SchemesManager;
use RusBuild\RusUpgradeMethods;

class RusCheckConflicts
{
    final public static function checkAddons($addons, $addon_ids)
    {
        $result = array();

        foreach ($addon_ids as $addon_id) {
            $search_result = in_array($addon_id , $addons);

            if ($search_result) {
                $addon_scheme = SchemesManager::getScheme($addon_id);

                if ($addon_scheme != false && !$addon_scheme->getUnmanaged()) {
                    $result[$addon_id] = $addon_scheme->getVersion();
                }
            }
        }

        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }

    }

    final public static function getSchemas($addons)
    {
        $params = array(
            'conflict_addons' => $addons,
            'cscart_version' => PRODUCT_VERSION,
            'cscart_edition' => PRODUCT_EDITION,
        );

        $response = Http::post(RUS_UPGRADE_GET_FILES . '.get_conflict_schema', array('request' => $params));

        $schema = RusUpgradeMethods::parseResponse($response);

        return $schema;
    }

}
