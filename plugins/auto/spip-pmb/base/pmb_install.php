<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function pmb_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/pmb');
        include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function pmb_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_auteurs_pmb");
	effacer_meta($nom_meta_base_version);
    /* on efface aussi l'entrée créée par CFG dans spip_meta */
    effacer_meta("spip_pmb");
}

?>