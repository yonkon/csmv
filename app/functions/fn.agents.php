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
use Tygh\Mailer;
use Tygh\Navigation\LastView;
use Tygh\Session;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


//function fn_generate_guest_password() {
//    return substr(md5(time()), 0, 8);
//}
//
//function fn_get_agent_by_id($id) {
//    $agent = db_query('SELECT * FROM ' .
//        self::table_name() .
//        ' WHERE ' . self::get_primary_key() . ' = ?i', $id
//    );
//    return $agent;
//}