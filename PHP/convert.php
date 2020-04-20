<?php

function convertPrint()
{
    $base1 = mysql_pconnect ('localhost', 'root', 'lldjolcdt') or die("c1");
    $base2 = mysql_pconnect ('localhost', 'abs_db', 'qnjtvpvc') or die("c2");
    mysql_select_db('absences',$base1) or die("what");
    $liste = mysqli_query($base_id,"SELECT print,AbID FROM absences",$base1) or die("Fuck");
    mysql_select_db('absence_prof',$base2);
    for($i=0;$i<mysqli_num_rows($liste);$i++)
    {
        $query = "UPDATE absences SET print=".mysqli_result($liste, $i, 'print')." WHERE ABID = ".mysqli_result($liste, $i,'AbID');
        mysqli_query($base_id,$query, $base2);
        echo $query;
        echo "</br>";
    }
}

function convertProfs()
{
    $base1 = mysql_pconnect ('127.0.0.1', 'root', 'lldojlcdt');
    $base2 = mysql_pconnect ('127.0.0.1', 'abs_db', 'qnjtvpvc');
    mysql_select_db('absences',$base1);
    $liste_prof = mysqli_query($base_id,"SELECT PrID,nom,prenom,discipline,statut,civilite FROM profs ORDER BY PrID",$base1);
    mysql_select_db('absence_prof',$base2);
    for($i=0;$i<mysqli_num_rows($liste_prof);$i++)
    {
        $prid = mysqli_result($liste_prof, $i, 'PrID');
        $civilite = mysqli_result($liste_prof, $i, 'civilite');
        $nom = mysqli_result($liste_prof, $i, 'nom');
        $prenom = mysqli_result($liste_prof, $i, 'prenom');
        $discipline = mysqli_result($liste_prof, $i, 'discipline');
        $requete = "INSERT INTO personnel (PRID, civilite, nom, prenom, poste, discipline) VALUES('$prid', '$civilite', '$nom', '$prenom', '', '$discipline')";
        mysqli_query($base_id,$requete, $base2);
    }
    mysql_close($base1);
    mysql_close($base2);
}

function convertLieu()
{
    $base1 = mysql_pconnect ('localhost', 'root', 'lldjolcdt') or die("c1");
    $base2 = mysql_pconnect ('localhost', 'abs_db', 'qnjtvpvc') or die("c2");
    mysql_select_db('absences',$base1);
    $liste = mysqli_query($base_id,"SELECT lieux FROM absences GROUP BY lieux",$base1);
    mysql_select_db('absence_prof',$base2);
    for($i=0;$i<mysqli_num_rows($liste);$i++)
    {
        $lieu = mysqli_result($liste, $i, 'lieux');
        $lieu = addslashes($lieu);
        echo $i." - ".$lieu;
        echo "</br>";
        if($lieu != '')
        {
            if($lieu == 'LLA Mulhouse')
        $requete = "INSERT INTO lieux (LID, nom_lieu, interne, capacite, adresse, temps_trajet, distance_trajet) VALUES('', 'LLA', 1, '', '', '', '')";
            else
        $requete = "INSERT INTO lieux (LID, nom_lieu, interne, capacite, adresse, temps_trajet, distance_trajet) VALUES('', '$lieu', 0, '', '', '', '')";
        mysqli_query($base_id,$requete, $base2);
        }
    }
    mysql_close($base1);
    mysql_close($base2);
}

function convertAbsences()
{
    $base1 = mysql_pconnect ('localhost', 'root', 'lldjolcdt') or die("c1");
    $base2 = mysql_pconnect ('localhost', 'abs_db', 'qnjtvpvc') or die("c2");
    mysql_select_db('absences',$base1);
    $liste = mysqli_query($base_id,"SELECT * FROM absences",$base1);
    mysql_select_db('absence_prof',$base2);
    echo mysqli_num_rows($liste)." lignes à importer <BR>";
    for($i=0;$i<mysqli_num_rows($liste);$i++)
    {
        $date_saisie = mysqli_result($liste, $i, 'date_saisie');
        $abid = mysqli_result($liste, $i, 'AbID');
        $prid = mysqli_result($liste, $i, 'PrID');
        $confirm = mysqli_result($liste, $i, 'confirm');
        $print = mysqli_result($liste, $i, 'print');
        $date_saisie = mysqli_result($liste, $i, 'date_saisie');
        //date debut et fin
        $date_abs = mysqli_result($liste, $i, 'date_abs');
        $tranche = mysqli_result($liste, $i, 'tranche');
        $debut = ($tranche == 'JOURNEE' || $tranche == 'MATIN') ? " 08:00:00" : " 13:00:00";
        $fin = ($tranche == 'JOURNEE' || $tranche == 'APRES-MIDI') ? " 18:00:00" : " 13:00:00";
        $date_debut = $date_abs . $debut;
        $date_fin = $date_abs . $fin;
        //
        $categorie = mysqli_result($liste, $i, 'categorie');
        $examen = mysqli_result($liste, $i, 'motif');
        $details = mysqli_result($liste, $i, 'detail');
        //lieux
        $nom = mysqli_result($liste, $i, 'lieux');
        $lid;
        if($nom == '')
            $lid = 0;
        else
        {
            if($nom == 'LLA Mulhouse')
                $nom = 'LLA';
            $requete = "SELECT LID from lieux WHERE nom_lieu='" . $nom . "'";
            //echo $requete;
            $foo = mysqli_query($base_id,$requete, $base2);
            if(is_bool($foo))
                $lid = 0;
            else
                $lid = mysqli_result($foo, 0, 'LID');
        }
        echo "LID ";$lid." - ";
        $ordonateur = mysqli_result($liste, $i, 'ordonateur');
        $ordonateur = addslashes($ordonateur);
        $categorie = addslashes($categorie);
        $examen = addslashes($examen);
        $details = addslashes($details);
        $etat = mysqli_result($liste, $i, 'confirm');
        $affiche = mysqli_result($liste, $i, 'affiche');
        $requete = "INSERT INTO absences (date_saisie,ABID,PRID,date_debut,date_fin,categorie,examen,details,ordonateur,etat,afficher,LID,fait,print) VALUES('$date_saisie', $abid, $prid, '$date_debut','$date_fin','$categorie','$examen','$details', '$ordonateur', $etat,$affiche,$lid,$confirm,$print)";
        echo "ligne ".$i." - ".$requete."<br>";
        mysqli_query($base_id,$requete, $base2) or die('Erreur de selection '.mysql_error());
    }
    mysql_close($base1);
    mysql_close($base2);
}

//convertPrint();
//convertLieu();
//convertAbsences();
//convertProfs();
?>
