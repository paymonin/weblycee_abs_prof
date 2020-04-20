<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>prévision absences HTML4</title>
</head>
<body>
    <table width='100%'>
    <?php
    require "db_fonction.php";
    $base_id = connecti_db();
    $date_courante = date("Y-m-d"); //date courante au format mysql
    $date_max = date("Y-m-d",mktime(0,0,0,date("m"),date("d")+$nb_jour_affichage,date("Y")));
    setlocale(LC_TIME, "fr_FR.utf8"); // passe au système de date français

    // enumere les dates ayant des absences à afficher
    $nb_ligne =0;

    echo "<tr><td colspan='2' style='line-height: 2; background-color: #00ccff; font-size: 3.5em;  font-weight: bold; text-align: center;'>Prévision des absences de professeurs</td></tr>";
    echo "<tr bgcolor='#ffffff' border='1' valign='top'><td valign='top' width='50%'><table width ='100%'>";
    for ($k=1; $k < $nb_jour_affichage; $k++)  // pour chaque jour à afficher
    {
        $query = "SELECT date_debut,date_fin,PRID FROM absences WHERE afficher='1' AND TO_DAYS('$date_courante') >= TO_DAYS(date_debut) AND TO_DAYS('$date_courante') <= TO_DAYS(date_fin)";
        $liste_date=mysqli_query($base_id,$query);

        for($i=0;$i<mysqli_num_rows($liste_date);$i++)  // pour chaque absence
        {
            // affiche le jour sur la première ligne d'une série d'absences

            if(($i == 0 || $nb_ligne==21) && $nb_ligne < 40)
            {
                list($y,$m,$d) = explode("-",$date_courante);
                $j = dateFR($date_courante);
                $jour = $j . " " . $d . "/" . $m . "/" . $y;
                //*********************************************** saut de colonne ***************************
                if ($nb_ligne == 20) $nb_ligne = 21; // evite date orpheline
                if ($nb_ligne ==21)
                    {
                    echo "</table></td><td valign='top' width='50%'><table width='100%'>";
                    }
                $nb_ligne++;
                //*********************************************** date ***************************
                echo "<tr style='line-height: 2; text-align: center; background-color: #00ffcc; font-size: 2em;  font-weight: bold;'><td colspan='3'>".$jour."</td></tr>";
                //*********************************************** date ***************************

            }
            // calcul de la durée à afficher
            $date_d = mysqli_result($liste_date,$i,'date_debut');
            $date_f = mysqli_result($liste_date,$i,'date_fin');
            $horaire;
            list($date_debut, $heure_debut) = explode(" ", $date_d);
            list($date_fin, $heure_fin) = explode(" ", $date_f);
            $heure_debut = substr($heure_debut,0,5);
            $heure_fin = substr($heure_fin,0,5);

            $horaire = $heure_debut . " - " . $heure_fin;

            if($heure_debut == "08:00" && $heure_fin == "13:00")
                $horaire = 'Matin';
            if($heure_debut == "08:00" && $heure_fin == "18:00")
                $horaire = 'Journée';
            if($heure_debut == "13:00" && $heure_fin == "18:00")
                $horaire = 'Apres-Midi';

            // recupere le nom du prof et la discipline
            $query = "SELECT * FROM personnel WHERE personnel.PRID = ".mysqli_result($liste_date,$i,'PRID');
            $personne=mysqli_query($base_id,$query);
            $nom = mysqli_result($personne,0,"civilite")." ".mysqli_result($personne,0,"nom");
            $discipline = mysqli_result($personne,0,"discipline");

            $nb_ligne++;
            if ($nb_ligne < 41)
            {
            //*************************************************** détails *************************************************
            echo "<tr style='font-size: 2em; line-height: 1.5;'><td width='20%'>".$horaire."</td>
            <td> ".$nom." (".$discipline.")</td></tr>\n";
            //*************************************************** détails *************************************************
            }

        }
        // passe à la date suivante
        $date_courante = date('Y-m-d', strtotime("+". $k. " day"));
    }
    if ($nb_ligne < 20) echo "</table></td><td valign='top' width='50%'><table width='100%'>";
    echo "</td></tr></table>";
    ?>

    </table>
</body>
</html>
