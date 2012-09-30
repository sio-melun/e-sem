<?php
/** 
 * Classe de manipulation des donnÃ©es des sÃ©minaires. 
 
 * DestinÃ©e aux extractions d'informations sur les sÃ©minaires et d'injections de donnÃ©es pour leur crÃ©ation
 * @package default
 * @author ED
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class DataSeminaire{   		
/**
 * Fonction statique qui rï¿½cupï¿½re les inscriptions des sï¿½ances (ateliers, tables rondes ou confï¿½rences)  destinï¿½ ï¿½ la gï¿½nï¿½ration d'un fichier CSV
 * @return retourne une chaine de caractï¿½res destinï¿½e ï¿½ ï¿½tre affichï¿½e dans un fichier CSV gï¿½nï¿½rï¿½ par la page appelante.
 */
public static function extractInscriptionsSeances($idSemi){
		$liste = "JOURNEE;CRENEAU;SEANCE;PARTICIPANT;ACADEMIE;PRISE EN CHARGE;TITRE;COURRIEL\n";
		$pdoSemi = PdoSeminaire::getInstance();
		$lesSeances = $pdoSemi->getLesSeances($idSemi);
		$day = "";
		
		//echo var_dump($lesSeances);
		foreach ($lesSeances as $seance=>$seances) :
			if (substr($seances['dateHeureDebut'],0,10) != $day){
				$j = PdoSeminaire::jourFr(date("N", strtotime($seances['dateHeureDebut'])));
				$day = $j . ' ' . date("d-m-Y", strtotime($seances['dateHeureDebut']));
				$liste .= $day ."\n";
				$creneau = "";
			}
			$day = substr($seances['dateHeureDebut'],0,10);
			if ($creneau != $seances['dateHeureDebut']){
					$liste .= ";".substr($seances['dateHeureDebut'],11,5)."-".substr($seances['dateHeureFin'],11,5)."\n";
			}
			$liste .= ";;".$seances['libelle']."\n";
			$lesInscrits = $pdoSemi->getLesInscrits($seances['id'], $idSemi);
			foreach ($lesInscrits as $inscrit) :
					$liste .= ";;;".$inscrit['nom']." " . $inscrit['prenom'] .";" .$inscrit['acad'].";".$inscrit['priseEnCharge'].";".$inscrit['titre'].";".$inscrit['mail']."\n";
			endforeach;
			$creneau = $seances['dateHeureDebut'];
		endforeach;	
		return $liste;  
	}
	/**
	 * Fonction statique qui rï¿½cupï¿½re les quantitï¿½s d'inscription des sï¿½ances (ateliers, tables rondes ou confÃ©rences) destinï¿½ ï¿½ la gï¿½nï¿½ration d'un fichier CSV
	 * @return retourne une chaine de caractï¿½res destinï¿½e ï¿½ ï¿½tre affichï¿½e dans un fichier CSV gï¿½nï¿½rï¿½ par la page appelante.
	 */
	public static function extractEtatInscriptionsSeances($idSemi){
		$liste = "JOURNEE;CRENEAU;SEANCE;CAPACITE;INSCRITS;PLACES RESTANTES\n";
		$pdoSemi = PdoSeminaire::getInstance();
		$lesSeances = $pdoSemi->getLesSeances($idSemi);
		$day = "";
		foreach ($lesSeances as $seance=>$seances) :
			if (substr($seances['dateHeureDebut'],0,10) != $day){
				$j = PdoSeminaire::jourFr(date("N", strtotime($seances['dateHeureDebut'])));
				$day = $j . ' ' . date("d-m-Y", strtotime($seances['dateHeureDebut']));
				$liste .= $day ."\n";
				$creneau = "";
			}
			$day = substr($seances['dateHeureDebut'],0,10);
			if ($creneau != $seances['dateHeureDebut']){
					$liste .= ";".substr($seances['dateHeureDebut'],11,5)."-".substr($seances['dateHeureFin'],11,5)."\n";
			}
			$liste .= ";;".$seances['libelle']."\n";
			$nbInscrits = $pdoSemi->getNbInscritsSeance($seances['id']);;
			$restant = $seances['nbMax'] - $nbInscrits;
			$max = $seances['nbMax'];
			if ($max == -1) { $max="Illimitï¿½"; $restant = "Illimitï¿½";}
			
			$liste .= ";;;".$max.";".$nbInscrits.";".$restant."\n";
			$creneau = $seances['dateHeureDebut'];
		endforeach;
		return $liste;
	}
	
	/**
	 * Fonction retournant une génération d'un fichier CSV récapitulant les inscrits triés par académie (sera utilisé pour produire un CSV servant à l'impression des fiches parcours individuels).
	 * @param $idSemi identifiant du séminaire dont l'extraction est souhaitée
	 */
	public static function extractTousParticipantParAcad($idSemi){
		$liste = ";;;;;";
		
		// génération de l'entête pour les ateliers
		// récupère toutes les seances
		$pdoSemi = PdoSeminaire::getInstance();
		$lesSeances = $pdoSemi->getLesSeances($idSemi);
		$jourC = "";	// jour Courant dans le parcours des séances
		$heureC = "";	// idem heure courante
		$second = ";;;;;";
		$troisieme = "";
		foreach ($lesSeances as $seance=>$seances) :
			// récup du jour de la séances actuellement analysée
			$jourA = substr($seances['dateHeureDebut'],0,10);
			$heureA = substr($seances['dateHeureDebut'],11,5);
			if ($jourA != $jourC){
				// le jour est écrit dans une colonne
				$liste .= $jourA.";";
				$jourC = $jourA;
			}else{
				$liste .=";";
			}
			// préparation de la seconde ligne
			if ($heureA != $heureC){
				// l'horaire est écrit dans une colonne
				$second .= $heureA.";";
				$heureC = $heureA;
			}else{
				$second .=";";
			}
			// troisième ligne
			$troisieme .= $seances['numRelatif'].";";
		endforeach;
		$liste .= "\n".$second."\n"."ACADEMIE;PARTICIPANT;PRISE EN CHARGE;TITRE;COURRIEL;".$troisieme;
		
		// Chargement des participants
		$lesInscrits = $pdoSemi->getLesInscritsSeminaire($idSemi);
		foreach ($lesInscrits as $inscrit) :
			$liste .= "\n".$inscrit['acad'].";" . $inscrit['nom'] ." " .$inscrit['prenom'].";".$inscrit['priseEnCharge'].";".$inscrit['titre'].";".$inscrit['mail'];
			// pour chaque séance de la liste, on va voir si on a une inscription de cette personne et on affiche 1 si c'est le cas dans la cellule
			foreach ($lesSeances as $seance=>$seances) :
				if ($pdoSemi->estInscritA($inscrit['id'], $seances['id'])){
					$liste .= ";1";
				}else{
					$liste .= ";";
				}
			endforeach;
		endforeach;
		
		return $liste;
	}
}