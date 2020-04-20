<?php
    require_once("./menu.php");
    $menu = affiche_menu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <link href="css/menu.css" type="text/css" rel="stylesheet" />
    <link href="absences.css" rel="stylesheet" type="text/css">
    <title>edition textes</title>
</head>
<body>
<?php
echo $menu;
//connexion à la base absences
include('db_fonction.php');
$base_id = connecti_db();
?>
<div class='corps'>
<form method="post" action="textes.php" name="select">
    <p>Choisissez la categorie de document à éditer: </p>
    <select name="categorie" id="categorie" onchange="document.select.submit()">
    <option value="rien">choisissez une categorie</option>
    <?php option_liste_categorie($_POST['categorie']);?>
    </select>
    <input name="status" type="hidden" value="edit">
</form>
<?php

//*******************************************************************************************************************************
if( isset( $_POST['status']))
    {
    if( isset( $_POST['status'])&&($_POST['status']=="set")) //mise à jour du texte
        {
        echo "Le texte à été mise a jour ";
        //$requete = "UPDATE textes SET titre='".MyAddSlashes($_POST['titre'])."', intro='".$_POST['intro']."', bas_intro='".MyAddSlashes($_POST['bas_intro'])."', intro_multi='".MyAddSlashes($_POST['intro_multi'])."', bas_intro_multi='".MyAddSlashes($_POST['bas_intro_multi'])."' WHERE categorie='".$_POST['categorie']."'";
        $requete = "UPDATE textes SET titre='".MyAddSlashes($_POST['titre'])."', intro_multi='".MyAddSlashes($_POST['intro_multi'])."', bas_intro_multi='".MyAddSlashes($_POST['bas_intro_multi'])."' WHERE categorie='".$_POST['categorie']."'";
        $update = mysqli_query($base_id,$requete);
        }
    // affichage du formulaire d'édition du texte
    echo "<form method=\"post\" action=".$_SERVER['PHP_SELF']." name=\"text\">";
    echo "<h1>".$_POST['categorie']."</h1>";

    $requete = "SELECT * FROM textes WHERE categorie='".$_POST['categorie']."'";
    $result= mysqli_query($base_id,$requete);

    echo "<h2> Titre </h2>";
    echo "<textarea name=\"titre\" cols=\"100\" rows=\"1\">";
    echo mysqli_result($result,0,"titre");
    echo "</textarea>";
    echo "<input type=\"submit\" value=\"Valider\">";

    echo "<h2> Texte d'introduction ( taper [gest] pour insérer le lieu de retrait)</h2>";
    echo "<textarea name=\"intro_multi\" cols=\"100\" rows=\"7\">";
    echo mysqli_result($result,0,"intro_multi");
    echo "</textarea>";
    echo "<input type=\"submit\" value=\"Valider\">";

    echo "<h2> texte de signature </h2>";
    echo "<textarea name=\"bas_intro_multi\" cols=\"100\" rows=\"2\">";
    echo mysqli_result($result,0,"bas_intro_multi");
    echo "</textarea>";
    echo "<input type=\"submit\" value=\"Valider\">";

    echo "<input name=\"status\" type=\"hidden\" value=\"set\">";
    echo "<input name=\"categorie\" type=\"hidden\" value=\"".$_POST['categorie']."\">";

    echo "</form>";
    }
?>
</div>
</body>
</html>
