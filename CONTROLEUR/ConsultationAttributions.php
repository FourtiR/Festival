<!-- manque encore le php -->
<?PHP $titre = 'Attribution';

require("./MODELE/Gestion.php"); 
require("./CONTROLEUR/ControlesEtGestionErreurs.inc.php");
// CONNEXION AU SERVEUR MYSQL PUIS SÉLECTION DE LA BASE DE DONNÉES festival 
$connexion = getConnexion();

ob_start ();
// IL FAUT QU'IL Y AIT AU MOINS UN ÉTABLISSEMENT OFFRANT DES CHAMBRES POUR  
// AFFICHER LE LIEN VERS LA MODIFICATION
$nbEtab=obtenirNbEtabOffrantChambres($connexion);
if ($nbEtab!=0)
{
   echo "
   <table width='75%' cellspacing='0' cellpadding='0' align='center'
   <tr><td>
   <a href='modificationAttributions.php?action=demanderModifAttrib'>
   Effectuer ou modifier les attributions</a></td></tr></table><br><br>";
   
   // POUR CHAQUE ÉTABLISSEMENT : AFFICHAGE D'UN TABLEAU COMPORTANT 2 LIGNES 
   // D'EN-TÊTE ET LE DÉTAIL DES ATTRIBUTIONS
   $sql=obtenirReqEtablissementsAyantChambresAttribuées();
   // $rsEtab=mysql_query($req, $connexion);
   $rsEtab=$connexion->query($sql);
   // $lgEtab=mysql_fetch_array($rsEtab);
   $lgEtab=$rsEtab->fetch(PDO::FETCH_ASSOC);
   // BOUCLE SUR LES ÉTABLISSEMENTS AYANT DÉJÀ DES CHAMBRES ATTRIBUÉES
   while($lgEtab!=FALSE)
   {
      $idEtab=$lgEtab['id'];
      $nomEtab=$lgEtab['nom'];
   
      echo "
      <table width='75%' cellspacing='0' cellpadding='0' align='center' 
      class='tabQuadrille'>";
      
      $nbOffre=$lgEtab["nombreChambresOffertes"];
      $nbOccup=obtenirNbOccup($connexion, $idEtab);
      // Calcul du nombre de chambres libres dans l'établissement
      $nbChLib = $nbOffre - $nbOccup;
      
      // AFFICHAGE DE LA 1ÈRE LIGNE D'EN-TÊTE 
      echo "
      <tr class='enTeteTabQuad'>
         <td colspan='2' align='left'><strong>$nomEtab</strong>&nbsp;
         (Offre : $nbOffre&nbsp;&nbsp;Disponibilités : $nbChLib)
         </td>
      </tr>";
          
      // AFFICHAGE DE LA 2ÈME LIGNE D'EN-TÊTE 
      echo "
      <tr class='ligneTabQuad'>
         <td width='65%' align='left'><i><strong>Nom groupe</strong></i></td>
         <td width='35%' align='left'><i><strong>Chambres attribuées</strong></i>
         </td>
      </tr>";
        
      // AFFICHAGE DU DÉTAIL DES ATTRIBUTIONS : UNE LIGNE PAR GROUPE AFFECTÉ 
      // DANS L'ÉTABLISSEMENT       
      $sql=obtenirReqGroupesEtab($idEtab);
      //$rsGroupe=mysql_query($req, $connexion);
      $rsGroupe=$connexion->query($sql);
      //$lgGroupe=mysql_fetch_array($rsGroupe);
      $lgGroupe=$rsGroupe->fetch(PDO::FETCH_ASSOC);
               
      // BOUCLE SUR LES GROUPES (CHAQUE GROUPE EST AFFICHÉ EN LIGNE)
      while($lgGroupe!=FALSE)
      {
         $idGroupe=$lgGroupe['id'];
         $nomGroupe=$lgGroupe['nom'];
         echo "
         <tr class='ligneTabQuad'>
            <td width='65%' align='left'>$nomGroupe</td>";
         // On recherche si des chambres ont déjà été attribuées à ce groupe
         // dans l'établissement
         $nbOccupGroupe=obtenirNbOccupGroupe($connexion, $idEtab, $idGroupe);
         echo "
            <td width='35%' align='left'>$nbOccupGroupe</td>
         </tr>";
         //$lgGroupe=mysql_fetch_array($rsGroupe);
         $lgGroupe=$rsGroupe->fetch(PDO::FETCH_ASSOC);
      } // Fin de la boucle sur les groupes
      
      echo "
      </table><br>";
      //$lgEtab=mysql_fetch_array($rsEtab);
      $lgEtab=$rsEtab->fetch(PDO::FETCH_ASSOC);
   } // Fin de la boucle sur les établissements
}
$contenu = ob_get_clean ();

require 'Vuetemplate.php';

echo $contenu

?>
