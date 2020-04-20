<?php
require_once("./menu.php");
$menu = affiche_menu();
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Edition des absences</title>
<script type="text/javascript" src="js/edit_personnel.js"></script>
<script type="text/javascript" src="js/add_personnel.js"></script>
<script type="text/javascript" src="js/bsn.AutoSuggest_c_2.0.js"></script>
    <script type="text/javascript" src="js/completion.js"></script>
 <link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
            <link href="absences.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" type="text/css" rel="stylesheet" />
        </head>
<body>
<?php
echo $menu;
?>
<div class='corps'>

<?php
$options = array();
include('db_fonction.php');
$base_id = connecti_db();
if(ISSET($_GET['action']))
{
    if ($_GET['action'] == 1)
    {
        if(!isset($_GET['confirm']))
        {
?>
            <script type="text/javascript">
            var answer = confirm ("Confirmer la suppression ?")
                if (answer)
                    window.location='<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>' + '&confirm=1';
                else
                    window.location='<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>' + '&confirm=0';
            </script>
<?php
        }
        if(isset($_GET['confirm']) && $_GET['confirm'] == 1)
        {
            $query = "DELETE FROM personnel WHERE PRID=" . $_GET['id'];
            mysqli_query($base_id,$query);
        }
    }
    if ($_GET['action'] == 2)
    {
        $query = "UPDATE personnel SET civilite='".mysqli_real_escape_string($base_id,$_GET['civilite'])."', nom='".mysqli_real_escape_string($base_id,$_GET['nom'])."', prenom='".mysqli_real_escape_string($base_id,$_GET['prenom']). "', poste='".mysqli_real_escape_string($base_id,$_GET['poste']). "', discipline='".mysqli_real_escape_string($base_id,$_GET['discipline']). "' WHERE PRID=".$_GET['id'];
        mysqli_query($base_id,$query);
    }
    if ($_GET['action'] == 3)
    {
        $query = "INSERT INTO personnel(civilite,nom,prenom,poste,discipline) VALUES('".mysqli_real_escape_string($base_id,$_GET['civilite'])."','".mysqli_real_escape_string($base_id,$_GET['nom'])."','".mysqli_real_escape_string($base_id,$_GET['prenom'])."','".mysqli_real_escape_string($base_id,$_GET['poste'])."','".mysqli_real_escape_string($base_id,$_GET['discipline'])."')";
        echo $query;
        mysqli_query($base_id,$query);
    }
}

$query = "SELECT * FROM personnel ORDER BY nom,prenom";
$liste_profs=mysqli_query($base_id,$query);
?>
<div id="statut"></div>

<table border=1 cellpadding=0 cellspacing=0>
    <tr>
        <th rowspan="2" width = 75px></th>
        <th colspan="6" class="TDligne2"><?php echo mysqli_num_rows($liste_profs); ?> enregistrements</th>
    </tr>
    <tr>
        <th width = "70px" BGCOLOR="#99CCFF">civilite</th>
        <th width = "200px" BGCOLOR="#99CCFF">Nom</th>
        <th width = "120px" BGCOLOR="#99CCFF">prenom</th>
        <th width = "130px" BGCOLOR="#99CCFF">Poste</th>
        <th width = "200px" BGCOLOR="#99CCFF">Discipline</th>
        <th width = "200px"><input type='button' onClick="javascript:add_personnel()" value='Ajouter une personne'></th>
    </tr>
    <tr id="addPersonnel">
    </tr>


<?php //autre lignes du tableau
for($j=0;$j<mysqli_num_rows($liste_profs);$j++) // enumere les absences
{
    $PrID = mysqli_result($liste_profs,$j,"PRID");
    echo "<tr><td>";
    $civilite = mysqli_result($liste_profs,$j,"civilite");
    $nom = mysqli_result($liste_profs,$j,"nom");
    $prenom = mysqli_result($liste_profs,$j,"prenom");
    $poste = mysqli_result($liste_profs,$j,"poste");
    $discipline = mysqli_result($liste_profs,$j,"discipline");

?>
    <input border=0 src="ico/supp.gif" type=image onClick="javascript:window.location='personnel.php?action=1&id=<?php echo $PrID ?>';" align="middle" >
    <input border=0 src="ico/edit.gif" type=image onClick="javascript:edit_personnel('text','<?php echo addslashes($civilite) ?>','<?php echo addslashes($nom) ?>','<?php echo addslashes($prenom) ?>', '<?php echo addslashes($poste) ?>', '<?php echo addslashes($discipline) ?>','<?php echo $j?>', <?php echo $PrID ?>);" align="middle" >
    <input border=0 src="ico/view.gif" type=image onClick="javascript:window.location='voir_personnel.php?id=<?php echo $PrID?>'" align="middle">
    </td>
<?php
    $bgColor = ($j % 2) ? "#CCFFCC" : "#66FFFF";

    echo "<td bgcolor=$bgColor id='civi$j'>$civilite</td>";
    echo "<td bgcolor=$bgColor id='nom$j'>$nom</td>";
    echo "<td bgcolor=$bgColor id='prenom$j'>$prenom</td>";
    echo "<td bgcolor=$bgColor id='poste$j'>$poste</td>";
    echo "<td bgcolor=$bgColor id='discipline$j'>$discipline</td>";
?>
<td id="save<?php echo $j?>"></td>

    </tr>

<?php
}
?>

</table>

<br>
</div>
</body>
</html>

