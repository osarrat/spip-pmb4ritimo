<?php
include_spip('base/serial');
include_spip('base/create');
include_spip('base/abstract_sql');


global $tables_principales;
global $tables_auxiliaires;
$spip_auteurs_pmb = array(
		"id_auteur_pmb"	=> "bigint(21) NOT NULL auto_increment",
		"id_auteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"pmb_session"	=> "VARCHAR(255) NOT NULL",
		"pmb_firstname"	=> "VARCHAR(255) NOT NULL",
		"pmb_lastname"	=> "VARCHAR(255) NOT NULL",
		"pmb_barcode"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_part1"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_part2"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_cp"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_city"	=> "VARCHAR(255) NOT NULL",
		"pmb_phone_number1"	=> "VARCHAR(255) NOT NULL",
		"pmb_phone_number2"	=> "VARCHAR(255) NOT NULL",
		"pmb_email"	=> "VARCHAR(255) NOT NULL",
		"pmb_birthyear"	=> "VARCHAR(255) NOT NULL",
		"pmb_location_id"	=> "VARCHAR(255) NOT NULL",
		"pmb_location_caption"	=> "VARCHAR(255) NOT NULL",
		"pmb_adhesion_date"	=> "VARCHAR(255) NOT NULL",
		"pmb_expiration_date"	=> "VARCHAR(255) NOT NULL"/*,
		"pmb_twitter"	=> "VARCHAR(255) NOT NULL"*/);


$spip_auteurs_pmb_key = array(
		"PRIMARY KEY"	=> "id_auteur_pmb",
		"KEY id_syndic"	=> "id_auteur");


global $table_primary;
$table_primary['auteurs_pmb']="id_auteur_pmb";

global $table_date;


global $table_des_tables;
$table_des_tables['auteurs_pmb'] = 'auteurs_pmb';

$tables_principales['spip_auteurs_pmb'] =
array('field' => &$spip_auteurs_pmb, 'key' => &$spip_auteurs_pmb_key);


function pmb_install($action){
  switch ($action){
	case 'test':
	  return 1;
	  break;
	case 'install':
		spip_query("CREATE TABLE IF NOT EXISTS ".$GLOBALS['table_prefix']."_auteurs_pmb (
			id_auteur_pmb bigint(21) NOT NULL auto_increment, 
			id_auteur bigint(21) DEFAULT '0' NOT NULL,
			pmb_session  VARCHAR(255) NOT NULL,
			pmb_firstname  VARCHAR(255) NOT NULL,
			pmb_lastname  VARCHAR(255) NOT NULL,
			pmb_barcode  VARCHAR(255) NOT NULL,
			pmb_address_part1  VARCHAR(255) NOT NULL,
			pmb_address_part2  VARCHAR(255) NOT NULL,
			pmb_address_cp  VARCHAR(255) NOT NULL,
			pmb_address_city  VARCHAR(255) NOT NULL,
			pmb_phone_number1  VARCHAR(255) NOT NULL,
			pmb_phone_number2  VARCHAR(255) NOT NULL,
			pmb_email  VARCHAR(255) NOT NULL,
			pmb_birthyear  VARCHAR(255) NOT NULL,
			pmb_location_id  VARCHAR(255) NOT NULL,
			pmb_location_caption  VARCHAR(255) NOT NULL,
			pmb_adhesion_date  VARCHAR(255) NOT NULL,
			pmb_expiration_date  VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id_auteur_pmb),
			KEY id_syndic (id_auteur)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=513 ");

		//spip_query("ALTER TABLE `".$GLOBALS['table_prefix']."_auteurs` ADD `twitter_user` VARCHAR( 255 ) NOT NULL");

		break;
	case 'uninstall':
		break;
  }
}





?>