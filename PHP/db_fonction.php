<?php
require_once('../config.inc.php');

####################################################################################
# crée une connection persistante avec le serveur Mysql
####################################################################################
function connecti_db()
{
    global $mysql_serv_IP;
    global $mysql_serv_UID;
    global $mysql_serv_passwd;
    global $mysql_serv_DB;

    $base_id = mysqli_connect($mysql_serv_IP,$mysql_serv_UID,$mysql_serv_passwd,$mysql_serv_DB);

    mysqli_query($base_id,"SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    return ($base_id);
}

function mysqli_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}

####################################################################################
#   renvoie la liste des profs et ID comme option d'un champ de formulaire
#   si $selected est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_prof($selected_id)
{
    $base_id = connecti_db();
    $liste_prof = mysqli_query($base_id,"SELECT PRID,nom,prenom FROM personnel ORDER BY nom,prenom");
    while ($prof = mysqli_fetch_assoc($liste_prof))
    {
        echo "<option value=".$prof["PRID"];
        if (isset($selected_id) && $prof["PRID"]==$selected_id)
            echo " selected";
        echo ">"." ". $prof["nom"]." ".$prof["prenom"]."</option>\n";
    }
    mysqli_free_result($liste_prof);
}

####################################################################################
#   renvoie la liste des categories comme option d'un champ de formulaire
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_categorie($selected_categorie)
{
    global $tabl_categorie;
    for ($i=0;$i<count($tabl_categorie);$i++)
    {
        echo "<option value=\"".$tabl_categorie[$i]."\"";
        if(isset($selected_categorie) && $tabl_categorie[$i] == $selected_categorie)
            echo " selected";
        echo ">";
        echo $tabl_categorie[$i]."</option>\n";
    }
}

####################################################################################
#   renvoie la liste des gestionnaires comme option d'un champ de formulaire
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_gest($selected_gest)
{
    $base_id = connecti_db();
    $liste_gest = mysqli_query($base_id,"SELECT username FROM users ORDER BY username");
    while ($gest = mysqli_fetch_assoc($liste_gest))
    {
        echo "<option value=".$gest["username"];
        if (isset($selected_gest) && $gest["username"] ==$selected_gest)
            echo " selected";
        echo ">"." ". $gest["username"]."</option>\n";
    }
    mysqli_free_result($liste_gest);
}
####################################################################################
#   renvoie la liste des tranches horaires comme option d'un champ de formulaire
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_tranche($selected_tranche)
{
    global $tabl_tranche;
    for ($i=0;$i<count($tabl_tranche);$i++)
    {
        echo "<option value=\"".$tabl_tranche[$i]."\"";
        if(isset($selected_tranche) && $tabl_tranche[$i] == $selected_tranche) echo " selected";
        echo ">";
        echo $tabl_tranche[$i]."</option>";
    }
}

####################################################################################
#   renvoie la liste des examens comme option d'un champ de formulaire
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_examen($selected_id)
{
    $base_id = connecti_db();
    $liste_examen = mysqli_query($base_id,"SELECT examen FROM absences WHERE CHAR_LENGTH(examen) > 2 GROUP BY examen ORDER BY examen ");
    while ($exam = mysqli_fetch_assoc($liste_examen))
    {
        $examen = htmlentities($exam["examen"], ENT_QUOTES, "UTF-8");
        echo "<option value='".$examen."'";
        if (isset($selected_id) && $examen==$selected_id)
            echo " selected";
        echo ">"." ". $examen."</option>\n";
    }
    mysqli_free_result($liste_examen);
}

####################################################################################
#   ajoute les slaches devant les caractères spéciaux selon la configuration de
#   magic_quote_gpc
####################################################################################

function MyAddSlashes($chaine ) {
  return( get_magic_quotes_gpc() == 1 ?
          $chaine :
          addslashes($chaine) );
}

########################################################################
#  date française en format texte
########################################################################

function dateFR($thedate)
{
    $joursem = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    list($annee, $mois, $jour) = explode('-', $thedate);
    $timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
    return $joursem[date("w",$timestamp)];
}

####################################################################################
#   renvoie une source de donnée pour l'autocopmlétion de champs input text
####################################################################################

function autocomplete_source($field,$table)
{
    $base_id = connecti_db();
    $requete = "SELECT $field FROM $table GROUP BY $field ORDER BY $field";
    $result = mysqli_query($base_id,$requete);
    echo "source: [";
    for($j=0; $j < mysqli_num_rows($result); $j++)
       echo "\"".addslashes(mysqli_result($result, $j))."\",";
    echo "\"\"]";
}

?>
