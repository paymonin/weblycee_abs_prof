<?php
require('./fpdf/fpdf.php');

// par défaut les marges sont de 10mm l'unité est le mm

class PDF extends FPDF
{
var $titre_page;
var $widths;
var $aligns;
//En-tête
    function Header() // methode appelée par addpage à la création de chaque page
    {
        //Logo
        $this->Image('../logo.png',10,8,25);
        //Police Arial gras 15
        $this->SetFont('Arial','B',15);
        //Décalage à droite
        $this->SetXY(50,10);
        //Titre
        $this->Cell(130,10,$this->titre_page,1,0,'C');
        //date
        //Police Arial gras 15
        $this->SetXY(50,18);
        $this->SetFont('Arial','',7);
        setlocale(LC_TIME, "fr_FR.utf8");
        $this->Cell(130,10,utf8_decode('imprimé le ').utf8_decode(strftime('%A %d %B %Y à %H:%M')),0,1,'L');
    }

    //Pied de page
    function Footer() // methode appelée par addpage à la création de chaque page
    {
        //Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        //Police Arial italique 8
        $this->SetFont('Arial','I',8);
        //Numéro de page
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    function SetWidths($w) // l'argument est une table de valeurs
    {
        //Tableau des largeurs de colonnes
        $this->widths=$w;
    }

    function SetAligns($a)  // l'argument est une table de valeurs
    {
        //Tableau des alignements de colonnes 'L' left , 'C' center ou 'R' right
        $this->aligns=$a;
    }

    function Row($data) // l'argument est une table de valeurs
    {
        //Calcule la hauteur de la ligne
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Effectue un saut de page si nécessaire
        $this->CheckPageBreak($h);
        //Dessine les cellules
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Sauve la position courante
            $x=$this->GetX();
            $y=$this->GetY();
            //Dessine le cadre
            //$this->Rect($x,$y,$w,$h);
            //Imprime le texte
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Repositionne à droite
            $this->SetXY($x+$w,$y);
        }
        //Va à la ligne
        $this->Ln($h);
    }

    function CRow($data) // l'argument est une table de valeurs
    {
        //Calcule la hauteur de la ligne
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Effectue un saut de page si nécessaire
        $this->CheckPageBreak($h);
        //Dessine les cellules
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Sauve la position courante
            $x=$this->GetX();
            $y=$this->GetY();
            //Dessine le cadre
            $this->Rect($x,$y,$w,$h);
            //Imprime le texte
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Repositionne à droite
            $this->SetXY($x+$w,$y);
        }
        //Va à la ligne
        $this->Ln($h);
    }


    function CheckPageBreak($h)
    {
        //Si la hauteur h provoque un débordement, saut de page manuel
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}

class BORDEREAU extends PDF
{

    function nom($civilite,$nom,$prenom)
    {
        // place le nom et prénom aligné à 10 mm de la droite de la page
        $this->SetX(-110);
        $this->SetFont('Times','B',14);
        $this->Cell(100,8,$civilite." ".$nom." ".$prenom,0,1,"R");
    }

    function haut($intro)
    {
        // place le texte de l'intro
        $this->SetY(35);
        $this->SetFont('Times','',12);
        $this->MultiCell('',5,$intro."\n ",0,1);


    }
    function bas($bas)
    {
        $this->SetFont('Times','',12);
        $this->SetX(-90);
        $this->MultiCell(80,12,"\n ".$bas,0,'L');
    }

    function titre_table($categorie)
    {

        $this->SetFont('Times','',8);
        $this->SetWidths(array(18,8,20,55,60));
        $this->SetAligns(array('C','C','C','C','C'));
        $this->CRow(array(
        "Jour",
        "Mois",
        "Durée",
        "LIEU",
        "MOTIF"));
    }

    function detail_table($jour,$mois,$tranche,$lieux,$motif)
    {
        $this->SetFont('Times','',8);
        $this->SetWidths(array(18,8,20,55,60));
        $this->SetAligns(array('C','C','C','C','C'));
        $this->Row(array(
        $jour,
        $mois,
        $tranche,
        $lieux,
        $motif));

    }
    function signature()
    {
        $this->SetFont('Times','',11);
        $this->Rect(65,260,130,20);
        $this->SetXY(67,260);
        $this->Write(12,utf8_decode("convocation rétirée le ......./......./.............."));
        $this->Write(12,"      Signature:");

    }
    function date_impression()
    {
        $this->SetFont('Arial','',10);
        setlocale(LC_TIME, "fr_FR.utf8");
        $this->Cell(190,10,utf8_decode('imprimé le ').utf8_decode(strftime('%A %d %B %Y à %H:%M')),0,1,'L');
    }
}
?>
