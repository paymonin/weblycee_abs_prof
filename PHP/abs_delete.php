<?php
include('db_fonction.php');
$base_id = connecti_db();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>suppression d'une absence</title>
</head>
<body>
<?php

            echo "<h1>Suppression de l'absence ".$abid." </h1>";
            echo "<br>";
            echo $myquery = "DELETE FROM absences WHERE ABID=" . $_GET['abid'];
            mysqli_query($base_id,$myquery);
            echo "<br>";
            echo $other_query = "DELETE FROM conflits WHERE ABID1=".$_GET['abid']." OR ABID2=".$_GET['abid'];
            mysqli_query($base_id,$other_query);

?>
<script type="text/javascript">
// Close And Refresh
   window.close();
</script>
</body>
