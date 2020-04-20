<?php
require_once("./menu.php");
include("./abs_table.inc.php");
$menu = affiche_menu();
?>
<!DOCTYPE html">
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
        <title>Edition des absences</title>
        <link href="absences.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="js/form_select.js"></script>
    </head>
    <body>
    <?php
    include('db_fonction.php');
    $base_id = connecti_db();
    echo $menu;
    ?>
    <div class='corps'>
    <?php
    // ########################################################################
    // ############################ ACTIONS sur la page #######################
    // ########################################################################
    // action = 1 ==> suppression
    // action = 4 ==> basculer affichage ecran
    // action = 6 ==> popup du bordereau PDF
    if (isset($_GET['action'])){
        // echo "ACTION SET<br>"; // DEBUG
        if($_GET['action'] == 1) { // SUPPR absence
            if(!isset($_GET['confirm'])){
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
                echo $myquery = "DELETE FROM absences WHERE ABID=" . $_GET['abid'];
                mysqli_query($base_id,$myquery);
                echo $other_query = "DELETE FROM conflits WHERE ABID1=".$_GET['abid']." OR ABID2=".$_GET['abid'];
                mysqli_query($base_id,$other_query);
            }
        }
        if($_GET['action'] == 4) { // Affichage éran
            $myquery = "UPDATE absences SET afficher=" . $_GET['afficher'] . " WHERE ABID=" . $_GET['abid'];
            mysqli_query($base_id,$myquery);
        }
        if($_GET['action'] == 6) { // génération du PDF
            echo "<script type=\"text/javascript\">";
            echo "window.open('avis_launch.php?abID=".$_GET['abid']."','launch','menubar=no, status=no, scrollbars=no, width=400, height=200');";
            echo "</script>";
        }
        ?>
        <script type="text/javascript">
            history.back(); // reviens en arriere pour enlever les paramètres GET du formulaire et recharger la page actualisée
        </script>
        <?php
    }

    // ################################################################################################################
    // ########################### fabrication de la requète de récupérations des données #############################
    // ################################################################################################################

    setlocale (LC_TIME, 'fr_FR.utf8','fra');
    $query = "SELECT * FROM absences,personnel,lieux WHERE personnel.PRID = absences.PRID AND absences.LID = lieux.LID";
    $critere = " ";

    //********* critere de date affichées *********
    if (isset($_GET['date'])) // si selection de date passée en parametre
        {
        $sel_date = $_GET['date'];
        $_SESSION['sel_date'] = $_GET['date']; // enregistre la sélection de date dans la session
        }
    else  // si pas de selection de date passée en paramétres
        {
        if (isset($_SESSION['sel_date']))  // mais selection de date passée en session
            {
            $sel_date = $_SESSION['sel_date'];
            }
        else $sel_date = -1;    // valeur par defaut
        }

    switch($sel_date)
        {
        case 1:
        default:
            $critere .= ' AND TO_DAYS(absences.date_debut) <= TO_DAYS(NOW()) AND TO_DAYS(absences.date_fin) >= TO_DAYS(NOW())';
            break;
        case 7:
            $ref_date = date('Y-m-d',strtotime('+7day'));
            $ref_date2 = date('Y-m-d');
            $critere .= ' AND TO_DAYS(absences.date_debut) <= TO_DAYS("'.$ref_date.' 0:00:00") AND TO_DAYS(absences.date_fin) >= TO_DAYS("'.$ref_date2.' 0:00:00")';
            break;
        case 30:
            $ref_date = date('Y-m-d',strtotime('+1month'));
            $ref_date2 = date('Y-m-d');
            $critere .= ' AND TO_DAYS(absences.date_debut) <= TO_DAYS("'.$ref_date.' 0:00:00") AND TO_DAYS(absences.date_fin) >= TO_DAYS("'.$ref_date2.' 0:00:00")';
            break;
        case -1:
            $ref_date = date('Y-m-d');
            $critere .= ' AND TO_DAYS(absences.date_fin) >= TO_DAYS("'.$ref_date.' 0:00:00")' ;
            break;
        case 0:
            ;
            break;
        }

    // ************** critere de catégorie et examen affichés ************
    if (isset($_GET['categorie'])) {
        $categorie = $_GET['categorie'];
        $_SESSION['categorie'] = $categorie;
    }
    else {
        if (isset($_SESSION['categorie']))	{
            $categorie = $_SESSION['categorie'];
        }
        else $categorie = "Tout";
    }

    if (isset($_GET['sel_examen']))	{
        $examen = $_GET['sel_examen'];
        $_SESSION['examen'] = $examen;
    }
    else {
        if (isset($_SESSION['examen'])) {
            $examen = $_SESSION['examen'];
        }
        else $examen = "Tout";
    }

    if ($categorie != 'Tout') {
        $critere .= " AND absences.categorie = '". $categorie. "'";
        if ($categorie == 'Examen')	{
            if ($examen != "Tout") $critere .= " AND absences.examen ='".$examen."' ";
        }
    }

    // ************** critere pre saisies affichées ? ************
    if (isset($_GET['presaisie'])) {
        $presaisie = $_GET['presaisie'];
        $_SESSION['presaisie'] = $_GET['presaisie'];
    }
    else  {
        if (isset($_SESSION['presaisie'])) 	{
            $presaisie = $_SESSION['presaisie'];
        }
        else if ($_SESSION['userType']==1) $presaisie = 'tout';
        else if ($_SESSION['userType']==2) $presaisie = 'seulement';
        else if ($_SESSION['userType']==3) $presaisie = 'tout';
        else $presaisie = 'tout';
    }

    if ($presaisie == 'sans') $critere .= " AND absences.preset = 0";
    else if ($presaisie == 'seulement') $critere .= " AND absences.preset = 1";
    else if ($presaisie == 'tout') $critere .= "";


    // *************** critere ordre de tri ******************
    if (isset($_GET['tri'])) // si tri passé en parametre
        {
        $sel_tri = $_GET['tri'];
        $_SESSION['tri'] = $_GET['tri']; // enregistre la sélection de tri dans la session
        }
    else  // si pas de selection de tri passée en paramétres
        {
        if (isset($_SESSION['tri']))  // mais selection de tri passée en session
            {
            $sel_tri = $_SESSION['tri'];
            }
        else $sel_tri = 'date_saisie';    // valeur par defaut
        }

    //**************** critere nom ***********************
    if (isset($_GET['sel_nom'])) // si nom passé en parametre
        {
        $sel_nom = $_GET['sel_nom'];
        $_SESSION['sel_nom'] = $_GET['sel_nom']; // enregistre la sélection de nom dans la session
        }
    else  // si pas de selection de nom passée en paramétres
        {
        if (isset($_SESSION['sel_nom']))  // mais selection de nom passée en session
            {
            $sel_nom = $_SESSION['sel_nom'];
            }
        else $sel_nom = 0;    // valeur par defaut
        }

    if ($sel_tri=="nom")
        {
        if ($sel_nom!=0) $critere .= " AND absences.PRID	= ".$sel_nom;
        }


    $critere .= " ORDER BY";
        if ($sel_tri=='dated')
            $critere .= " absences.date_debut,personnel.nom,personnel.prenom";
        if ($sel_tri=='datec')
            $critere .= " absences.date_debut DESC,personnel.nom,personnel.prenom";
        if ($sel_tri=='nom')
            $critere .= " personnel.nom,personnel.prenom,absences.date_debut DESC";
        if ($sel_tri=='discipline')
            $critere .= " personnel.discipline,personnel.nom,personnel.prenom,absences.date_debut";
        if ($sel_tri=='date_saisie')
            $critere .= " absences.date_saisie DESC";

    // *************** critere numéro de page ******************
    $page = (isset($_GET['page'])) ? $_GET['page'] : 0;

    // *************** exécute la requete ******************
    $query .= $critere;
    $liste_abs=mysqli_query($base_id,$query);
    $CompteTotalAbs = mysqli_num_rows($liste_abs);

    // echo $query."<br>"; // DEBUG

    // ############################################################################################################
    // ##################################### formulaire de sélection et de tri ####################################
    // ############################################################################################################
    ?>


    <form name="critere" method="get" action="absences.php">
    <table>
        <tr>
        <td>
        Affichage des jours
        </td>
        <td>
        Limiter à type<br> d'absence
        </td>
        <td>
        Ordre de tri
        </td>
        <td>
        Pré-saisies
        </td>
        <td>

        </td>
        </tr>
        <tr>
        <td>
        <select name="date" id='date' onchange="document.critere.submit()">
            <option value="1">Aujourd'hui</option>
            <option value="7">7 jours prochain</option>
            <option value="30">30 jours prochain</option>
            <option value="0">Tout</option>
            <option value="-1">Tout à partir d'aujourd'hui</option>
        </select>
        </td>
        <td>
        <select name="categorie" id='categorie' onchange="document.critere.submit()">
            <option>Tout</option>
            <?php option_liste_categorie(null);?>
        </select>
        </td>
        <td>
        <select name="tri" id='tri' onchange="document.critere.submit()">
            <option value="date_saisie">Par date de saisie</option>
            <option value="dated">Par date abs croissante</option>
            <option value="datec">Par date abs décroissante</option>
            <option value="nom">Par Nom</option>
            <option value="discipline">Par Discipline</option>
        </select>
        </td>
        <td>
        <select name="presaisie" id='presaisie' onchange="document.critere.submit()">
            <option value="avec">Toutes les saisies</option>
            <option value="sans">Sans les pré-saisies</option>
            <option value="seulement">Pré-saisies seules</option>
        </select>
        </td>
        <td>
        <a href="absences_new.php"><img src="ico/bouton_new_abs.png"></a>
        <a href="abs_pdf.php" target="_blank"> absences publiées pour les jours à venir</a><br>
        </td>
        </tr>
        <tr>
            <td>

            </td>


            <td id='form_examen'>
                <select name="sel_examen" id='sel_examen' onchange="document.critere.submit()">
                <option value="Tout">Tout</option>
                <?php option_liste_examen(isset($_GET['sel_examen'])?$_GET['sel_examen']:null); ?>
                </select>
            </td>

            <td id='form_nom'>
                <select name="sel_nom" id='sel_nom' onchange="document.critere.submit()">
                <option value="Tous">Tout</option>
                <?php option_liste_prof(isset($_GET['sel_nom'])?$_GET['sel_nom']:null); ?>
                </select>
            </td>

            <td>

            </td>

            <td>

            </td>
        </tr>
    </table>
    </form>

    <!-- ************************** positionnemnt formulaire de selection et de tri ************************** -->
    <script type="text/javascript">
        setSelected('date', '<?php echo $sel_date ?>');
        setSelected('categorie', '<?php echo $categorie ?>');
        setSelected('sel_examen', '<?php echo $examen ?>');
        if ('<?php echo $categorie ?>' == 'Examen')
            document.getElementById("form_examen").style.visibility = "visible";
        else
            document.getElementById("form_examen").style.visibility = "hidden";
        setSelected('tri', '<?php echo $sel_tri ?>');
        if ('<?php echo $sel_tri ?>' == 'nom')
            document.getElementById("form_nom").style.visibility = "visible";
        else
            document.getElementById("form_nom").style.visibility = "hidden";
        setSelected('presaisie', '<?php echo $presaisie ?>');
    </script>

    <?php

    // ************ affichage des pages en cas de pages multiples ******
    echo "Pages: ";

    for($i=0; $i<$CompteTotalAbs / 50; $i++)
    {
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $tmp = explode('page=', $url);
        if(count($tmp) < 2)
            if(strpos($url, '?') == false)
                $url = $tmp[0] . "?page=" . $i;
            else
                $url = $tmp[0] . "&page=" . $i;
        else
        {
            for($c=0;$c < strlen($tmp[1]) && $tmp[1][$c] <= '9' && $tmp[1][$c] >= '0'; $c++)
                ;
            $tmp[1] = substr($tmp[1], $c, strlen($tmp[1] - $c));
            $url = $tmp[0] . "page=" . $i . $tmp[1];
        }
        if($page == $i)
            echo "<a href='". $url ."'><b>". ($i + 1) ."</b></a>";
        else
        {
            echo "<a href='". $url ."'>";
            echo $i + 1;
            echo "</a>";
        }
        echo " ";
    }

    // ##################################################################################
    // ############# affichage des données ##############################################
    // ##################################################################################

    $query .= " LIMIT ". $page * 50 .", 50";
    $liste_abs=mysqli_query($base_id,$query);
    ?>

    <table cellpadding=0 cellspacing=0>
    <tr>
    <th rowspan="3" width = 120px><img src="ico/menu.gif"></th>
    <th colspan="8">

    <?php echo mysqli_num_rows($liste_abs); ?> enregistrements sur <?php echo $CompteTotalAbs; ?>
    </th>
    </tr>
    <tr>
    <th width = "150px" BGCOLOR="#99CCFF" colspan='2' rowspan='2'>Date</a></th>
    <th width = "280px" BGCOLOR="#99CCFF">Nom</th>
    <th width = "130px" BGCOLOR="#99CCFF">Discipline</th>
    <th width = "80px" BGCOLOR="#99CCFF">Categorie</th>
    <th width = "150px" BGCOLOR="#99CCFF">Examen</th>
    <th width = "100px" BGCOLOR="#99CCFF">Date de saisie</th>
    <th width = "80px"  BGCOLOR="#99CCFF">Pré-saisie</th>
    </tr>
    <tr>
    <th class="TDligne2">Details</th>
    <th class="TDligne2">Ordonateur</th>
    <th class="TDligne2" colspan='2'>Lieux</th>
    <th class="TDligne2">modif. par</th>
    <th class="TDligne2">gest convoc</th>
    </tr>


    <?php //autre lignes du tableau
    while ($abs = mysqli_fetch_assoc($liste_abs)) // enumere les absences
    {
        $tab_jours = array("Dim ", "Lun ", "Mar ", "Mer ", "Jeu ", "Ven ", "Sam ");
        $abID = $abs["ABID"];
        $date_saisie = $abs["date_saisie"];
        $date_saisie = date("d/m/Y H:i", strtotime($date_saisie));
        $date_debut = $abs["date_debut"];
        $date_fin = $abs["date_fin"];
        $date = explode(" ", $date_debut);
        $date_debut = $date[1];
        $date = $date[0];
        $date_fin = explode(" ", $date_fin);
        $date_f = $date_fin[0];
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
        $jour = $tab_jours[date('w', mktime(0,0,0,date($ymd[1]),date($ymd[2]),date($ymd[0])))];
        $categorie = $abs["categorie"];
        $examen = $abs["examen"];
        $details = $abs["details"];
        $ordonateur = $abs["ordonateur"];
        $nom = $abs["nom"];
        $prenom = $abs["prenom"];
        $poste = $abs["poste"];
        $discipline = $abs["discipline"];
        $nom_lieu = $abs["nom_lieu"];
        $interne = $abs["interne"];
        $capacite = $abs["capacite"];
        $etat = $abs["etat"];
        $affiche = $abs["afficher"];
        $preset = $abs["preset"];
        $print = $abs["print"];
        $user = $abs["user"];
        $gest = $abs["gest"];

        if ($abs["categorie"] == "Examen") $TDclass = "class='TDexamen '";
        elseif ($abs["categorie"] == "Stage") $TDclass = "class='TDstage '";
        elseif ($abs["categorie"] == "Maladie") $TDclass = "class='TDmaladie '";
        elseif ($abs["categorie"] == "Autre") $TDclass = "class='TDautre '";

        $inter = intval((strtotime($date_f)-strtotime($date))/86400)+1;

        // informations sur l'absence
    ?>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <tr>
        <td>
        <!-- icones cliquables -->
        <img border=0 src="ico/edit.gif" type="image" <?php if ($_SESSION['userType'] < 3) echo "onClick=\"javascript:window.location='absences_new.php?action=3&abid=".$abID."';\"\n";?>>
        <img border=0 src="ico/supp.gif" type="image" <?php if ($_SESSION['userType'] < 3) echo "onClick=\"javascript:window.location='absences.php?action=1&abid=".$abID."';\"\n";?>>
        <img border=0 src="ico/copy.gif" type="image" <?php if ($_SESSION['userType'] < 3) echo "onClick=\"javascript:window.location='absences_new.php?action=4&abid=".$abID."';\"\n";?>>
        <?php
        if($print == 0) $pdf_ico="ico/pdf.gif"; else $pdf_ico="ico/pdf_fait.gif";
        if($print == 0) $pdf_act=1; else $pdf_act=0;
        echo "<img border=0 src=\"".$pdf_ico."\" type=image ";
            if ($_SESSION['userType'] < 3) echo "onClick=\"javascript:window.location='absences.php?action=6&abid=".$abID."&print=".$pdf_act."';\"";
        echo ">\n";
        if($affiche == 1) $aff_ico = "ico/tv.gif"; else $aff_ico = "ico/no_tv.gif";
        if($affiche == 1) $aff_act = 0; else $aff_act = 1;
        echo "<img border=0 src=\"".$aff_ico."\" type=image ";
            if ($_SESSION['userType'] < 3) echo "onClick=\"javascript:window.location='absences.php?action=4&abid=".$abID."&afficher=".$aff_act."';\"";
        echo ">\n";
        ?>
        </td>

        <?php
        if($preset == 0){
            $style = $categorie;
            }
        else{
            $style = $categorie."P";
            };
        ?>

        <td id=<?php echo $style ?> class="top_left" colspan='2' align='center'>
            <?php echo "$jour $ymd[2]/$ymd[1]/$ymd[0]"; if($inter > 1) echo " (".$inter." jours)";?>
        </td>
        <td id=<?php echo $style ?> class="top">
            <b><?php echo "$nom $prenom"?></b>
        </td>
        <td id=<?php echo $style ?> class="top">
            <?php echo "$poste $discipline"?>
        </td>
        <td id=<?php echo $style ?> class="top">
            <?php echo "$categorie"?>
        </td>
        <td id=<?php echo $style ?> class="top">
            <?php echo "$examen"?>
        </td>
        <td id=<?php echo $style ?> class="top_right" align='middle'>
            <?php echo "$date_saisie"?>
        </td>
        <td align='middle'>
    <?php
        //***************************************** gestion de l'affichage de l'état d'avancement ***********************************************
        /*
        if ($etat == 1)
            echo '<input border=0 src="ico/1.gif" type=image onClick="javascript:window.location=\'absences.php?action=5&id=<?php echo $abID ?>\';"  > ';
        if ($etat == 2)
            echo '<input border=0 src="ico/2.gif" type=image onClick="javascript:window.location=\'absences.php?action=7&id=<?php echo $abID ?>\';"  > ';
        if ($etat == 3)
            echo '<input border=0 src="ico/3.gif" type=image onClick="javascript:window.location=\'absences.php?action=9&id=<?php echo $abID ?>\';"  > ';
        */
        //****************************************************************************************************************************************
        if($preset == 0)
            echo '<input border=0 src="ico/check_off.gif" type=image onClick="javascript:window.location=\'absences.php?action=5&abID=' . $abID . '&preset=1\';"  > '."\n";
        else
            echo '<input border=0 src="ico/check_on.gif" type=image onClick="javascript:window.location=\'absences.php?action=5&abID=' . $abID . '&preset=0\';"  > '."\n";

    ?>
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
            <?php echo $details?>
        </td>
        <td class="bottom">
            <?php echo $ordonateur?>
        </td>
        <td class="bottom" colspan='2'>
            <?php echo $nom_lieu?>
        </td>
        <td class="bottom_right" align="middle">
            <?php echo $user?>
        </td>
        <td class="bottom_right" align="middle">
            <?php echo $gest?>
        </td>

    </tr>

    <?php
    }
    ?>
    </table>
    </div>
    </body>
</html>

