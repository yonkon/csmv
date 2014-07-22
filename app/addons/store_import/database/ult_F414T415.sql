DROP TABLE IF EXISTS ?:settings_vendor_values_upg;
CREATE TABLE `?:settings_vendor_values_upg` (
  `object_id` mediumint(8) unsigned NOT NULL auto_increment,
  `company_id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `section_name` varchar(128) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`object_id`, `company_id`)
) Engine=MyISAM DEFAULT CHARSET UTF8;

INSERT INTO ?:settings_vendor_values_upg
    SELECT
        ?:settings_objects.object_id,
        company_id,
        ?:settings_objects.name,
        ?:settings_sections.name as section_name,
        ?:settings_vendor_values.value
    FROM ?:settings_objects
    LEFT JOIN ?:settings_sections ON ?:settings_sections.section_id = ?:settings_objects.section_id
    INNER JOIN ?:settings_vendor_values ON ?:settings_vendor_values.object_id = ?:settings_objects.object_id;

DELETE FROM ?:settings_vendor_values WHERE object_id IN (
    SELECT object_id FROM ?:settings_objects WHERE section_id IN (
        SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
    )
);

DROP TABLE IF EXISTS ?:settings_objects_upg;
CREATE TABLE `?:settings_objects_upg` (
  `object_id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `section_name` varchar(128) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`object_id`)
) Engine=MyISAM DEFAULT CHARSET UTF8;

INSERT INTO ?:settings_objects_upg
    SELECT
        ?:settings_objects.object_id,
        ?:settings_objects.name,
        ?:settings_sections.name as section_name,
        ?:settings_objects.value
    FROM ?:settings_objects
    LEFT JOIN ?:settings_sections ON ?:settings_sections.section_id = ?:settings_objects.section_id;

DELETE FROM ?:settings_descriptions WHERE object_type = 'V' AND object_id IN (
    SELECT variant_id FROM ?:settings_variants WHERE object_id IN (
        SELECT object_id FROM ?:settings_objects WHERE section_id IN (
            SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
        )
    )
);

DELETE FROM ?:settings_descriptions WHERE object_type = 'O' AND object_id IN (
    SELECT object_id FROM ?:settings_objects WHERE section_id IN (
        SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
    )
);

DELETE FROM ?:settings_descriptions WHERE object_type = 'S' AND object_id IN (
    SELECT section_id FROM ?:settings_sections WHERE parent_id IN (
        SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
    )
);

DELETE FROM ?:settings_descriptions WHERE object_type = 'S' AND object_id IN (
    SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
);

DELETE FROM ?:settings_variants WHERE object_id IN (
    SELECT object_id FROM ?:settings_objects WHERE section_id IN (
        SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
    )
);

DELETE FROM ?:settings_objects WHERE section_id IN (
    SELECT section_id FROM ?:settings_sections WHERE type = 'ADDON'
);

DELETE s1, s2 FROM ?:settings_sections s1 LEFT JOIN ?:settings_sections as s2 ON s2.parent_id = s1.section_id WHERE s1.type = 'ADDON';
INSERT INTO `?:payment_processors` (processor, processor_script, processor_template, admin_template, callback, type) VALUES ('Alpha Bank', 'alpha_bank.php', 'views/orders/components/payments/cc_outside.tpl', 'alpha_bank.tpl', 'N', 'P') ON DUPLICATE KEY UPDATE `processor_id` = `processor_id`;
REPLACE INTO ?:storage_data VALUES ('addons_snapshots', '51371ebab8cb424c75d867fe73e50bed,6775b3701963b392791fa8c687bac742,d0091c6ed89f0aedd76a40863775277a,da2fd5324f611f2b1d8b4fef9ae3179e,1c80f2768de5b4fb4d2b3944d370cc7a,c69b98dafc2dfacb204cfd71400f3ca8,03121c8182a3b49ee95c327b6d3940b2,235acb56aad0eac7acf7ce56c756115c,12f8524c45544e9bb9448c45bd191081,449a22bd4fd9e552309e9175dec5745e,bd8bc36eb41bc90c585ae7e902e9e284,4c3b118e2c4d898d99f7ed6756f239f0,9beedfe36624c1c064be3382b97f2eb7,bcafeedd7dd058cb267db6bfb7086f27,68249180d0f8ced902a75a5444104dd4,3b8c35e3f8f78f15c6e98f33345ad991,b50e298ae54c7c326d21425c9bc59a39,69fbdd120a7f41fdf04a875302cc9493,90b93be7713dbd6bad07926f7d6eb55f,c06cd01ce149aa26966db5feaccfef6c,eaf45716a98a4bafe872c75c4d245b32,9292d36f62272ba6fc7cd9f3b04f79f9,879494ec811609b65a1d03fdba267b21,952e8cf2c863b8ddc656bac6ad0b729b,5a8b93606dea3db356b524f621e7a8bb,e9741eb2a4ec7d4bc13ce20d13627fc6,7bc397e032bdaae9dca38e5f5452f9a6,a1eff01a6862aea0d5237eb513a378d3,d590327cacc0208d3dcb54fe719e5831,32dc190b81f0b4dd9911972550576baa,281211c4c174214495bd2deb623e9b9e,bf9ad0cf4d2ffc6e54348937e904b667,694779637169a7bc5536f826faa0a05f,da2b534385b751f3fb550c43198dc87c,d9ddf16079b7ba158c82e819d2c363d1,d2e43e8c7123cdf91e4edd3380281d75,aadb0c6e3f30f8db66b89578b82a8a35,c8e43e20a7128fc60f2425a93a0f82c2,b3230f212f048d3087bf992923735b84,0642f2352e66f384142539f5cdd39491,2506ead1700ca25630c8123e5d2a205d,2310732290bbe1cf470b4bc24e008891');
DELETE FROM ?:storage_data WHERE data_key IN ('gift_registry_next_check', 'send_feedback');
