<?php
session_start(); 
$idParticipant = 1;//empty($_SESSION['participant']) ? null : $_SESSION['participant'];
$nomParticipant =  "NonTest";
$prenomParticipant = "prénomTest";
if (!$idParticipant) {
  header('Location : index.php?action=login');
  exit(1);
}
?>

<script type='text/javascript'>

function etatsNbSeanceInscr(statNbInscr) {
 var res="</div><div class='Block'> <div class='cadreBlock' style='width: 40%'>";
 var temp;
 if (statNbInscr == 0) 
  temp="Vous n'êtes inscrit(e) à aucune séance.";
 else if (statNbInscr == 1) 
   temp="Vous êtes inscrit(e) à 1 séance"; 
 else
   temp="Vous êtes inscrit(e) à " + statNbInscr +" séances";
 res += temp;
 res += "</div> </div>";
 return res;
 }


function inscrSeance(idSeance, heureDeb, description, checked, raz) {
    var razInscr = (raz == 'raz') ? '&raz=1' : '';    
    $.ajax({
     type: "POST",
     url: "include/ajaxInscription.php",
     data: "idSeance="+ <?php echo idSeance ?>+"&inscrire="+checked+"&dateHeureDebut="+heureDeb+razInscr, 
     error:function(msg){ 
       alert( "Error ! " + msg);       
     },
     success:function(data){
         //alert(data); return;
       //data = "idSeance;stat dispo/max;idSeance;stat dispo/max" etc.
       // (fonctionnement général pour checkbox et radio bouton)              
       var tabRadio = data.split(";");
       for(var i = 0; i < tabRadio.length-2; i+=2) {                       
             $('#'+tabRadio[i]).html(tabRadio[i+1]);
       }       
       var nbInscr = etatsNbSeanceInscr(tabRadio[tabRadio.length-2]);
       $('#statNbSeanceInscr').html(nbInscr);
       $.gritter.add({
    		// (string | mandatory) the heading of the notification
    		title: ((checked) ? 'Inscription ' : 'Déinscription '),
    		// (string | mandatory) the text inside the notification
    		text:  'Séance de ' + heureDeb + " "+ description //idSeance
    	})
     }
    });
 }  
</script>

<script>
 $(function() {
  // $( "#content" ).accordion();
  $( "#contentBad" ).accordion({ active: false});
 });
</script>


<div id='statNbSeanceInscr'>
<div class="Block">
 <div class='cadreBlock' style='width: 40%'>
<?php if (!$statNbInscr) : ?>
Vous n'êtes inscrit(e) à aucune séance.
<?php elseif ($statNbInscr == 1) : ?>
Vous êtes inscrit(e) à 1 séance 
<?php else: ?>
Vous êtes inscrit(e) à <?php echo ($statNbInscr+0) ?> séances
<?php endif; ?>
</div>
</div>
</div>


<div id="content">

<?php $i=0; $classSeancesParalleles='odd_row'?>
<?php foreach ($lesSeances as $jour => $seances) : ?>
<h3><a href="#"><?php print($jour) ?></a></h3>
<div>
<table class="dataTable">
<thead><tr><th class="dataTableHeader">Je m'inscris</th><th class="dataTableHeader">Disponibilités</th><th class="dataTableHeader">Type</th>
           <th class="dataTableHeader">Horaires</th><th class="dataTableHeader">Seance</th><th class="dataTableHeader">Intervenants</th></tr>
</thead>
<tbody>	 
<?php foreach ($seances as $seancesHoraire) :  ?>
  <?php  $seancesParalleles = count($seancesHoraire) > 1; 
      if ($seancesParalleles) :
        $classSeancesParalleles = ($classSeancesParalleles=='odd_row') ? 'even_row' : 'odd_row';
        $classInput ='radio';
        $i++;
      else:
        $classInput ='checkbox';
      endif; ?>
   <?php $isChecked = false;?>       
  <?php foreach ($seancesHoraire as $seance) :  ?>
   <?php $isChecked |= $seance['idParticipant'];?>
   <?php $oddEvenclass = ($seancesParalleles) ? $classSeancesParalleles : (($oddEvenclass == 'even_row') ? 'odd_row' : 'even_row');?>
   <?php $strClass = ($seance['type']=='atelier') ? '_atelier':''?>
   <tr class='<?php echo $oddEvenclass.$strClass ?>'> 
	 <td>
    <input id='cb<?php print($seance['id']) ?>' 
           type='<?php print($classInput) ?>' 
           name='inscrire<?php echo ($seancesParalleles) ? $i : ''?>' 
           <?php echo ($seance['idParticipant']) ? 'checked' : ' '?> 
           onClick='inscrSeance(<?php print($seance['id'])?>,"<?php print($seance['realDateHeureDebut']) ?>", "<?php print(substr($seance['libelle'],0, 25).'...')?>", this.checked, null )' 
     /> 
    </td>
	 <td id='<?php print($seance['id']) ?>'><?php print($seance['dispo']) ?> / <?php print($seance['nbMax']) ?> </td>
	 <td> <?php print($seance['type']) ?></td>
	 <td><?php print($seance['dateHeureDebut']) ?> - <?php print($seance['dateHeureFin']) ?> </td>
   <td><?php print('('.$seance["numRelatif"] . ') ' . $seance['libelle']) ?> </td>
   <td><?php print($seance['intervenants']) ?> </td>
   <?php $oldIdS = $seance['id']; $oldDateHeureDebut = $seance['dateHeureDebut'];?>
   </tr>
  <?php endforeach; 
  // ajouter une option de raz pour les radios
  if ($seancesParalleles) : ?>
     <tr class='<?php echo $oddEvenclass.$strClass  ?>'> 
	    <td>
    <input id='cbRAZ<?php print($oldIdS) ?>' 
           type='<?php print($classInput) ?>' 
           name='inscrire<?php echo ($seancesParalleles) ? $i : ''?>' 
           <?php echo (!$isChecked) ? 'checked' : ' '?> 
           onClick='inscrSeance(<?php print($oldIdS)?>,"<?php print($seance['realDateHeureDebut']) ?>", "Non merci", false, "raz")' 
     /> 
    </td>
	 <td>Non merci</td>
	 <td> </td>
	 <td><?php echo $oldDateHeureDebut ?></td>
   <td>Non merci</td>
   <td> </td>
  <?php endif;
    // pour etre sur d'alterner lors du retour en mode unique/parallele   
  if ($seancesParalleles) $oddEvenclass = $classSeancesParalleles; else $classSeancesParalleles = $oddEvenclass;
  endforeach; ?>
<tbody>  	 
</table>
</div>
<?php endforeach; ?> 

</div>