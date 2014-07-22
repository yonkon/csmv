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

$schema['top']['addons']['items']['rus_build_menu'] = array(
                    'href' => 'addons.manage&rus_build=Y',
                    'position' => 310,
                    'subitems' => array(
                        'rus_build_menu.rus_addons' => array(
                            'href' => 'addons.manage&rus_build=Y',
                            'position' => 100
                        ),
                        'rus_build_menu.rus_upgrade' => array(
                            'href' => 'rus_upgrade.upgrade',
                            'position' => 200
                        ),
                        'rus_build_menu.activate' => array(
                            'href' => 'rus_upgrade.activate',
                            'position' => 200
                        ),
                    ),
);

return $schema;
