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

// rus_build_pack dbazhenov

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_define('CRON_IMPORT_KEY_LENGTH', 8);
fn_define('SYMBOL_RUBL', '<span class="b-rub">Р</span>');
fn_define('SYMBOL_RUBL_TEXT', ' Руб.');
fn_define('CURRENCY_RUB', 'RUB');

fn_define('RUS_SERVER', 'https://updates.cs-cart.ru');
fn_define('RUS_UPGRADE_DIR', Registry::get('config.dir.root') . '/var/rus_build_pack/');
fn_define('RUS_UPGRADE_VERSION_FILE', 'version_info.txt');
fn_define('RUS_UPGRADE_GET_FILES', RUS_SERVER . '/index.php?dispatch=rus_upgrade.get_files');
fn_define('RUS_CHECK_UPDATES_SCRIPT', RUS_SERVER . '/index.php?dispatch=rus_upgrade.check_updates');

