<?php
include('db_fonction.php');

function displayTable($n, $date, $liste_date)
{
    for($j=0; $j<2*$n; $j++)
    {
        $tmp = date("Y-m-d", strtotime("+" . (int)($j / 2) . " day", $date)) . (($j % 2) ? " 13:00:00" : " 08:00:00");
        $date_boucle = date_parse($tmp);
        for($k=0; $k<mysqli_num_rows($liste_date); $k++)
        {
            $date_debut = date_parse(mysqli_result($liste_date, $k, 'date_debut'));
            $date_fin = date_parse(mysqli_result($liste_date, $k, 'date_fin'));
            $lieux = "test";//mysqli_result($liste_date, $k, 'nom_lieu');
            $lid = 42;//mysqli_result($liste_date, $k, 'LID');
            if($date_boucle['year'] >= $date_debut['year'] && $date_boucle['year'] <= $date_fin['year'] &&
                $date_boucle['month'] >= $date_debut['month'] && $date_boucle['month'] <= $date_fin['month'] &&
                $date_boucle['day'] >= $date_debut['day'] && $date_boucle['day'] <= $date_fin['day'] &&
                $date_boucle['hour'] >= $date_debut['hour'] && $date_boucle['hour'] < $date_fin['hour'])
            {
                echo "<td ";
                echo "id='occupe'";
                echo "onmouseover=\"montre('". $lieux ."');\" onmouseout=\"cache();\">";
                echo $lid==0 ? "204" : "ext.";
                echo "</td>";
                break;
            }
        }
        if($k == mysqli_num_rows($liste_date))
        {
            echo "<td ";
            echo (($j+2) % 14 <= 1) ? "id='dimanche'" : "id='vide'";
            echo "></td>";
        }
    }
}

function displayHeader($n, $type, $date, $details)
{
    $jours = array("Lun ", "Mar ", "Mer ", "Jeu ", "Ven ", "Sam ", "Dim ");
    $w = $n * 70 + 220 + ($details == 0 ? 0 : 100);
    echo "
        <table id='tablecontainer' width='".$w."px'>
        <tr>";
    if(!$type)
    {
        echo"
        <th rowspan=2 width='100px'>Examen</th>";
         if($details)
            echo "<th rowspan=2 width='100px'>Tache</th>";
        else
            echo "<th rowspan=2 ></th>";
        echo "<th rowspan=2 width='120px'>Professeur</th>";
    }
    else
    {
        echo"
        <th rowspan=2 width='120px'>Professeur</th>
        <th rowspan=2 width='100px'>Examen</th>";
         if($details)
            echo "<th rowspan=2 width='100px'>Tache</th>";
        else
            echo "<th rowspan=2 ></th>";
    }

    for($i=0;$i<$n;$i++)
    {
        $tmp = date("Y-m-d",strtotime("+".$i." day", $date));
        list($y,$m,$d) = explode('-', $tmp);
        echo "<th width='70px' ";
        echo (($i+1) % 7 == 0) ? "id='dimanche'" : "";
        echo "colspan=2>";
        echo $jours[$i % 7] . "</br>";
        echo $d . "/" . $m."</th>";
    }
    echo "</tr><tr>";

    for($i=0;$i<$n;$i++)
        echo "<th width='35px'>M</th><th width='35px'>A</th>";
    echo "</tr>";
}


function displayExam($semaine_debut, $semaine_fin, $date, $n, $listExpand)
{

    displayHeader($n, 0, $date, count($listExpand));
    $base_id = connecti_db();
    $query = "SELECT examen,date_debut,date_fin,PRID,ABID,details FROM absences WHERE absences.categorie = 'Examen' AND TO_DAYS(absences.date_debut) >= TO_DAYS('".$semaine_debut."') AND TO_DAYS(absences.date_debut) <= TO_DAYS('".$semaine_fin."') ORDER BY examen,details";
    $liste_examen = mysqli_query($base_id,$query);
    $last;
    $last_exam = '';
    $prid = '';
    $examen = '';
    $details = '';

    for($i=0; $i<mysqli_num_rows($liste_examen); $i++)
    {
        $last = $prid;
        $last_details = $details;
        $prid = mysqli_result($liste_examen, $i, 'PRID');
        $details = mysqli_result($liste_examen, $i, 'details');
        if($last_exam != $examen)
            echo "<tr colspan=14><td>&nbsp;</td></tr>";
        $examen = mysqli_result($liste_examen, $i, 'examen');
        $afficher_details = 0;
        foreach($listExpand as $el)
            if($el == $examen)
            {
                $afficher_details = 1;
                break;
            }
        if($examen == '')
            continue;
        if($afficher_details == 0 && $examen == $last_exam && $last == $prid)
            continue;
        if($afficher_details == 1 && $prid == $last && $last_details == $details)
            continue;
        if($afficher_details)
            $query = "SELECT date_debut,date_fin,nom FROM absences,personnel WHERE personnel.PRID = absences.PRID AND absences.PRID = ". $prid ." AND absences.examen = '". $examen."' AND details='".$details."'";
        else
            $query = "SELECT date_debut,date_fin,nom FROM absences,personnel WHERE personnel.PRID = absences.PRID AND absences.PRID = ". $prid ." AND absences.examen = '". $examen."'";
        $liste_date = mysqli_query($base_id,$query);
        $nom = mysqli_result($liste_date, 0, 'nom');

        echo "<tr>";
        $ico = ($afficher_details == 0 ? 'ico/expand.gif' : 'ico/collapse.gif');
        if($last_exam != $examen)
            echo "<td class='info'>" . $examen . "<a href='".generateUrl($afficher_details, $examen)."'><img align='right' src='".$ico."' alt='expand'/></a></td>";
        else
            echo "<td> </td>";

        if($afficher_details)
            echo "<td>".$details."</td>";
        else
            echo "<td></td>";
        echo "<td>".$nom."</td>";
        displayTable($n, $date, $liste_date);
        echo "</tr>";
        $last_exam = $examen;
    }

    echo "</table>";
}

function generateUrl($afficher_details, $examen)
{
    $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if($afficher_details == 0)
    {
        $pos = strpos($url,$examen);

        if($pos == false) //add
        {
            if(strpos($url,"exp") == false)
            {
                if(strpos($url,"?") == false)
                    $url .= '?exp=';
                else
                    $url .= '&exp=';
            }
            else
                $url .= ",";
           $url .= $examen;
        }
    }
    else
    {
        $ar = explode($examen, $url);
        if(!isset($ar[1]) || $ar[1] == '')
            $url = substr($ar[0], 0, -1);
        else
            $url = $ar[0];
        if(isset($ar[1]))
            $url .= $ar[1];
        if(substr($url, strlen($url)-3, 3) == 'exp')
            $url = substr($url, 0, -4);
    }
    return $url;
}

function displayProf($semaine_debut, $semaine_fin, $date, $n, $listExpand)
{
    displayHeader($n, 1, $date, count($listExpand));
    $base_id = connecti_db();
    $query = "SELECT examen,date_debut,date_fin,PRID,ABID,details FROM absences WHERE absences.categorie = 'Examen' AND TO_DAYS(absences.date_debut) >= TO_DAYS('".$semaine_debut."') AND TO_DAYS(absences.date_debut) <= TO_DAYS('".$semaine_fin."') ORDER BY PRID,examen";
    $liste_examen = mysqli_query($base_id,$query);
    $last;
    $last_exam = '';
    $prid = '';
    $examen = '';
    $details = '';

    for($i=0; $i<mysqli_num_rows($liste_examen); $i++)
    {
        $last = $prid;
        $last_details = $details;
        $prid = mysqli_result($liste_examen, $i, 'PRID');
        $details = mysqli_result($liste_examen, $i, 'details');
        if($last != $prid)
            echo "<tr colspan=14><td>&nbsp;</td></tr>";
        $examen = mysqli_result($liste_examen, $i, 'examen');
        $afficher_details = 0;
        foreach($listExpand as $el)
            if($el == $examen)
            {
                $afficher_details = 1;
                break;
            }
        if($examen == '')
            continue;
        if($afficher_details == 0 && $examen == $last_exam && $last == $prid)
            continue;
        if($afficher_details == 1 && $examen == $last_exam && $last_details == $details)
            continue;
        if($afficher_details)
            $query = "SELECT date_debut,date_fin,nom FROM absences,personnel WHERE personnel.PRID = absences.PRID AND absences.PRID = ". $prid ." AND absences.examen = '". $examen."' AND details='".$details."'";
        else
            $query = "SELECT date_debut,date_fin,nom FROM absences,personnel WHERE personnel.PRID = absences.PRID AND absences.PRID = ". $prid ." AND absences.examen = '". $examen."'";
        $liste_date = mysqli_query($base_id,$query);
        $nom = mysqli_result($liste_date, 0, 'nom');

        echo "<tr>";
        $ico = ($afficher_details == 0 ? 'ico/expand.gif' : 'ico/collapse.gif');
        if($last != $prid)
            echo "<td class='info'>" . $nom . "</td><td>" .$examen . "<a href='".generateUrl($afficher_details, $examen)."'><img align='right' src='".$ico."' alt='expand'/></a></td>";
        elseif($last_exam != $examen)
            echo "<td>" . ' ' . "</td><td>" .$examen . "<a href='".generateUrl($afficher_details, $examen)."'><img align='right' src='".$ico."' alt='expand'/></a></td>";
        else
            echo "<td>" . ' ' . "</td><td></td>";
        if($afficher_details)
            echo "<td>".$details."</td>";
        else
            echo "<td></td>";
        displayTable($n, $date, $liste_date);
        echo "</tr>";
        $last_exam = $examen;
    }

    echo "</table>";
}

?>

