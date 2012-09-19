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
 * Fonction statique qui récupère les inscriptions des séances (ateliers, tables rondes ou conférences)  destiné à la génération d'un fichier CSV
 * @return retourne une chaine de caractères destinée à être affichée dans un fichier CSV généré par la page appelante.
 */
public static function extractInscriptionsSeances($idSemi){
		$liste = "JOURNEE;CRENEAU;SEANCE;PARTICIPANT;ACADEMIE;PRISE EN CHARGE;TITRE;COURRIEL\n";
		$pdoSemi = PdoSeminaire::getInstance();
		$lesSeances = $pdoSemi->getLesAteliers($idSemi);
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
	 * Fonction statique qui récupère les quantités d'inscription des séances (ateliers, tables rondes ou confÃ©rences) destiné à la génération d'un fichier CSV
	 * @return retourne une chaine de caractères destinée à être affichée dans un fichier CSV généré par la page appelante.
	 */
	public static function extractEtatInscriptionsSeances($idSemi){
		$liste = "JOURNEE;CRENEAU;SEANCE;CAPACITE;INSCRITS;PLACES RESTANTES\n";
		$pdoSemi = PdoSeminaire::getInstance();
		$lesSeances = $pdoSemi->getLesAteliers($idSemi);
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
			$nbInscrits = $pdoSemi->getNbInscritsAtelier($seances['id']);;
			$restant = $seances['nbMax'] - $nbInscrits;
			$max = $seances['nbMax'];
			if ($max == -1) { $max="Illimité"; $restant = "Illimité";}
			
			$liste .= ";;;".$max.";".$nbInscrits.";".$restant."\n";
			$creneau = $seances['dateHeureDebut'];
		endforeach;
		return $liste;
	}
}