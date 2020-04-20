<?php
require_once("./menu.php");
$menu = affiche_menu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Lieux</title>
    <link href="absences.css" rel="stylesheet" type="text/css">
    <link href="css/menu.css" type="text/css" rel="stylesheet" />
</head>
<body>
<?php
echo $menu;
include('db_fonction.php');
$base_id = connecti_db();
$query = "SELECT * FROM absences,personnel,lieux WHERE personnel.PRID = ".$_GET['id']." AND absences.LID = lieux.LID AND absences.PRID = ".$_GET['id']." ORDER BY date_saisie";
$liste = mysqli_query($base_id,$query);
?>
<div class='corps'>
</br>
<table cellpadding=0 cellspacing=0>
<tr>
<th rowspan="3" width = 150px><img src="ico/menu.gif"></th>
<th colspan="7" class="TDligne2">

<?php echo mysqli_num_rows($liste); ?> enregistrements&nbsp;
</th>
</tr>
<tr>
<th width = 130px BGCOLOR="#99CCFF" colspan='2' rowspan='2'>Date</a></th>
<th width = 400px BGCOLOR="#99CCFF">Nom</th>
<th width = 130px BGCOLOR="#99CCFF">Discipline</th>
<th width = 80px BGCOLOR="#99CCFF">Categorie</th>
<th width = 150px BGCOLOR="#99CCFF">Examen</th>
<th rowspan='2' BGCOLOR="#99CCFF">Date de saisie</th>
</tr>
<tr>
<th class="TDligne2">Details</th>
<th class="TDligne2">Ordonateur</th>
<th class="TDligne2" colspan='2'>Lieux</th>
</tr>


<?php //autre lignes du tableau
for($j=0;$j<mysqli_num_rows($liste);$j++) // enumere les absences
{
    $tab_jours = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
    $abID = mysqli_result($liste,$j,"ABID");
    $date_saisie = mysqli_result($liste,$j,"date_saisie");
    $date_saisie = date("d/m/Y", strtotime($date_saisie));
    $date_debut = mysqli_result($liste,$j,"date_debut");
    $date_fin = mysqli_result($liste,$j,"date_fin");
    $date = explode(" ", $date_debut);
    $date_debut = $date[1];
    $date = $date[0];
    $date_fin = explode(" ", $date_fin);
    $date_fin = $date_fin[1];
    $tranche = '';
    if ($date_debut == '08:00:00')
    {
        if($date_fin == '13:00:00')
            $tranche = 'Matin';
        elseif ($date_fin == '18:00:00')
            $tranche = 'Journee';
    }
    elseif ($date_debut == '13:00:00' && $date_fin == '18:00:00')
        $tranche = 'Apres-Midi';
    $date_debut=substr($date_debut, 0, 5);
    $date_fin=substr($date_fin, 0, 5);
    $date_debut=str_replace(':','h',$date_debut);
    $date_fin=str_replace(':','h',$date_fin);
    $ymd = explode("-", $date);
    $jour = $tab_jours[date('w', mktime(0,0,0,date($ymd[2]),date($ymd[1]),date($ymd[0])))];
    $categorie = mysqli_result($liste,$j,"categorie");
    $examen = mysqli_result($liste,$j,"examen");
    $details = mysqli_result($liste,$j,"details");
    $ordonateur = mysqli_result($liste,$j,"ordonateur");
    $nom = mysqli_result($liste,$j,"nom");
    $prenom = mysqli_result($liste,$j,"prenom");
    $poste = mysqli_result($liste,$j,"poste");
    $discipline = mysqli_result($liste,$j,"discipline");
    $nom_lieu = mysqli_result($liste,$j,"nom_lieu");
    $interne = mysqli_result($liste,$j,"interne");
    $capacite = mysqli_result($liste,$j,"capacite");


    if (mysqli_result($liste,$j,"categorie") == "Examen") $TDclass = "class='TDexamen '";
    elseif (mysqli_result($liste,$j,"categorie") == "Stage") $TDclass = "class='TDstage '";
    elseif (mysqli_result($liste,$j,"categorie") == "Maladie") $TDclass = "class='TDmaladie '";
    elseif (mysqli_result($liste,$j,"categorie") == "Autre") $TDclass = "class='TDautre '";


    // informations sur l'absence
?>
<tr>
    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
</tr>
<tr>
    <td>
    <input border=0 src="ico/supp.gif" type=image onClick="javascript:window.location='absences.php?action=1&id=<?php echo $abID ?>';"  >
    <input border=0 src="ico/edit.gif" type=image onClick="javascript:window.location='absences_new.php';"  >
    <input border=0 src="ico/tv.gif" type=image onClick="javascript:window.location='absences_new.php';"  >
    </td>
    <td id=<?php echo $categorie ?> class="top_left" colspan='2' align='center'>
        <?php echo "$jour $ymd[2]/$ymd[1]/$ymd[0]" ?>
    </td>
    <td id=<?php echo $categorie ?> class="top">
        <b><?php echo "$nom $prenom"?></b>
    </td>
    <td id=<?php echo $categorie ?> class="top">
        <?php echo "$poste $discipline"?>
    </td>
    <td id=<?php echo $categorie ?> class="top">
        <?php echo "$categorie"?>
    </td>
    <td id=<?php echo $categorie ?> class="top">
        <?php echo "$examen"?>
    </td>
    <td rowspan='2' id=<?php echo $categorie ?> class="top_right" align='middle'>
        <?php echo "$date_saisie"?>
    </td>
</tr>

<tr>
    <td>
    </td>
<?php
    if ($tranche != '')
        echo "<td class='bottom_left' colspan='2'>$tranche</td>";
    else
        echo "<td class='bottom_left'>$date_debut</td><td class='bottom'>$date_fin</td>"
?>
    <td class="bottom">
        <?php echo "$details"?>
    </td>
    <td class="bottom">
        <?php echo "$ordonateur"?>
    </td>
    <td class="bottom" colspan='2'>
        <?php echo "$nom_lieu"?>
    </td>
</tr>

<?php
}
?>
</table>

</div>
</body>
</html>

