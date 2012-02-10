<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function pmb_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['auteurs_pmb']='auteurs_pmb';
	
	return $interface;
}

function pmb_declarer_tables_principales($tables_principales){

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
		"KEY id_auteur"	=> "id_auteur");


    $tables_principales['spip_auteurs_pmb'] = array('field' => &$spip_auteurs_pmb, 'key' => &$spip_auteurs_pmb_key);
    
    return $tables_principales;

}

?>