<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

include_spip('inc/session');
if (!defined("_ECRIRE_INC_VERSION")) return;

	  
	  error_reporting(E_ALL);
// Authentifie via PMB et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_ldap_dist
function auth_pmb_dist ($login, $pass, $md5pass="", $md5next="") {

	spip_log("pmb $login " . ($pass ? "mdp fourni" : "mdp absent"). ($md5pass ? "md5mdp fourni" : "md5mdp absent"));
	//connexion webservices pmb
	include_spip('pmb_mes_fonctions');
	pmb_ws_charger_client($ws, '');
	
	// Securite 
	if (!$login || (!$pass && !$md5pass)) return array();

		     
	 if (!$md5pass AND $pass) {
		$md5pass = md5($pass);
	} 

	// Utilisateur connu ?
	try {
	      //$session_id = $ws->pmbesOPACEmpr_login($login,$pass);
	      $session_id = $ws->pmbesOPACEmpr_login_md5($login,$md5pass);
	      if ($session_id) {
		      // importer les infos depuis pmb, 
		      // avec le statut par defaut a l'install
		      // refuser d'importer n'importe qui 
		      if (!$statut = $GLOBALS['pmb_statut_nouvel_auteur']) return array();
		      
		      if (!$resultpmb = $ws->pmbesOPACEmpr_get_account_info($session_id)) return array();  
		      
		      
		      // Si l'utilisateur figure deja dans la base, y recuperer les infos
		      if ($result = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login) . " AND source='pmb'")) {
			  //mette à jour les infos pmb de l'auteur
			  $m = sql_updateq('spip_auteurs_pmb', array(
				      'pmb_session' => $session_id,
				      'pmb_firstname' => importer_charset($resultpmb->personal_information->firstname, 'utf-8'),
				      'pmb_lastname' => importer_charset($resultpmb->personal_information->lastname, 'utf-8'),
				      'pmb_address_part1' => importer_charset($resultpmb->personal_information->address_part1, 'utf-8'),
				      'pmb_address_part2' => importer_charset($resultpmb->personal_information->address_part2, 'utf-8'),
				      'pmb_address_cp' => importer_charset($resultpmb->personal_information->address_cp, 'utf-8'),
				      'pmb_address_city' => importer_charset($resultpmb->personal_information->address_city, 'utf-8'),
				      'pmb_phone_number1' => importer_charset($resultpmb->personal_information->phone_number1, 'utf-8'),
				      'pmb_phone_number2' => importer_charset($resultpmb->personal_information->phone_number2, 'utf-8'),
				      'pmb_email' => importer_charset($resultpmb->personal_information->email, 'utf-8'),
				      'pmb_birthyear' => importer_charset($resultpmb->personal_information->birthyear, 'utf-8'),
				      'pmb_location_id' => importer_charset($resultpmb->location_id, 'utf-8'),
				      'pmb_location_caption' => importer_charset($resultpmb->location_caption, 'utf-8'),
				      'pmb_adhesion_date' => importer_charset($resultpmb->adhesion_date, 'utf-8'),
				      'pmb_expiration_date' => importer_charset($resultpmb->expiration_date, 'utf-8')),
				      "id_auteur=".$result['id_auteur']);

			    return $result;
		      }
		      
		      // Recuperer les donnees de l'auteur
		      // Convertir depuis UTF-8 (jeu de caracteres par defaut)
		      include_spip('inc/charsets');
		      $nom = importer_charset($resultpmb->personal_information->firstname." ".$result->personal_information->lastname, 'utf-8');
		      $email = importer_charset($resultpmb->personal_information->email, 'utf-8');
		      //$login = strtolower(importer_charset($resultpmb->cb, 'utf-8'));
		      $bio = '';

		      $n = sql_insertq('spip_auteurs', array(
				      'source' => 'pmb',
				      'nom' => $nom,
				      'login' => $login,
				      'email' => $email,
				      'bio' => $bio,
				      'statut' => $statut,
				      'pass' => ''));
		       spip_log("Creation de l'auteur '$nom' dans spip_auteurs id->".$n);
		    
		      //renseigner les infos pmb de l'auteur
		      $m = sql_insertq('spip_auteurs_pmb', array(
				      'id_auteur' => $n,
				      'pmb_session' => $session_id,
				      'pmb_firstname' => importer_charset($resultpmb->personal_information->firstname, 'utf-8'),
				      'pmb_lastname' => importer_charset($resultpmb->personal_information->lastname, 'utf-8'),
				      'pmb_address_part1' => importer_charset($resultpmb->personal_information->address_part1, 'utf-8'),
				      'pmb_address_part2' => importer_charset($resultpmb->personal_information->address_part2, 'utf-8'),
				      'pmb_address_cp' => importer_charset($resultpmb->personal_information->address_cp, 'utf-8'),
				      'pmb_address_city' => importer_charset($resultpmb->personal_information->address_city, 'utf-8'),
				      'pmb_phone_number1' => importer_charset($resultpmb->personal_information->phone_number1, 'utf-8'),
				      'pmb_phone_number2' => importer_charset($resultpmb->personal_information->phone_number2, 'utf-8'),
				      'pmb_email' => importer_charset($resultpmb->personal_information->email, 'utf-8'),
				      'pmb_birthyear' => importer_charset($resultpmb->personal_information->birthyear, 'utf-8'),
				      'pmb_location_id' => importer_charset($resultpmb->location_id, 'utf-8'),
				      'pmb_location_caption' => importer_charset($resultpmb->location_caption, 'utf-8'),
				      'pmb_adhesion_date' => importer_charset($resultpmb->adhesion_date, 'utf-8'),
				      'pmb_expiration_date' => importer_charset($resultpmb->expiration_date, 'utf-8')));
		      spip_log("Creation de l'auteur '$nom' dans spip_auteurs_pmb id->".$m);
		     spip_log("test6");
		       if ($n)	return sql_fetsel("*", "spip_auteurs", "id_auteur=$n");


		      spip_log("Creation de l'auteur '$nom' impossible");
		      $ws->pmbesOPACEmpr_logout(session_get('pmb_session'));
		    
		      
		      return array(); 
		      
	      } else {
		     //utilisateur inconnu
		    return array();  
	      }
	} catch (SoapFault $fault) {
		print("Erreur : ".$fault->faultcode." : ".$fault->faultstring);
		return array();
	}
	return array();
      
}

?>
