<?php
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Pragma: no-cache"); // HTTP/1.0
header('Content-Type: text/xml;charset=utf-8');
echo "<?xml version='1.0' encoding='UTF-8' ?>";

$liste = array();
include('db_fonction.php');
$base_id = connecti_db();

$requete;
$result;
$input;
$field;

if(isset($_GET['discipline']))
{
    $input = strtolower($_GET['discipline']);
    $field='discipline';
    $table = 'personnel';
}
if(isset($_GET['poste']))
{
    $input = strtolower($_GET['poste']);
    $field='poste';
    $table = 'personnel';
}
if(isset($_GET['nom']))
{
    $input = strtolower($_GET['nom']);
    $field='nom';
    $table = 'personnel';
}
if(isset($_GET['prenom']))
{
    $input = strtolower($_GET['prenom']);
    $field='prenom';
    $table = 'personnel';
}
if(isset($_GET['ordonateur']))
{
    $input = strtolower($_GET['ordonateur']);
    $field='ordonateur';
    $table='absences';
}
if(isset($_GET['lieux']))
{
    $input = strtolower($_GET['lieux']);
    $field='nom_lieu';
    $table='lieux';
}
if(isset($_GET['details']))
{
    $input = strtolower($_GET['details']);
    $field='details';
    $table='absences';
}
if(isset($_GET['motif']))
{
    $input = strtolower($_GET['motif']);
    $field='motif';
    $table='absences';
}
if(isset($_GET['examen']))
{
    $input = strtolower($_GET['examen']);
    $field='examen';
    $table='absences';
}


$requete = "SELECT $field FROM $table GROUP BY $field ORDER BY $field";
$result = mysqli_query($base_id,$requete);
for($j=0; $j < mysqli_num_rows($result); $j++)
   array_push ($liste, mysqli_result($result, $j));

echo "<results>";
$i = 1;
$len = strlen($input);
foreach ($liste as $element)
{
    //if (strtolower(substr($element,0,$len)) == $input)
    if (preg_match("/".$input."/i",$element))
            echo "<rs id=\"".$i."\" info=\"\">".$element."</rs>";
            $i++;
}


echo("</results>");
/*
echo "<?xml version='1.0' encoding='UTF-8' ?><results><rs id=\"1\" info=\"\">test</rs><rs id=\"2\" info=\"\">test42</rs></results>"
 */
?>
