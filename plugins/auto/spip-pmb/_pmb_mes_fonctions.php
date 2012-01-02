<?php
/*************************************************************************************/
/*                                                                                   */
/*      Portail web pour PMB	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                     */
/*	email : info@openstudio.fr		        	                             	 */
/*      web : http://www.openstudio.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/

include_spip('base/pmb_tables');

$rpc_client = NULL;


function pmb_section_extraire($id_section, $url_base='') {
	$tableau_sections = Array();
	pmb_ws_charger_client($ws, $url_base);
	try {
	      //récupérer les infos sur la section parent
	      $section_parent = $ws->pmbesOPACGeneric_get_section_information($id_section);
	      $tableau_sections[0] = Array();
	      $tableau_sections[0]['section_id'] = $section_parent->section_id;
	      $tableau_sections[0]['section_location'] = $section_parent->section_location;
	      $tableau_sections[0]['section_caption'] = $section_parent->section_caption;
	      $tableau_sections[0]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section_parent->section_image;

	      $tab_sections = $ws->pmbesOPACGeneric_list_sections($id_section);
	      $cpt = 1;
	      if (is_array($tab_sections)) {
			  foreach ($tab_sections as $section) {
				$tableau_sections[$cpt] = Array();
				$tableau_sections[$cpt]['section_id'] = $section->section_id;
				$tableau_sections[$cpt]['section_location'] = $section->section_location;
				$tableau_sections[$cpt]['section_caption'] = $section->section_caption;
				$tableau_sections[$cpt]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section->section_image;

				
				$cpt++;
			  }
	      }
	} catch (Exception $e) {
		 echo 'Exception reçue (1): ',  $e->getMessage(), "\n";
	} 
	return $tableau_sections;
}
function pmb_location_extraire($id_location, $url_base='') {
	$tableau_locationsections = Array();
	pmb_ws_charger_client($ws, $url_base);
	try {
	      $tab_locations = $ws->pmbesOPACGeneric_get_location_information_and_sections($id_location);
	      //récupérer les infos sur la localisation parent
	      $tableau_locationsections[0] = Array();
	      $tableau_locationsections[0]['location_id'] = $tab_locations->location->location_id;
	      $tableau_locationsections[0]['location_caption'] = $tab_locations->location->location_caption;

	      $cpt = 1;
	      if (is_array($tab_locations->sections)) {
		      foreach ($tab_locations->sections as $section) {
			    $tableau_locationsections[$cpt] = Array();
			    $tableau_locationsections[$cpt]['section_id'] = $section->section_id;
			    $tableau_locationsections[$cpt]['section_location'] = $section->section_location;
			    $tableau_locationsections[$cpt]['section_caption'] = $section->section_caption;
			    $tableau_locationsections[$cpt]['section_image'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac").'/'.$section->section_image;

			    
			    $cpt++;
		      }
	      }
	} catch (Exception $e) {
		 echo 'Exception reçue (2) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_locationsections;
}
function pmb_liste_afficher_locations($url_base) {
	$tableau_sections = Array();
	pmb_ws_charger_client($ws, $url_base);
	try {
	      $tab_locations = $ws->pmbesOPACGeneric_list_locations();
	      $cpt = 0;
	      if (is_array($tab_locations)) {
		      foreach ($tab_locations as $location) {
			    $tableau_locations[$cpt] = Array();
			    $tableau_locations[$cpt]['location_id'] = $location->location_id;
			    $tableau_locations[$cpt]['location_caption'] = $location->location_caption;
			    $cpt++;
		      }
	      }
	} catch (Exception $e) {
		 echo 'Exception reçue (3) : ',  $e->getMessage(), "\n";
	}
	return $tableau_locations;
}

function pmb_notices_section_extraire($id_section, $url_base, $debut=0, $fin=5) {
	$tableau_resultat = Array();
	
	$search = array();
	$search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);
			
	pmb_ws_charger_client($ws, $url_base);
	try {	
			$tableau_resultat[0] = Array();
					
			$r=$ws->pmbesOPACAnonymous_advancedSearch($search);
			
			$searchId=$r["searchId"];
			$tableau_resultat[0][' '] = $r["nbResults"];
	    
			 //$r=$ws->pmbesOPACAnonymous_fetchSearchRecords($searchId,$debut,$fin,"serialized_unimarc","utf-8");
			 $r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$fin,"utf-8");
			  $i = 1;
			  if (is_array($r)) {
			      foreach($r as $value) {
					$tableau_resultat[$i] = Array();				
				    
					pmb_ws_parser_notice_array($value, $tableau_resultat[$i]);
					$i++;
			      }
			  }
		

	} catch (Exception $e) {
		 echo 'Exception reçue (4) : ',  $e->getMessage(), "\n";
	} 

	return $tableau_resultat;
}



function pmb_collection_extraire($id_collection, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	pmb_ws_charger_client($ws, $url_base);
	try {
	      $result = $ws->pmbesCollections_get_collection_information_and_notices($id_collection,$id_session);
	      if ($result) {
		  $tableau_resultat['collection_id'] = $result->information->collection_id;
		  $tableau_resultat['collection_name'] = $result->information->collection_name;
		  $tableau_resultat['collection_parent'] = $result->information->collection_parent;
		  $tableau_resultat['collection_issn'] = $result->information->collection_issn;
		  $tableau_resultat['collection_web'] = $result->information->collection_web;
		   $tableau_resultat['notice_ids'] = Array();

		$liste_notices = Array();
		  $cpt=0;
		  if (is_array($result->notice_ids)) {
			      foreach($result->notice_ids as $cle=>$valeur) {
				if (($cpt>=$debut) && ($cpt<$nbresult+$debut)) $liste_notices[] = $valeur;
				$cpt++;
			      }
		  }
		  pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);
		  $tableau_resultat['notice_ids'][0]['nb_resultats'] = $cpt;

		  $cpt=0;
		  if (is_array($liste_notices)) {
			foreach($liste_notices as $notice) {
			    $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
			    $cpt++;
			  }
		  }
		}
	      

	} catch (Exception $e) {
		 echo 'Exception reçue (5) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;
}

function pmb_editeur_extraire($id_editeur, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	pmb_ws_charger_client($ws, $url_base);
	try {
	      $result = $ws->pmbesPublishers_get_publisher_information_and_notices($id_editeur,$id_session);
	      if ($result) {
		  $tableau_resultat['publisher_id'] = $result->information->publisher_id;
		  $tableau_resultat['publisher_name'] = $result->information->publisher_name;
		  $tableau_resultat['publisher_address1'] = $result->information->publisher_address1;
		  $tableau_resultat['publisher_address2'] = $result->information->publisher_address2;
		  $tableau_resultat['publisher_zipcode'] = $result->information->publisher_zipcode;
		  $tableau_resultat['publisher_city'] = $result->information->publisher_city;
		  $tableau_resultat['publisher_country'] = $result->information->publisher_country;
		  $tableau_resultat['publisher_web'] = $result->information->publisher_web;
		  $tableau_resultat['publisher_comment'] = $result->information->publisher_comment;
		   $tableau_resultat['notice_ids'] = Array();

		  $liste_notices = Array();
		  $cpt=0;
		  if (is_array($result->notice_ids)) {
			foreach($result->notice_ids as $cle=>$valeur) {
			  if (($cpt>=$debut) && ($cpt<$nbresult+$debut)) $liste_notices[] = $valeur;
			  $cpt++;
			}
		  }
		  pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);
		  $tableau_resultat['notice_ids'][0]['nb_resultats'] = $cpt;

		  $cpt=0;
		  if (is_array($liste_notices)) {
			foreach($liste_notices as $notice) {
			  $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
			  $cpt++;
			}
		  }
		}
	} catch (Exception $e) {
		 echo 'Exception reçue (6) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;

}

function pmb_auteur_extraire($id_auteur, $debut=0, $nbresult=5, $id_session=0) {
	$tableau_resultat = Array();
	
	pmb_ws_charger_client($ws, $url_base);
	try {
	      $result = $ws->pmbesAuthors_get_author_information_and_notices($id_auteur,$id_session);
	      if ($result) {
		  $tableau_resultat['author_id'] = $result->information->author_id;
		  $tableau_resultat['author_type'] = $result->information->author_type;
		  $tableau_resultat['author_name'] = $result->information->author_name;
		  $tableau_resultat['author_rejete'] = $result->information->author_rejete;
		  if ($result->information->author_rejete) {
		      $tableau_resultat['author_nomcomplet'] =  $tableau_resultat['author_rejete'].' '.$tableau_resultat['author_name'];
		  } else {
		      $tableau_resultat['author_nomcomplet'] = $tableau_resultat['author_name'];
		  }

		  $tableau_resultat['author_see'] = $result->information->author_see;
		  $tableau_resultat['author_date'] = $result->information->author_date;
		  $tableau_resultat['author_web'] = $result->information->author_web;
		  $tableau_resultat['author_comment'] = $result->information->author_comment;
		  $tableau_resultat['author_lieu'] = $result->information->author_lieu;
		  $tableau_resultat['author_ville'] = $result->information->author_ville;
		  $tableau_resultat['author_pays'] = $result->information->author_pays;
		  $tableau_resultat['author_subdivision'] = $result->information->author_subdivision;
		  $tableau_resultat['author_numero'] = $result->information->author_numero;
		  $tableau_resultat['notice_ids'] = Array();

		  $liste_notices = Array();
		  $cpt=0;
		  if (is_array($result->notice_ids)) {
			foreach($result->notice_ids as $cle=>$valeur) {
			  if (($cpt>=$debut) && ($cpt<$nbresult+$debut)) $liste_notices[] = $valeur;
			  $cpt++;
			}
		  }
		  pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);
		   $tableau_resultat['notice_ids'][0]['nb_resultats'] = $cpt;
		  $cpt=0;
		  if (is_array($liste_notices)) {
			foreach($liste_notices as $notice) {
			  $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
			  $cpt++;
			}
		   }
		}
	} catch (Exception $e) {
		 echo 'Exception reçue (7) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;

}

function pmb_recherche_extraire($recherche='', $url_base, $look_ALL='', $look_AUTHOR='', $look_PUBLISHER='', $look_COLLECTION='', $look_SUBCOLLECTION='', $look_CATEGORY='', $look_INDEXINT='', $look_KEYWORDS='', $look_TITLE='', $look_ABSTRACT='', $id_section='', $debut=0, $fin=5, $typdoc='',$id_location='') {
	$tableau_resultat = Array();
	//$recherche = strtolower($recherche);
	$search = array();
	$searchType = 0;	
	$type_recherche=0;

	if ($recherche=='*') $recherche='';
	
	if ($look_ALL) {
		  if ($recherche) $search[] = array("inter"=>"or","field"=>42,"operator"=>"BOOLEAN", "value"=>$recherche);	
		  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
		  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);								
		  if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
	} else {
		if ($look_TITLE) {
			  $searchType = 1;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>1,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_AUTHOR) {
			  $searchType = 2;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>2,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
	    
		if ($look_PUBLISHER) {
			  $searchType = 3;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>3,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_COLLECTION) {
			  $searchType = 4;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>4,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_ABSTRACT) {
			  if ($recherche) $search[] = array("inter"=>"or","field"=>10,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"AND","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
	  
		if ($look_CATEGORY) {
			  $searchType = 6;
			  if ($recherche) $search[] = array("inter"=>"or","field"=>11,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_INDEXINT) {
			  if ($recherche) $search[] = array("inter"=>"or","field"=>12,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}

		if ($look_KEYWORDS) {
			  if ($recherche) $search[] = array("inter"=>"","field"=>13,"operator"=>"BOOLEAN", "value"=>$recherche);
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
		if ((!$look_TITLE) && (!$look_AUTHOR) && (!$look_PUBLISHER) && (!$look_COLLECTION) && (!$look_ABSTRACT) && (!$look_CATEGORY) && (!$look_INDEXINT) && (!$look_KEYWORDS)) {
			  if ($typdoc) $search[] = array("inter"=>"and","field"=>15,"operator"=>"EQ", "value"=>$typdoc);
			  if ($id_section) $search[] = array("inter"=>"and","field"=>17,"operator"=>"EQ", "value"=>$id_section);							if ($id_location) $search[] = array("inter"=>"and","field"=>16,"operator"=>"EQ", "value"=>$id_location);
		}
	}
	pmb_ws_charger_client($ws, $url_base);
	try {	
			$tableau_resultat[0] = Array();
					
			//cas d'une recherche simple 
			if (($look_ALL)&&(!$id_section)&&(!$typdoc)){
			  $r=$ws->pmbesOPACAnonymous_simpleSearch($searchType,$recherche);
			/*} else if (($look_ALL)&&($id_section)&&(!$typdoc)){
			  $r=$ws->pmbesSearch_simpleSearchLocalise($searchType,$recherche,$id_location,$id_section);
			*/} 
			 else {
			 try {
			  $r=$ws->pmbesOPACAnonymous_advancedSearch($search);
			 }catch (Exception $e) {
				 echo 'Exception reçue (8) : ',  $e->getMessage(), "\n";
			}
			 
			}
			$searchId=$r->searchId;
			$tableau_resultat[0]['nb_resultats'] = $r->nbResults;
	    
			$r=$ws->pmbesOPACAnonymous_fetchSearchRecordsArray($searchId,$debut,$fin,"utf-8");
			$i = 1;
			  if (is_array($r)) {
			      foreach($r as $value) {
				    $tableau_resultat[$i] = Array();				
				
				    pmb_ws_parser_notice_array($value, $tableau_resultat[$i]);
				    $i++;
			      }
			  }
		

	} catch (Exception $e) {
		 echo 'Exception reçue (8) : ',  $e->getMessage(), "\n";
	} 

	return $tableau_resultat;
}

function pmb_recuperer_champs_recherche($langue=0) {
	$tresultat = Array();
	
	pmb_ws_charger_client($ws, $url_base);
	try {
	     $result = $ws->pmbesSearch_getAdvancedSearchFields('opac|search_fields',$langue,true);
	     $cpt=0;
	     if (is_array($result)) {
			      foreach ($result as &$res) {
					    $tresultat[$cpt] = Array();
					    $tresultat[$cpt]['id'] = $res->id;
					    $tresultat[$cpt]['label'] = $res->label;
					    $tresultat[$cpt]['type'] = $res->type;
					    $tresultat[$cpt]['operators'] = $res->operators;
					    $tresultat[$cpt]['values'] = Array();
					    $cpt2=0;
					    if (is_array($res->values)) {
						    foreach ($res->values as &$value) {
							$tresultat[$cpt]['values'][$cpt2]['value_id'] = $value->value_id;
							$tresultat[$cpt]['values'][$cpt2]['value_caption'] = $value->value_caption;
							$cpt2++;
						    }
					    }
					    $cpt++;
				}
	      }
	    

	} catch (Exception $e) {
		 echo 'Exception reçue (9) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;
}


function pmb_ws_parser_notice_array($value, &$tresultat) {
	    include_spip("/inc/filtres_images");
	    
	    $indice_exemplaire = 0;
	    $tresultat = Array();
	    $id_notice = $value->id;
	    $authors_700 = array();
	    $authors_701 = array();
	    $authors_702 = array();
	    
	    if (isset($value->f) && is_array($value->f)) {
	    	foreach($value->f as $a_field_f) {
	    		$field_type = $a_field_f->c;
	    		$field_id = $a_field_f->id;
	    		
	    		if (isset($a_field_f->s) && is_array($a_field_f->s)) {
	    			foreach($a_field_f->s as $a_field_s) {
	    				$field_subtype = $a_field_s->c;
	    				$field_value = $a_field_s->value;
	    				
	    				switch($field_type) {
	    					case '010': {
	    						switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['isbn'] .= $field_value;
	    								break;
	    							}
	    							case 'b': {
	    								$tresultat['reliure'] .= $field_value;
	    								break;
	    							}
	    							case 'd': {
	    								$tresultat['prix'] .= $field_value;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '101': {
	    						 switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['langues'] .= $field_value;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '102': {
	    						switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['pays'] .= $field_value;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '200': {
	    						switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['titre'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$field_value))))));
	    								break;
	    							}
	    							 case 'e': {
	    								$tresultat['soustitre'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$field_value))))));
	    								break;
	    							}
	    							case 'f': {
	    								$tresultat['auteur'] .= $field_value;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '210': {
	    						switch($field_subtype) {
	    							case 'c': {
	    								$tresultat['editeur'] .= $field_value;
	    								break;
	    							}
	    							case 'a': {
	    								$tresultat['editeur'] .= ' ('.$field_value.')';
	    								$tresultat['id_editeur'] = $field_id;
	    								break;
	    							}
	    							case 'd': {
	    								$tresultat['annee_publication'] .= $field_value;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '215': {
	    						switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['importance'] .= $field_value;
	    								break;
	    							}
	    							case 'c': {
	    								$tresultat['presentation'] .= $field_value;
	    								break;
	    							}
	    							case 'd': {
	    								$tresultat['format'] .= $field_value;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '225': {
	    						switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['collection'] .= $field_value;
	    								$tresultat['id_collection'] = $field_id;
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '330': {
	    						switch($field_subtype) {
	    							case 'a': {
	    								$tresultat['resume'] .= str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$field_value))))));
	    								break;
	    							}
	    						}
	    						break;
	    					}
	    					case '700': {
	    						switch($field_subtype) {
	    							case 'a': {
										$tresultat['id_auteur'] = $field_id;
										$authors_700[] = "<a href=\"?page=author_see&amp;id=".$field_id."\">".$field_value."</a>";
										$tresultat['lesauteurs'] .= $field_value;
										break;	    								
	    							}
	    							case 'b': {
										$tresultat['lesauteurs'] = $field_value." ".$tresultat['lesauteurs'];
										$tresultat['liensauteurs'] .= " ".$field_value;
										break;	    								
	    							}
	    						}
	    						break;
	    					}
	    					case '701': {
	    						switch($field_subtype) {
	    							case 'a': {
										$tresultat['id_auteur2'] = $field_id;
										$authors_701[] = "<a href=\"?page=author_see&amp;id=".$field_id."\">".$field_value."</a>";
										$tresultat['lesauteurs2'] .= $field_value;
										break;	    								
	    							}
	    							case 'b': {
										$tresultat['lesauteurs2'] = $field_value." ".$tresultat['lesauteurs'];
										$tresultat['liensauteurs2'] .= " ".$field_value;
										break;	    								
	    							}
	    						}
	    						break;
	    					}
	    					case '702': {
	    						switch($field_subtype) {
	    							case 'a': {
										$tresultat['id_auteur3'] = $field_id;
										$authors_702[] = "<a href=\"?page=author_see&amp;id=".$field_id."\">".$field_value."</a>";
										$tresultat['lesauteurs3'] .= $field_value;
										break;	    								
	    							}
	    							case 'b': {
										$tresultat['lesauteurs3'] = $field_value." ".$tresultat['lesauteurs'];
										$tresultat['liensauteurs3'] .= " ".$field_value;
										break;	    								
	    							}
	    						}
	    						break;
	    					}
	    				}

	    			}
	    		}
	    		
	    	}
	    }
	    
	    $tresultat['liensauteurs']=implode(', ', $authors_700);
	    $tresultat['liensauteurs2']=implode(', ', $authors_701);
	    $tresultat['liensauteurs3']=implode(', ', $authors_702);
	    
	    if ($tresultat['lesauteurs'] == "")
		  $tresultat['lesauteurs'] = $tresultat['auteur'];
	     $tresultat['logo_src'] = lire_config("spip_pmb/url","http://tence.bibli.fr/opac")."/getimage.php?url_image=http%3A%2F%2Fimages-eu.amazon.com%2Fimages%2FP%2F!!isbn!!.08.MZZZZZZZ.jpg&noticecode=".str_replace("-","",$tresultat['isbn']);

	    //si pas de numéro isbn (exemple jouets ludothèque) il n'y aura pas de logo
	     if ($tresultat['isbn'] == '') $tresultat['logo_src'] = '';
	     
	    $tresultat['id'] = $id_notice;

	  
}

function pmb_ws_autres_lecteurs($id_notice) {

	$tresultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	
	try {	
	     if ($ws->pmbesOPACGeneric_is_also_borrowed_enabled()) {
		$r=$ws->pmbesOPACGeneric_also_borrowed($id_notice,0);
		$listenotices = Array();
		if (is_array($r)) {
		    if (is_array($r)) {
			foreach ($r as $notice) {
			    $listenotices[] = $notice['notice_id'];
			}
		    }
		}
		if (count($listenotices)>0) {
		      pmb_ws_recuperer_tab_notices ($listenotices, $ws, $tresultat);
		}
	    }
	} catch (Exception $e) {
		 echo 'Exception reçue (10) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;
}
function pmb_ws_documents_numeriques ($id_notice, $id_session=0) {

	$tresultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	
	try {	
		$r=$ws->pmbesNotices_listNoticeExplNums($id_notice, $id_session);
		$cpt = 0;
		if (is_array($r)) {
			foreach ($r as $docnum) {
			    $tresultat[$cpt] = Array();
			    $tresultat[$cpt]['name'] = str_replace("","\"",str_replace("","\"",str_replace("","&oelig;", stripslashes(str_replace("\n","<br />", str_replace("","'",$docnum->name))))));
			    $tresultat[$cpt]['mimetype'] = $docnum->mimetype;
			    $tresultat[$cpt]['url'] = $docnum->url;
			    $tresultat[$cpt]['downloadUrl'] = $docnum->downloadUrl;
			    
			    $cpt++;
		      }
		}

	} catch (Exception $e) {
		 echo 'Exception reçue (11) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;

}

function pmb_ws_dispo_exemplaire($id_notice, $id_session=0) {
  
	$tresultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	
	try {	
	     $r=$ws->pmbesItems_fetch_notice_items($id_notice, $id_session);
	      $cpt = 0;
	      if (is_array($r)) {
			foreach ($r as $exemplaire) {
			    $tresultat[$cpt] = Array();
			    $tresultat[$cpt]['id'] = $exemplaire->id;
			    $tresultat[$cpt]['cb'] = $exemplaire->cb;
			    $tresultat[$cpt]['cote'] = $exemplaire->cote;
			    $tresultat[$cpt]['location_id'] = $exemplaire->location_id;
			    $tresultat[$cpt]['location_caption'] = $exemplaire->location_caption;
			    $tresultat[$cpt]['section_id'] = $exemplaire->section_id;
			    $tresultat[$cpt]['section_caption'] = $exemplaire->section_caption;
			    $tresultat[$cpt]['statut'] = $exemplaire->statut;
			    $tresultat[$cpt]['support'] = $exemplaire->support;
			    $tresultat[$cpt]['situation'] = $exemplaire->situation;
			    
			    $cpt++;
		      }
		}
		

	} catch (Exception $e) {
		 echo 'Exception reçue (12) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;
}

//récuperer une notice en xml via les webservices
function pmb_ws_recuperer_notice ($id_notice, &$ws, &$tresultat) {
	
	try {	
	$listenotices = array(''.$id_notice);
	$tresultat['id'] = $id_notice;
		  $r=$ws->pmbesNotices_fetchNoticeListArray($listenotices,"utf-8",true,false);
		  if (is_array($r)) {
		      foreach($r as $value) {
			      pmb_ws_parser_notice_array($value, $tresultat);
			}
		  }
		

	} catch (Exception $e) {
		 echo 'Exception reçue (13) : ',  $e->getMessage(), "\n";
	} 

	

}
//récuperer une notice en xml via les webservices
function pmb_ws_recuperer_tab_notices ($listenotices, &$ws, &$tresultat) {
	
	
	try {	
	
		  $tresultat['id'] = $id_notice;
		  $r=$ws->pmbesNotices_fetchNoticeListArray($listenotices,"utf-8",true,false);
		  $cpt=0;
		  if (is_array($r)) {
		      foreach($r as $value) {
			    $tresultat[$cpt] = Array();
			    pmb_ws_parser_notice_array($value, $tresultat[$cpt]);
			    $cpt++;
			}
		  }
		

	} catch (Exception $e) {
		 echo 'Exception reçue (14) : ',  $e->getMessage(), "\n";
	} 

	

}

//charger les webservices
function pmb_ws_charger_client(&$ws, $url_base) {
	global $rpc_client;
	if ($rpc_client)
		$ws = $rpc_client;
	try {
		$rpc_type = lire_config("spip_pmb/rpc_type","soap");
		if($rpc_type == "soap") {
			ini_set("soap.wsdl_cache_enabled", "0");
			$ws = new SoapClient(lire_config("spip_pmb/wsdl", "http://tence.bibli.fr/pmbws/PMBWsSOAP_1?wsdl"), array("features" => SOAP_SINGLE_ELEMENT_ARRAYS, 'encoding' => 'iso8859-1'));
		}
		else {
			include_spip('jsonRPCClient');
			$ws = new jsonRPCClient(lire_config("spip_pmb/jsonrpc", ""), false);
		}
		$rpc_client = $ws;
	}
	catch (Exception $e) {
		    echo 'Exception reçue (15) : ',  $e->getMessage(), "\n";
	} 

}
function pmb_ws_liste_tri_recherche() {
	//retourne un tableau contenant la liste des tris possibles
	/* Exemple de retour:
	  Array
	  (
	  [0] => Array
	  (
	  [sort_name] => text_1
	  [sort_caption] => Titre
	  )
	  
	  [1] => Array
	  (
	  [sort_name] => num_2
	  [sort_caption] => Indexation décimale
	  )
	  
	  [2] => Array
	  (
	  [sort_name] => text_3
	  [sort_caption] => Auteur
	  )
	...
      )*/
	$tresultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	
	try {	
	     $tresultat=$ws->pmbesSearch_get_sort_types();
	 
	} catch (Exception $e) {
		 echo 'Exception reçue (16) : ',  $e->getMessage(), "\n";
	} 
	return $tresultat;
}

// retourne un tableau associatif contenant tous les champs d'une notice 
function pmb_notice_extraire ($id_notice, $url_base, $mode='auto') {
	$tableau_resultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	pmb_ws_recuperer_notice($id_notice, $ws, $tableau_resultat);
	return $tableau_resultat;
}


// retourne un tableau associatif contenant tous les champs d'un tableau d'id de notices 
function pmb_tabnotices_extraire ($tabnotices, $url_base, $mode='auto') {
	$tableau_resultat = Array();
	$listenotices = Array();
	pmb_ws_charger_client($ws, $url_base);
	if (is_array($tabnotices)) {
		foreach($tabnotices as $cle=>$valeur){
		    $listenotices[] = $valeur;
		}
	}
	
	pmb_ws_recuperer_tab_notices ($listenotices, $ws, $tableau_resultat);
	return $tableau_resultat;
			
}

// retourne un tableau associatif contenant les prêts en cours
function pmb_prets_extraire ($session_id, $url_base, $type_pret=0) {
	$tableau_resultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	try{
	      $loans = $ws->pmbesOPACEmpr_list_loans($session_id, $type_pret);
	      $liste_notices = Array();
	      $cpt = 0;
	      if (is_array($loans)) {
		foreach ($loans as $loan) {
			$tableau_resultat[$cpt] = Array();
			$tableau_resultat[$cpt]['empr_id'] = $loan->empr_id;
			$liste_notices[] = $loan->notice_id;
			$tableau_resultat[$cpt]['notice_id'] = $loan->notice_id;
			$tableau_resultat[$cpt]['bulletin_id'] = $loan->bulletin_id;
			$tableau_resultat[$cpt]['expl_id'] = $loan->expl_id;
			$tableau_resultat[$cpt]['expl_cb'] = $loan->expl_cb;
			$tableau_resultat[$cpt]['expl_support'] = $loan->expl_support;
			$tableau_resultat[$cpt]['expl_location_id'] = $loan->expl_location_id;
			$tableau_resultat[$cpt]['expl_location_caption'] = $loan->expl_location_caption;
			$tableau_resultat[$cpt]['expl_section_id'] = $loan->expl_section_id;
			$tableau_resultat[$cpt]['expl_section_caption'] = $loan->expl_section_caption;
			$tableau_resultat[$cpt]['expl_libelle'] = $loan->expl_libelle;
			$tableau_resultat[$cpt]['loan_startdate'] = $loan->loan_startdate;
			$tableau_resultat[$cpt]['loan_returndate'] = $loan->loan_returndate;
			
			$cpt++;
		  }
	      }
	      if ($cpt>0) {
		    $tableau_resultat['notice_ids'] = Array();
		    pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);  
	      }
	      $cpt=0;
	      if (is_array($liste_notices)) {
		foreach($liste_notices as $notice) {
		      $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
		      $cpt++;
		  }
	      }
	} catch (Exception $e) {
		 echo 'Exception reçue (17) : ',  $e->getMessage(), "\n";
	} 
	return $tableau_resultat;
			
}

function pmb_reservations_extraire($pmb_session, $url_base) {
	$tableau_resultat = Array();
	pmb_ws_charger_client($ws, $url_base);
	$reservations = $ws->pmbesOPACEmpr_list_resas($pmb_session);
	$liste_notices = Array();
	
	$cpt = 0;
	if (is_array($reservations)) {
		foreach ($reservations as $reservation) {
		      $tableau_resultat[$cpt] = Array();
		      $tableau_resultat[$cpt]['resa_id'] = $reservation->resa_id;
		      $tableau_resultat[$cpt]['empr_id'] = $reservation->empr_id;
		      $tableau_resultat[$cpt]['notice_id'] = $reservation->notice_id;
		      $liste_notices[] = $reservation->notice_id;
		      $tableau_resultat[$cpt]['bulletin_id'] = $reservation->bulletin_id;
		      $tableau_resultat[$cpt]['resa_rank'] = $reservation->resa_rank;
		      $tableau_resultat[$cpt]['resa_dateend'] = $reservation->resa_dateend;
		      $tableau_resultat[$cpt]['resa_retrait_location_id '] = $reservation->resa_retrait_location_id ;
		      $tableau_resultat[$cpt]['resa_retrait_location'] = $reservation->resa_retrait_location;
		  
		      $cpt++;
		}
	}
	if ($cpt>0) {
	      $tableau_resultat['notice_ids'] = Array();
	      pmb_ws_recuperer_tab_notices($liste_notices, $ws, $tableau_resultat['notice_ids']);  
	}
	$cpt=0;
	if (is_array($liste_notices)) {
		foreach($liste_notices as $notice) {
		    $tableau_resultat['notice_ids'][$cpt]['id'] = $notice;
		    $cpt++;
		}
	}
	return $tableau_resultat;

}
function pmb_tester_session($pmb_session, $id_auteur, $url_base) {
	
	//tester si la session pmb est toujours active
	pmb_ws_charger_client($ws, $url_base);
	

	try {
	      if ($ws->pmbesOPACEmpr_get_account_info($pmb_session)) {
	  	return 1;
	      } else {
		 $m = sql_updateq('spip_auteurs_pmb', array(
				      'pmb_session' => ''),
				      "id_auteur=".$id_auteur);
		return 0;
	      }

	} catch (Exception $e) {
		$m = sql_updateq('spip_auteurs_pmb', array(
				      'pmb_session' => ''),
				      "id_auteur=".$id_auteur);
		return 0;
	}
}
function pmb_reserver_ouvrage($session_id, $notice_id, $bulletin_id, $location, $url_base) {
	pmb_ws_charger_client($ws, $url_base);
	$result= Array();

	$result = $ws->pmbesOPACEmpr_add_resa($session_id, $notice_id, $bulletin_id, $location);

	if (!$result->success) {
	    if ($result->error == "no_session_id") return "La réservation n'a pas pu être réalisée pour la raison suivante : pas de session";

	    else if ($result->error == "no_empr_id") return "La réservation n'a pas pu être réalisée pour la raison suivante : pas d'id emprunteur";
	    else if ($result->error == "check_empr_exists") return "La réservation n'a pas pu être réalisée pour la raison suivante : id emprunteur inconnu";
	    else if ($result->error == "check_notice_exists") return "La réservation n'a pas pu être réalisée pour la raison suivante : Notice inconnue";
	    else if ($result->error == "check_quota") return "La réservation n'a pas pu être réalisée pour la raison suivante : violation de quotas: Voir message complémentaire";
	    else if ($result->error == "check_resa_exists") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document déjà réservé par ce lecteur";
	    else if ($result->error == "check_allready_loaned") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document déjà emprunté par ce lecteur";
	    else if ($result->error == "check_statut") return "La réservation n'a pas pu être réalisée pour la raison suivante : Pas de document prêtable";
	    else if ($result->error == "check_doc_dispo") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document disponible, mais non réservable";
	    else if ($result->error == "check_localisation_expl") return "La réservation n'a pas pu être réalisée pour la raison suivante : Document non réservable dans les localisations autorisées";
	    else if ($result->error == "resa_no_create") return "La réservation n'a pas pu être réalisée pour la raison suivante : échec de l'enregistrement de la résevation";
	    else return "La réservation n'a pas pu être réalisée pour la raison suivante : ".$result->error;
	} else return "Votre réservation a été enregistrée";
/* Description des entrées:

      session_id type string        Le numéro de session
      notice_id type integer        l'id de la notice
      bulletin_id type integer        l'id du bulletin
      location type integer        la localisation de retrait ni applicable
      Description des retours:

      success type boolean        Un boolean indiquant le succès éventuel de l'opération
      error type string        Code d'erreur si la réservation n'est pas effectuée:
      no_session_id (pas de session)
      no_empr_id (pas d'id emprunteur)
      check_empr_exists (id emprunteur inconnu)
      check_notice_exists (Notice inconnue)
      check_quota (violation de quotas: Voir message complémentaire)
      check_resa_exists (Document déjà réservé par ce lecteur)
      check_allready_loaned (Document déjà emprunté par ce lecteur)
      check_statut (Pas de document prêtable)
      check_doc_dispo (Document disponible, mais non réservable)
      check_localisation_expl (Document non réservable dans les localisations autorisées)
      resa_no_create (échec de l'enregistrement de la résevation)
      message type string        Message d'information complémentaire
*/
}

function pmb_notice_champ ($tableau_resultat, $champ) {
	return $tableau_resultat[$champ];
}
function pmb_tableau2_valeur ($tableau_resultat, $indice1, $indice2) {
	return $tableau_resultat[$indice1][$indice2];
}
/*mettre le champ de recherche au format de pmb */
function pmb_prepare_recherche ($recherche) {
	$recherche = str_replace("+"," ",$recherche);
	return $recherche;
}

/* fonction str_replace avec l'ordre des parametres compatible spip */
function pmb_remplacer ($chaine, $p1, $p2) {
	return str_replace($p1,$p2,$chaine);
}
function contient($texte, $findme) {
	return (strpos($texte, $findme) !== false);
}
function extraire_attribut_url($url,$attribut) {
		if ($url) {
		  preg_match('`'.$attribut.'=[0-9]+$`',$url, $result);		
		  return(substr($result[0], 3));
		}
		return '';
}

?>
