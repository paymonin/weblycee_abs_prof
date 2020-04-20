<?php
require "./modele.php";
require "./db_fonction.php";

// ###############selection de l'absence  ou des absences de même catégorie #########################################################

$abID = $_GET['abID']; // ID de l'absence passée en GET dans l'URL
$prID = $_GET['prID']; // récupère l'ID du prof
$categorie = $_GET['categorie']; // récupère la categorie d'absence
$printstatus = $_GET['printstatus']; // récupère la categorie d'absence

// prend toutes les absences de mêmes catégorie non imprimées ou seulement l'absences selectionnée si elle est déja imprimée
$base_id = connecti_db();
$query = "SELECT * FROM personnel,absences,lieux WHERE absences.LID = lieux.LID AND personnel.PRID = absences.PRID AND absences.PRID=".$prID." AND absences.categorie='".$categorie."' ";
$query .= " AND absences.preset = 0 ";
if($printstatus==0) $query .= "AND absences.print=0"; else $query .="AND absences.ABID =".$abID;  // si avis déja imprimé, ne réimprime que cet avis, sinon imprime tous les avis non imprimé de même catégorie
// echo $query."<br>"; // DEBUG

$abs=mysqli_query($base_id,$query); //Liste des absences à imprimer pour ce profs
// echo "<br>lignes:".mysqli_num_rows($abs); // DEBUG

// détermine 48h avant la 1ère convocation

$last = mysqli_num_rows($abs);
for ($i=0;$i<$last;$i++)
{
$unix_time_limite = strtotime(mysqli_result($abs,$i,'date_debut'));
if ($unix_time_limite >= strtotime(mysqli_result($abs,$i,'date_debut')))
    {
    $unix_time_limite = strtotime(mysqli_result($abs,$i,'date_debut'));
    $limite = mysqli_result($abs,$i,'date_debut');
    }
}

// détermine le gestionnaire de la première convocation et le lieu de retrait

$query_gest = "SELECT usertxt FROM users WHERE username = '".mysqli_result($abs,0,'gest')."'";
$result_gest = mysqli_query($base_id,$query_gest);
$usertxt = mysqli_result($result_gest,0,'usertxt');

// ###################### Selection des textes du bordereau  ################################################################

$querytxt =  "SELECT * FROM textes WHERE categorie = '".$categorie."'";
$textes = mysqli_query($base_id,$querytxt);

// passage de utf8 à iso pour les classes fpdf
$titre_page = utf8_decode(mysqli_result($textes,0,"titre"));

$intro = utf8_decode(mysqli_result($textes,0,"intro_multi"));
$usertxt = utf8_decode($usertxt);
$intro = str_replace( '[gest]',$usertxt,$intro);

$bas = utf8_decode(mysqli_result($textes,0,"bas_intro_multi"));


if ($categorie == "Examen" || $categorie == "Stage") $bordereau_double = true;
else $bordereau_double = false;

// ####################### composition du bordereau     ########################################################################

setlocale(LC_TIME, "fr_FR.utf8"); // bascule les format de date en français

$pdf=new BORDEREAU(); // crée un bordereau basée sur modele.php

// ------------------------   création page -----------------------
$pdf->titre_page = $titre_page;
$pdf->AliasNbPages();
$pdf->AddPage();

$civilite = mysqli_result($abs,0,"civilite");
$nom = utf8_decode(mysqli_result($abs,0,"nom"));
$prenom = utf8_decode(mysqli_result($abs,0,"prenom"));

$pdf->haut($intro);

$pdf->titre_table($categorie);
$next_abs_haut = $pdf->GetY();

$pdf->SetY(22);
$pdf->nom($civilite,$nom,$prenom);

if ($bordereau_double)
{
    $pdf->SetFont('Times','B',12);
    $pdf->SetXY(40,155); // cadre accusé de réception
    $pdf->Cell(160,8,utf8_decode(" ACCUSE DE RECEPTION imprimé le ").utf8_decode(strftime('%A %d %B %Y à %H:%M')),1,0,'C');

    $pdf->SetXY(20,165); // cadre nom prénom
    $pdf->SetFont('Times','B',14);
    $pdf->Cell(100,8,$civilite." ".$nom." ".$prenom,0,1,"L");

    list($date_limite, $heure_limite) = explode(" ", $limite);
    $date_limite = date('Y-m-d',strtotime($date_limite.' - 2 days'));
    list($annee_limite,$mois_limite,$jour_limite) = explode("-",$date_limite);
    $date_limite = $jour_limite."/".$mois_limite."/".$annee_limite;

    $pdf->SetFont('Times','',12);
    $pdf->SetXY(20,175); // choix 1
    $pdf->Cell(4,4,"",1,0,'L');
    $pdf->Write(4,utf8_decode("Je me rends à la convocation, fiche d'absence/remplacement à rendre avant le "));
    $pdf->SetFont('Times','B',12);
    $pdf->Write(4,$date_limite);

    $pdf->SetFont('Times','',12);
    $pdf->SetXY(20,182); // choix 2
    $pdf->Cell(4,4,"",1,0,'L');
    $pdf->Cell(5,5,utf8_decode("Je ne me rends pas à la convocation pour le motif :"),0,0,'L');

    $pdf->SetXY(10,200); // tableau ligne de titre
    $pdf->titre_table($categorie);
    $next_abs_bas = $pdf->GetY();



}


// ------------------------   détails  -----------------------

for ($i=0;$i<$last;$i++)
    {
    // -------- collecte des infos -----------
    $motif = utf8_decode(mysqli_result($abs,$i,"examen"));
    $detail = utf8_decode(mysqli_result($abs,$i,"details"));
    $lieux = utf8_decode(mysqli_result($abs,$i,"nom_lieu"));
    $date_d = mysqli_result($abs,$i,'date_debut');
    $date_f = mysqli_result($abs,$i,'date_fin');

    list($date_debut, $heure_debut) = explode(" ", $date_d);
    list($date_fin, $heure_fin) = explode(" ", $date_f);

    list($annee_debut,$mois_debut,$jour_debut) = explode("-",$date_debut);
    list($annee_fin,$mois_fin,$jour_fin) = explode("-",$date_fin);
    $joursemdebut = dateFR($date_debut);
    $joursemfin = dateFR($date_fin);

    $horaire;
    $heure_debut = substr($heure_debut,0,5);
    $heure_fin = substr($heure_fin,0,5);
    if($date_debut == $date_fin)
    {
        if($heure_debut == "08:00")
        {
            if($heure_fin == "13:00")
                $horaire = 'Matin';
            elseif ($heure_fin == "18:00")
                $horaire = 'Journée';
        }
        elseif($heure_debut == "13:00" && $heure_fin == "18:00")
            $horaire = 'Apres-Midi';
        else
        $horaire = $heure_debut . " - " . $heure_fin;
    }
    else
        $horaire = " au ";

    //-------------------- lignes pour chaque absence -----------------------------

    $pdf->SetY($next_abs_haut);
    if ($date_debut == $date_fin)
    {
        $pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,$lieux,$motif."  ".$detail);
    }
    else
    {
        $inter = intval((strtotime($date_fin)-strtotime($date_debut))/86400)+1;
        $pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,"","");
        $pdf->detail_table($joursemfin." ".$jour_fin,$mois_fin,$inter." jours",$lieux,$motif."  ".$detail);
    }
    $next_abs_haut = $pdf->GetY();
    if ($i == $last -1)
    {
        $pdf->bas($bas);
    }
    if ($bordereau_double)
    {
        $pdf->SetY($next_abs_bas);
        if ($date_debut == $date_fin)
        {
            $pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,$lieux,$motif."  ".$detail);
        }
        else
        {
            $inter = intval((strtotime($date_fin)-strtotime($date_debut))/86400)+1;
            $pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,"","");
            $pdf->detail_table($joursemfin." ".$jour_fin,$mois_fin,$inter." jours",$lieux,$motif."  ".$detail);
        }
        $next_abs_bas = $pdf->GetY();
        if ($i == $last -1)
        {
        $pdf->signature();
        }
    }
    // enregistre l'absence comme imprimée
    $update_print = mysqli_query($base_id,"UPDATE absences SET print='1' WHERE absences.PRID=".$prID." AND absences.categorie='".$categorie."'");

    }
$pdf->Output();

?>
