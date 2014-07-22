
ALTER TABLE  `cscart_users` ADD  `midname` VARCHAR( 128 ) NOT NULL;
ALTER TABLE  `cscart_users` ADD  `city` varchar(128) NOT NULL;
ALTER TABLE  `cscart_users` ADD  `curator_id` int(10) unsigned DEFAULT NULL;
ALTER TABLE  `cscart_users` ADD  `agent_contract_id` varchar(32) DEFAULT NULL;