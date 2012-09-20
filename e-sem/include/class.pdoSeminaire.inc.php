<?php
/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoSeminaire qui contiendra l'unique instance de la classe
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoSeminaire{
	private static $serveur='mysql:host=127.0.0.1';
	private static $bdd='dbname=bd_seminaire';
  private static $user='seminaire';
  private static $mdp='67vHVdpeWKGvqc';
	private static $monPdo;
	private static $monPdoSeminaire = null;
	/**
	 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
	 * pour toutes les méthodes de la classe
	 */
	private function __construct(){
		PdoSeminaire::$monPdo = new PDO(PdoSeminaire::$serveur.';'.PdoSeminaire::$bdd, PdoSeminaire::$user, PdoSeminaire::$mdp);
		PdoSeminaire::$monPdo->query("SET CHARACTER SET utf8");
		PdoSeminaire::$monPdo->query("SET lc_time_names = 'fr_FR'");
	}
	public function _destruct(){
		PdoSeminaire::$monPdo = null;
		PdoSeminaire::$monPdoSeminaire = null;
	}
	/**
	 * Fonction statique qui crée l'unique instance de la classe
	 * @return l'unique objet de la classe PdoSeminaire
	 */
	public static function getInstance(){
		if(PdoSeminaire::$monPdoSeminaire==null){
			PdoSeminaire::$monPdoSeminaire= new PdoSeminaire();
		}
		return PdoSeminaire::$monPdoSeminaire;
	}
	/**
	 * Retourne les informations sur les ateliers

	 * @return un tableau associatif jour=>creneau=>atelier
	 */
	public function getLesJoursCreneauxAteliers(){
		$tab = array("jour"=>1,"date"=>"20/10/2012",
                                "creneau"=> array(
                                            "num"=>1,"debut"=>"15h30","fin"=>"17h","atelier"=>array(
		1=>"le managenent",
		2=>"le demenagement")
		)
		);
		 
		return $tab;


	}
	
	/**
	 * Retourne les informations sur les academies

	 * @return un tableau d'academie
	 */
	public function getLesAcademies(){
		//$tab= array(1=>"Créteil",2=>"Paris",3=>"Versailles");
		
		$sql = 'SELECT * FROM academie';
		$stmt = self::$monPdo->prepare($sql);
		$stmt->execute();
		$tab = $stmt->fetchAll();
		return $tab;	
	}

	
	public function getSeancesMesBySeminaire($idSeminaire, $idParticipant) {
		$sql = 'SELECT  idSeminaire, idParticipant, id, nbMax - count(idSeance) as dispo, nbMax, type, numRelatif, dateHeureDebut, dateHeureFin, libelle, intervenants FROM seance,inscription WHERE id = idSeance and idPArticipant=:idP group by id having  idSeminaire=:idSem ORDER BY dateHeureDebut, numRelatif';
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':idP', $idParticipant);
		$stmt->bindParam(':idSem', $idSeminaire);
			
		$stmt->execute();
		$tab = $stmt->fetchAll();
		$desSeances = array();
		$curJour = null;
		foreach ($tab as $seance) :
   		$j = self::jourFr(date("N", strtotime($seance['dateHeureDebut'])));
		  $day = $j . ' ' . date("d-m-Y", strtotime($seance['dateHeureDebut']));
		  if ($curJour != $day):
		    $curJour = $day;
		    $heureDeb = null;
		    $desSeances[$curJour] = array();
		  endif;
		  $seance['realDateHeureDebut']= $seance['dateHeureDebut'];
		  $seance['dateHeureDebut']= date("H:i", strtotime($seance['dateHeureDebut']));
		  $seance['dateHeureFin']= date("H:i", strtotime($seance['dateHeureFin']));
		  $heureDeb = $seance['dateHeureDebut'];
    		// les seances sont stockees par jour et heureDeb
		  $desSeances[$curJour][$heureDeb][] = $seance;
		 endforeach;
		 return $desSeances;
	}
	
	public function getSeancesBySeminaire($idSeminaire, $idParticipant) {
		$sql = 'SELECT  idSeminaire, idParticipant, id, nbMax - count(idSeance) as dispo, nbMax, type, numRelatif, dateHeureDebut, dateHeureFin, libelle, intervenants FROM seance left join inscription on id = idSeance and idPArticipant=:idP group by id having  idSeminaire=:idSem ORDER BY dateHeureDebut, numRelatif';
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':idP', $idParticipant);
		$stmt->bindParam(':idSem', $idSeminaire);
			
		$stmt->execute();
		$tab = $stmt->fetchAll();				
		$desSeances = array();
		$curJour = null;
		foreach ($tab as $seance) :
   		$j = self::jourFr(date("N", strtotime($seance['dateHeureDebut'])));
		  $day = $j . ' ' . date("d-m-Y", strtotime($seance['dateHeureDebut']));
		  if ($curJour != $day):
		    $curJour = $day;
		    $heureDeb = null;
		    $desSeances[$curJour] = array();
		  endif;
		  $seance['realDateHeureDebut']= $seance['dateHeureDebut'];
		  $seance['dateHeureDebut']= date("H:i", strtotime($seance['dateHeureDebut']));
		  $seance['dateHeureFin']= date("H:i", strtotime($seance['dateHeureFin']));
		  $heureDeb = $seance['dateHeureDebut'];
    		// les seances sont stockees par jour et heureDeb
		  $desSeances[$curJour][$heureDeb][] = $seance;
		 endforeach;
		 return $desSeances;
		
	}
	
		
	static function jourFr($jour){
		$jours = array('Lundi','Mardi','Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
		return $jours[$jour];
	}

/**
 * 
 * Obtient des informations (nombre d'inscrits...) sur les seances d'un même créneau (dateHeureDebut)
 * @param date_sql $dateHeureDebut
 */
	public function getStatInscriptionSeance($dateHeureDebut){
		$sql = 'SELECT id, nbMax-count(idSeance) AS dispo, nbMax FROM seance LEFT JOIN inscription ON id=idSeance WHERE dateHeureDebut = :dateHeureDeb GROUP BY id'; 		
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':dateHeureDeb', $dateHeureDebut);
		$stmt->execute();
		return $stmt->fetchAll();		
	}
	
	/**
	 * 
	 * Obtient le nombre de séances auxquelles une personne donnée s'est inscrites  
	 * @param int $idParticipant
	 */
	public function getNombreSeancesInscritesBy($idParticipant){
		$sql = 'SELECT count(*) FROM inscription WHERE idParticipant= :idP'; 		
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':idP', $idParticipant);
		$stmt->execute();
		$n = $stmt->fetch();
		return $n[0];
	}
	
	
	/**
	 * 
	 * Inscrit une personne à une séance. Garantit que cette personne
	 * ne sera pas inscrite sur une séance d'un même horaire (heure de debut) 
	 * @param int $idSeance
	 * @param int $idParticipant
	 */
	public function inscriptionSeance($idSeance, $idParticipant) {
		try {
			self::$monPdo->beginTransaction();
  		// supprime toutes les inscriptions du participant à l'heure 
			// de la séance à laquelle il demande l'inscription
			$sql = "DELETE FROM inscription WHERE idParticipant = :idP AND idSeance IN (SELECT id FROM seance WHERE dateHeureDebut IN (SELECT dateHeureDebut FROM seance WHERE id = :idS))";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSeance);
			$stmt->bindParam(':idP', $idParticipant);			
			$stmt->execute();
												
			$sql = "INSERT INTO inscription VALUES (:idP,  :idS)";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSeance);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();
			
			self::$monPdo->commit();
		} catch (Exception $e) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Supprime toutes les inscriptions d'un participant à une date donnée 
	 * @param date_sql $dateHeureDebut
	 * @param int $idParticipant
	 */
	public function deinscriptionSeances($dateHeureDebut, $idParticipant) {
		return $this->razInscriptionSeances($dateHeureDebut, $idParticipant);		
	}
	 
	/**
	 * 
	 * obtient la liste des seances inscrites liées à un participant
	 * @param int $idParticipant le participant concerné
	 */
	public function getListIdSeancesIncrites($idParticipant) {
		try {
			$sql = "SELECT id FROM inscription WHERE idParticipant = :idP";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			return FALSE;
		}
	}
 
	/**
	 * 
	 * Supprime toutes les inscriptions d'un participant à une date donnée 
	 * @param date_sql $dateHeureDebut
	 * @param int $idParticipant
	 */
  public function razInscriptionSeances($dateHeureDebut, $idParticipant) {
		try {			
  		// supprime toutes les inscriptions d'un participant à toutes  
			// les séances d'une heure donnée
			$sql = "DELETE FROM inscription WHERE idParticipant = :idP AND idSeance IN (SELECT id FROM seance WHERE dateHeureDebut = :dhd)";			
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':dhd', $dateHeureDebut);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();
		} catch (Exception $e) {
			return FALSE;
		}
		return TRUE;
  }
  
  
  public function getLesAteliers($idSemi){
  	$tab = array();
  	try {
  		$sql = "SELECT * FROM seance WHERE idSeminaire = :idS order by dateHeureDebut ASC";
  		$stmt = self::$monPdo->prepare($sql);
  		$stmt->bindParam(':idS', $idSemi);
  		$stmt->execute();
  		$tab = $stmt->fetchAll();
  			
  	} catch (Exception $e) {
  		return FALSE;
  	}
  	return $tab;
  }
  
  public function getLesInscrits($atelier, $seminaire){
  	$tab = array();
  	try {
  		$sql = "SELECT participant.nom, participant.prenom, participant.mail, participant.titre, academie.nom acad, participer.priseEnCharge FROM participant, inscription, academie, participer WHERE participant.id = inscription.idParticipant AND idSeance = :IDS AND academie.id = participant.idAcademie AND participer.idParticipant = participant.id AND participer.idSeminaire = :IDSE";
  		$stmt = self::$monPdo->prepare($sql);
  		$stmt->bindParam(':IDS', $atelier);
  		$stmt->bindParam(':IDSE', $seminaire);
  		$stmt->execute();
  		$tab = $stmt->fetchAll();
  	} catch (Exception $e) {
  		return FALSE;
  	}
  	return $tab;
  }
  
  public function getNbInscritsAtelier($idAtelier){
  	$resu = 0;
  	try {
  		$sql = "SELECT count(*) nombre FROM inscription WHERE idSeance = :idS";
  		$stmt = self::$monPdo->prepare($sql);
  		$stmt->bindParam(':idS', $idAtelier);
  		$stmt->execute();
  		$resu = $stmt->fetchObject();
  	} catch (Exception $e) {
  		return FALSE;
  	}
  	return $resu->nombre;
  }
  
  public function getLesSeminaires(){
  	$tab = array();
  	try {  	
  		$sql = "SELECT * from seminaire ORDER BY dateDebut DESC";
  		$stmt = self::$monPdo->prepare($sql);
  		$stmt->execute();
  		$tab = $stmt->fetchAll();
  	} catch (Exception $e) {
  		return FALSE;
  	}
  	//echo var_dump($tab);
  	return $tab;
  }  

  /**
   * Retourne un tableau 
   *  $okUser[0] = $user object, $okUser[1] = cleok boolean
   * @param string $email du participant 
   * @param string $cle d'un formulaire
   */
  public function getUser($email, $cle) {
  	$tab = array();
  	try {
  		$sql = "SELECT * FROM participant WHERE mail=:idM";
  		$stmt = self::$monPdo->prepare($sql);
  		$stmt->bindParam(':idM', $email);  		
  		$stmt->execute();
  		$user = $stmt->fetch(PDO::FETCH_OBJ);
  		$okcle = ($cle == '123') ? true : false; 
  		$tab = array(0=>$user, 1=>$okcle);  		
  	} catch (Exception $e) {
  		return $tab = array(0=>null, 1=>null);
  	}
  	return $tab;
  	 
  }
  
}
