<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>préparation bordereau</title>
    <link href="absences.css" rel="stylesheet" type="text/css">
<link href="css/menu.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/form_select.js"></script>
</head>
<body>
<h1>Préparation du document</h1>

<?php
require "db_fonction.php";

$abID = $_GET['abID']; // ID de l'absence passée en GET dans l'URL

// retrouve l'ID du prof et la categorie d'absence
$base_id = connecti_db();
$query = "SELECT PRID,categorie,print FROM absences WHERE ABID = ".$abID;
$profID=mysqli_query($base_id,$query);
$prID = mysqli_result($profID,0,"PRID"); // récupère l'ID du prof
$categorie = mysqli_result($profID,0,"categorie"); // récupère la categorie d'absence
$printstatus = mysqli_result($profID,0,"print"); // recupere le statut d'impression

// ouvre le popup du PDF
echo "<script type=\"text/javascript\">";
echo "window.open('avis_pdf.php?abID=".$_GET['abID']."&prID=".$prID."&categorie=".$categorie."&printstatus=".$printstatus."','pdf','menubar=no, status=no, scrollbars=no, menubar=no, width=600, height=800');";
echo "</script>";

?>

<script type="text/javascript">
// Close And Refresh
    setTimeout("window.opener.location.href='absences.php'",4000);
    setTimeout("window.close()",4200);
</script>
</body>
