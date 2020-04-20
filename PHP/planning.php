<?php
require_once("./menu.php");
include('planning_func.php');
$menu = affiche_menu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<link href="css/menu.css" type="text/css" rel="stylesheet" />
	<link href="css/infobulle.css" type="text/css" rel="stylesheet" />
	<link href="css/planning.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="yui/css/fonts-min.css" />
	<link rel="stylesheet" type="text/css" href="yui/css/calendar.css" />
	<script type="text/javascript" src="yui/js/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="yui/js/calendar-min.js"></script>
	<script type="text/javascript" src="js/infobulle.js"></script>
	<script type="text/javascript" src="js/form_select.js"></script>
	<script type="text/javascript" src="js/calendar_planning.js"></script>
	<title>Planning</title>
</head>

<body class="yui-skin-sam">
<?php
echo $menu;
echo "<div class='corps'>";
$date = (ISSET($_GET['d'])) ? mktime(0,0,0,$_GET['m'], $_GET['d'],$_GET['y']) :  time("now");
$n = (isset($_GET['n'])) ? $_GET['n'] : 7;
$semaine_debut=date("Y-m-d", strtotime("-" . date("w", $date)+1 . " day", $date));
$semaine_fin=date("Y-m-d", strtotime("-" . date("w", $date)+$n . " day", $date));
list($y, $m, $d) = explode("-", $semaine_debut);
$date = mktime(0, 0, 0, $m ,$d, $y);
$affichage = isset($_GET['affichage']) ? $_GET['affichage'] : 'prof';
$listExpand = isset($_GET['exp']) ? explode(',', $_GET['exp']) : array();

// formulaire de selection et tri
?>
	</br>
	Affichage:
	<form name='critere'>
		<select id='affichage' name='affichage' onchange='document.critere.submit()'>
			<option value='exam'>Examen</option>
			<option value='prof'>Professeur</option>
		</select>
		<select id='n' name='n' onchange='document.critere.submit()'>
			<option value='7'>1 semaine</option>
			<option value='14'>2 semaines</option>
			<option value='28'>1 mois</option>
		</select>
		<input type='submit' value='Afficher'>
	</form>
<?php
list($y,$m,$d) = explode('-', $semaine_debut);
echo "</br>Du <b id='semaine_debut'>" . $d . "/" . $m . "/" . $y . "</b> au <b>";
list($y,$m,$d) = explode('-', $semaine_fin);
echo $d . "/" . $m . "/" . $y . "</b>";
    echo "
        <div id=\"cal1Container\"></div>
        <div id=\"caleventlog\" class=\"eventlog\">
        <div id=\"evtentries\" class=\"bd\"></div>";
if($affichage == 'prof')
    displayProf($semaine_debut, $semaine_fin, $date, $n, $listExpand);
else
    displayExam($semaine_debut, $semaine_fin, $date, $n, $listExpand);
?>
<div id="curseur" class="infobulle"></div>
<div style="clear:both"></div>

<script type="text/javascript">
    setSelected('affichage', '<?php echo $affichage ?>');
    setSelected('n', '<?php echo $n ?>');
</script>
</div>
</div>
</body>
</html>
