<?php
Function abs_tableau($liste_abs,$mode)
{
    // l'argument est le résultat d'une requete sur la table absences

    echo "<table cellpadding=0 cellspacing=0><tr>";

    // entête du tableau
    if (!isset($mode)) $gif_menu = "<img src=\"ico/menu.gif\">";
    echo "<th rowspan=\"3\" width = 120px>".$gif_menu."</th>";
    echo "<th colspan=\"8\">".mysqli_num_rows($liste_abs)." enregistrements </th></tr>";
    echo "<tr>";
    echo "<th width = \"150px\" BGCOLOR=\"#99CCFF\" colspan='2' rowspan='2'>Date</a></th>";
    echo "<th width =\"280px\" BGCOLOR=\"#99CCFF\">Nom</th>";
    echo "<th width = \"130px\" BGCOLOR=\"#99CCFF\">Discipline</th>";
    echo "<th width = \"80px\" BGCOLOR=\"#99CCFF\">Categorie</th>";
    echo "<th width = \"150px\" BGCOLOR=\"#99CCFF\">Examen</th>";
    echo "<th width = \"100px\" BGCOLOR=\"#99CCFF\">Date de saisie</th>";
    echo "<th width = \"80px\" rowspan='2' BGCOLOR=\"#99CCFF\">Pré-saisie</th>";
    echo "</tr><tr>";
    echo "<th class=\"TDligne2\">Details</th>";
    echo "<th class=\"TDligne2\">Ordonateur</th>";
    echo "<th class=\"TDligne2\" colspan='2'>Lieux</th>";
    echo "<th class=\"TDligne2\">modif. par</th></tr>";

    //autre lignes du tableau
    for($j=0;$j<mysqli_num_rows($liste_abs);$j++) // enumere les absences
    {
        $tab_jours = array("Dim ", "Lun ", "Mar ", "Mer ", "Jeu ", "Ven ", "Sam ");
        $abID = mysqli_result($liste_abs,$j,"ABID");
        $date_saisie = mysqli_result($liste_abs,$j,"date_saisie");
        $date_saisie = date("d/m/Y H:i", strtotime($date_saisie));
        $date_debut = mysqli_result($liste_abs,$j,"date_debut");
        $date_fin = mysqli_result($liste_abs,$j,"date_fin");
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
        $categorie = mysqli_result($liste_abs,$j,"categorie");
        $examen = mysqli_result($liste_abs,$j,"examen");
        $details = mysqli_result($liste_abs,$j,"details");
        $ordonateur = mysqli_result($liste_abs,$j,"ordonateur");
        $nom = mysqli_result($liste_abs,$j,"nom");
        $prenom = mysqli_result($liste_abs,$j,"prenom");
        $poste = mysqli_result($liste_abs,$j,"poste");
        $discipline = mysqli_result($liste_abs,$j,"discipline");
        $nom_lieu = mysqli_result($liste_abs,$j,"nom_lieu");
        $interne = mysqli_result($liste_abs,$j,"interne");
        $capacite = mysqli_result($liste_abs,$j,"capacite");
        $etat = mysqli_result($liste_abs,$j,"etat");
        $affiche = mysqli_result($liste_abs,$j,"afficher");
        $preset = mysqli_result($liste_abs,$j,"preset");
        $print = mysqli_result($liste_abs,$j,"print");
        $user = mysqli_result($liste_abs,$j,"user");

        if (mysqli_result($liste_abs,$j,"categorie") == "Examen") $TDclass = "class='TDexamen '";
        elseif (mysqli_result($liste_abs,$j,"categorie") == "Stage") $TDclass = "class='TDstage '";
        elseif (mysqli_result($liste_abs,$j,"categorie") == "Maladie") $TDclass = "class='TDmaladie '";
        elseif (mysqli_result($liste_abs,$j,"categorie") == "Autre") $TDclass = "class='TDautre '";

        $inter = intval((strtotime($date_f)-strtotime($date))/86400)+1;


        // informations sur l'absence

        echo "<tr><td colspan=\"9\">&nbsp;</td></tr>"; // ligne vide entre deux

       // icone edit / suppr / copy
        echo "<tr><td>";
        if ($_SESSION['userType'] <= 2) echo "<input border=0 src=\"ico/edit.gif\" type=image onClick=\"javascript:window.location='absences_new.php?action=3&abid=".$abID."'\">";
        if ($_SESSION['userType'] <= 2) echo "<input border=0 src=\"ico/supp.gif\" type=image onClick=\"javascript:window.location='absences.php?action=1&id=".$abID."'\"> ";
        switch($mode)
        {
            default:
                if ($_SESSION['userType'] <= 2) echo "<input border=0 src=\"ico/copy.gif\" type=image onClick=\"javascript:window.location='absences_new.php?action=4&abid=".$abID."'\">";
                if ($_SESSION['userType'] <= 1)
                {
                    if($print == 0)	echo '<input border=0 src="ico/pdf.gif" type=image onClick="javascript:window.location=\'absences.php?action=6&abID=' . $abID . '&print=1\';" > '."\n";
                    else echo '<input border=0 src="ico/pdf_fait.gif" type=image onClick="javascript:window.location=\'absences.php?action=6&abID=' . $abID . '&print=0\';" > '."\n";
                }
                if ($_SESSION['userType'] <= 1)
                {
                    if($affiche == 1) echo '<input border=0 src="ico/tv.gif" type=image onClick="javascript:window.location=\'absences.php?action=4&abID=' . $abID . '&afficher=0\';"  > '."\n";
                    else echo '<input border=0 src="ico/no_tv.gif" type=image onClick="javascript:window.location=\'absences.php?action=4&abID=' . $abID . '&afficher=1\';"  > '."\n";
                }
                break;
            case 0:
                break;
        }
        echo "</td>";

        // change le style pour les pré-saisies
        if($preset == 0){
            $style = $categorie;
            }
        else{
            $style = $categorie."P";
            };


        echo "<td id=".$style." class=\"top_left\" colspan='2' align='center'>".$jour." ".$ymd[2]."/".$ymd[1]."/".$ymd[0];
        if($inter > 1) echo " (".$inter." jours)</td>";
        echo "<td id=".$style." class=\"top\"><b>".$nom." ".$prenom."</b></td>";
        echo "<td id=".$style." class=\"top\">".$poste." ".$discipline."</td>";
        echo "<td id=".$style." class=\"top\">".$categorie."</td>";
        echo "<td id=".$style." class=\"top\">".$examen."</td>";
        echo "<td id=".$style." class=\"top_right\" align='middle'>".$date_saisie."</td>";
        echo "<td rowspan='2' align='middle'>";

        /*if ($etat == 1)
            echo '<input border=0 src="ico/1.gif" type=image onClick="javascript:window.location=\'absences.php?action=1&id=<?php echo $abID ?>\';"  > ';
        if ($etat == 2)
            echo '<input border=0 src="ico/2.gif" type=image onClick="javascript:window.location=\'absences.php?action=1&id=<?php echo $abID ?>\';"  > ';
        if ($etat == 3)
            echo '<input border=0 src="ico/3.gif" type=image onClick="javascript:window.location=\'absences.php?action=1&id=<?php echo $abID ?>\';"  > ';*/

        switch($mode)
        {
            default:
                if($preset == 0) echo '<input border=0 src="ico/check_off.gif" type=image onClick="javascript:window.location=\'absences.php?action=5&abID=' . $abID . '&preset=1\';"  > '."\n";
                else echo '<input border=0 src="ico/check_on.gif" type=image onClick="javascript:window.location=\'absences.php?action=5&abID=' . $abID . '&preset=0\';"  > '."\n";
                break;
            case 0:
                break;
        }

        echo "</td></tr><tr><td></td>";

        if ($tranche != '')
            echo "<td class='bottom_left' colspan='2'>".$tranche."</td>";
        else
            echo "<td class='bottom_left'>".$date_debut."</td><td class='bottom'>".$date_fin."</td>";

        echo "<td class=\"bottom\">".$details."</td>";
        echo "<td class=\"bottom\">".$ordonateur."</td>";
        echo "<td class=\"bottom\" colspan='2'>".$nom_lieu."</td>";
        echo "<td class=\"bottom_right\" align=\"middle\">".$user."</td>";
        echo "</tr>";
    }
    echo "</table>";
}
Function mini_tableau($liste_abs,$index)
{
    // l'argument est le résultat d'une requete sur la table absences et un index

    echo "<table cellpadding=0 cellspacing=0><tr>";

    // lignes du tableau d'information sur une absences
    $j = $index;

    // collecte des informations
    $tab_jours = array("Dim ", "Lun ", "Mar ", "Mer ", "Jeu ", "Ven ", "Sam ");
    $abID = mysqli_result($liste_abs,$j,"ABID");
    $date_saisie = mysqli_result($liste_abs,$j,"date_saisie");
    $date_saisie = date("d/m/Y H:i", strtotime($date_saisie));
    $date_debut = mysqli_result($liste_abs,$j,"date_debut");
    $date_fin = mysqli_result($liste_abs,$j,"date_fin");
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
    $categorie = mysqli_result($liste_abs,$j,"categorie");
    $examen = mysqli_result($liste_abs,$j,"examen");
    $details = mysqli_result($liste_abs,$j,"details");
    $ordonateur = mysqli_result($liste_abs,$j,"ordonateur");
    $nom = mysqli_result($liste_abs,$j,"nom");
    $prenom = mysqli_result($liste_abs,$j,"prenom");
    $poste = mysqli_result($liste_abs,$j,"poste");
    $discipline = mysqli_result($liste_abs,$j,"discipline");
    $nom_lieu = mysqli_result($liste_abs,$j,"nom_lieu");
    $interne = mysqli_result($liste_abs,$j,"interne");
    $capacite = mysqli_result($liste_abs,$j,"capacite");
    $etat = mysqli_result($liste_abs,$j,"etat");
    $affiche = mysqli_result($liste_abs,$j,"afficher");
    $preset = mysqli_result($liste_abs,$j,"preset");
    $print = mysqli_result($liste_abs,$j,"print");
    $user = mysqli_result($liste_abs,$j,"user");

    if (mysqli_result($liste_abs,$j,"categorie") == "Examen") $TDclass = "class='TDexamen '";
    elseif (mysqli_result($liste_abs,$j,"categorie") == "Stage") $TDclass = "class='TDstage '";
    elseif (mysqli_result($liste_abs,$j,"categorie") == "Maladie") $TDclass = "class='TDmaladie '";
    elseif (mysqli_result($liste_abs,$j,"categorie") == "Autre") $TDclass = "class='TDautre '";

    $inter = intval((strtotime($date_f)-strtotime($date))/86400)+1;

    // change le style selon la catégorie et la pré-saisies

    if($preset == 0){
        $style = $categorie;
        }
    else{
        $style = $categorie."P";
        };


    // affichage des informations sur l'absence

    echo "<tr><td colspan=\"8\">&nbsp;</td></tr>"; // ligne vide entre deux

    // première ligne
    echo "<tr>";

    echo "<td width=120px id=".$style." class=\"top_left\" colspan='2' align='center'>absence du <br>".$jour." ".$ymd[2]."/".$ymd[1]."/".$ymd[0];
    if($inter > 1) echo " (".$inter." jours)</td>";
    echo "<td width=200px id=".$style." class=\"top\"><b>".$nom." ".$prenom."</b></td>";
    echo "<td width=120px id=".$style." class=\"top\">".$poste." ".$discipline."</td>";
    echo "<td width=100px id=".$style." class=\"top\">".$categorie."</td>";
    echo "<td width=120px id=".$style." class=\"top\">".$examen."</td>";
    echo "<td width=120px id=".$style." class=\"top_right\" align='middle'>Saisie du <br>".$date_saisie."</td>";
    echo "<td width=40px rowspan='2' align='middle'>";

    //if($preset == 0) echo "<img border=0 src=\"ico/check_off.gif\" type=image>\n";
    //else echo "<img border=0 src=\"ico/check_on.gif\" type=image>\n";

    echo "</td></tr>";

    // deuxième ligne
    echo "<tr>";

    if ($tranche != '')
        echo "<td class='bottom_left' colspan='2'>".$tranche."</td>";
    else
        echo "<td class='bottom_left'>".$date_debut."</td><td class='bottom'>".$date_fin."</td>";

    echo "<td class=\"bottom\">".$details."</td>";
    echo "<td class=\"bottom\">".$ordonateur."</td>";
    echo "<td class=\"bottom\" colspan='2'>".$nom_lieu."</td>";
    echo "<td class=\"bottom_right\" align=\"middle\">par ".$user."</td>";
    echo "</tr>";

    echo "</table>";
}
?>
