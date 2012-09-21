<?php
/** 
 * Classe de manipulation des données des séminaires. 
 
 * Destinée aux extractions d'informations sur les séminaires et d'injections de données pour leur création
 * @package default
 * @author ED
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class DataSeminaire{   		
/**
 * Fonction statique qui r�cup�re les inscriptions des s�ances (ateliers, tables rondes ou conf�rences)  destin� � la g�n�ration d'un fichier CSV
 * @return retourne une chaine de caract�res destin�e � �tre affich�e dans un fichier CSV g�n�r� par la page appelante.
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
	 * Fonction statique qui r�cup�re les quantit�s d'inscription des s�ances (ateliers, tables rondes ou conférences) destin� � la g�n�ration d'un fichier CSV
	 * @return retourne une chaine de caract�res destin�e � �tre affich�e dans un fichier CSV g�n�r� par la page appelante.
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
			if ($max == -1) { $max="Illimit�"; $restant = "Illimit�";}
			
			$liste .= ";;;".$max.";".$nbInscrits.";".$restant."\n";
			$creneau = $seances['dateHeureDebut'];
		endforeach;
		return $liste;
	}
}