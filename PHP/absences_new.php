<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Saisie absence</title>
        <link rel="stylesheet" type="text/css" href="yui/css/fonts-min.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/slider.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/calendar.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/button.css" />
        <link rel="stylesheet" type="text/css" href="css/slider.css" />
        <link href="absences.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    </head>
    <body class="yui-skin-sam">
        <?php
        require_once("./menu.php");
        include("./abs_table.inc.php");
        $menu = affiche_menu();
        echo $menu;
        ?>
        <div class='corps'>
        <form name="dates">
        <?php
        include('db_fonction.php');
        $base_id = connecti_db();
        //###################################################################################
        // 1er appel de la page sans $_GET['action'] valeurs par défaut pour le formulaire:
        //###################################################################################
        if(!isset($_GET['action']))
        {
            // definition des valeurs du formulaire par défaut
            //echo "**** First call ****"; //DEBUG
            // prof id
            $prid;
            //date
            if (isset($_SESSION['date_abs']))
                {
                $start_date = $_SESSION['date_abs'];
                $end_date = $start_date;
                //echo "session ".$start_date; //DEBUG
                }
            else
                {
                setlocale(LC_TIME, "fr_FR.utf8");
                // date au format fr
                $start_date = date("d/m/Y");
                $end_date = $start_date;
                //echo "no session ".$start_date; //DEBUG
                }
            // categorie
            $categorie = "Stage";
            //lieux
            $lid;
            //ordonateur
            $ordonateur;
            //examen
            $examen;
            //detail
            $details;
            //pré saisie
            if ($_SESSION['userType']==1) $presaisie = 0;
            if ($_SESSION['userType']==2) $presaisie = 1;
            // gestionnaire de l'abscence
            $gest = $_SESSION['username'];
            // fichier joint
            $file;
            // champ action caché
            $action=1; // pour inserer au prochain appel
        }
        //###################################################################################
        // appel de la page sur un $_GET['action'] = 1-->INSERT 2-->UPDATE 3-->EDIT 4--> COPY 5-->GESTION CONFLIT
        //###################################################################################

        // #####################################################################################
        else if (isset($_GET['action']))
        {
            //echo "action".$_GET['action']; //DEBUG
            //##################################################################################
            if($_GET['action'] == 1 || $_GET['action'] == 2) // INSERT ou UPDATE
                {
                // definition des valeurs du formulaire depuis le GET
                // PRID
                $prid = $_GET['prid'];
                //date
                $start_date = $_GET['start_date'];
                $mysql_start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)))." ".$_GET['bselHour'].":".$_GET['bselMin'].":00";
                // mise en mémoire session de la date
                $_SESSION['date_abs'] = $start_date;
                $end_date = $_GET['end_date'];
                $mysql_end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)))." ".$_GET['eselHour'].":".$_GET['eselMin'].":00";
                //categorie
                $categorie = $_GET['categorie'];
                //lieux
                if(!isset($_GET['lieux']))
                    $lid = 0;
                else
                {
                    $lieu = $_GET['lieux'];
                    if(strlen($lieu)>0){
                        $lidquery = "SELECT LID from lieux WHERE nom_lieu = '" . $_GET['lieux'] . "'";
                        $res = mysqli_query($base_id,$lidquery);
                        if(mysqli_num_rows($res) == 0)
                        {
                            $myquery = "INSERT INTO lieux (LID, nom_lieu) VALUES('','".$_GET['lieux']."')";
                            mysqli_query($base_id,$myquery);
                            $lidquery = "SELECT LID from lieux WHERE nom_lieu = '" . mysqli_real_escape_string($base_id,$_GET['lieux']) . "'";
                            $res = mysqli_query($base_id,$lidquery);
                        }
                        $lid = mysqli_result($res,0,"LID");
                    }
                    else {
                        $lid = 0;
                    }
                }
                //ordonateur
                if(!isset($_GET['ordonateur']))
                    $ordonateur = '';
                else
                    $ordonateur = $_GET['ordonateur'];
                //examen
                if(!isset($_GET['examen']))
                    $examen='';
                if ($_GET['categorie'] == "Examen" && ($_GET['examen']) == '')
                    $examen = "Non defini";
                else
                    $examen = $_GET['examen'];
                //detail
                if(!isset($_GET['details']))
                    $details = '';
                else
                    $details = $_GET['details'];
                //pré saisie
                if (isset($_GET['pre-saisie'])) $presaisie = 1;	else $presaisie = 0;
                // gestionnaire de la convocation
                if (!isset($_GET['gest']))
                    $gest = $_SESSION['username'];
                else
                    $gest = $_GET['gest'];

                $date_saisie = date("Y-m-d H:i:s");

                // ############################################################# INSERT  ###########################
                if($_GET['action'] == 1){
                    $query = "INSERT INTO absences(date_saisie,
                        PRID,
                        date_debut,
                        date_fin,
                        categorie,
                        examen,
                        details,
                        ordonateur,
                        LID,
                        user,
                        afficher,
                        preset,
                        gest)
                        VALUES('".$date_saisie."',"
                        .$prid.",'"
                        .$mysql_start_date."','"
                        .$mysql_end_date."','"
                        .$_GET['categorie']."','"
                        .mysqli_real_escape_string($base_id,$examen)."','"
                        .mysqli_real_escape_string($base_id,$details)."','"
                        .mysqli_real_escape_string($base_id,$ordonateur)."','"
                        .$lid."','".$_SESSION['username']."','0',"
                        .$presaisie
                        .",'".$gest."')";
                        //echo "<BR>".$query;        //DEBUG
                        mysqli_query($base_id,$query) or die();
                        $abid = mysqli_insert_id($base_id); // récupère l'ABID de la dernière insertion
                        echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
                        echo "<!-- INSERT -->"; // DEBUG
                        $test_conflit = true;
                    }
                    //################################################################  UPDATE ###################
                if($_GET['action'] == 2 || $_GET['action'] == 5) // UPDATE absence(2) ou UPDATE absence et conflit(5)
                    {
                    $query = "UPDATE absences SET
                        date_saisie='".date("Y-m-d H:i:s")."',
                        PRID=".$prid.",
                        date_debut='".$mysql_start_date."',
                        date_fin='".$mysql_end_date."',
                        categorie='".$_GET['categorie']."',
                        examen='".mysqli_real_escape_string($base_id,$examen)."',
                        details='".mysqli_real_escape_string($base_id,$details)."',
                        ordonateur='".mysqli_real_escape_string($base_id,$ordonateur)."',
                        print=0,
                        LID=".$lid.",
                        preset=".$presaisie.",
                        user='".$_SESSION['username']."',
                        gest='".$gest."' WHERE ABID=".$_GET['abid'];
                        //echo "<BR>".$query;        //DEBUG
                        mysqli_query($base_id,$query) or die();
                        $abid = $_GET['abid'];
                        echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
                        echo "<!-- UPDATE -->"; // DEBUG
                        $test_conflit = true;
                    }
                }

            //####################################################### EDIT ou COPY ############################
            if ($_GET['action'] == 3 ||  $_GET['action'] == 4) // EDIT
                {
                $abid = $_GET['abid'];
                // definition des valeurs du formulaire depuis les données de la base
                $query = "SELECT * from absences where ABID=" . $abid;
                $abs_data = mysqli_query($base_id,$query);

                // PRID
                $prid = mysqli_result($abs_data,0,'PRID');
                //dates et heure
                $mysql_start_date = mysqli_result($abs_data,0,'date_debut');
                $start_date = date('d/m/Y',strtotime($mysql_start_date));
                $mysql_end_date = mysqli_result($abs_data,0,'date_fin');
                $end_date = date('d/m/Y',strtotime($mysql_end_date));
                $array_debut = date_parse(mysqli_result($abs_data,0,'date_debut'));
                $array_fin = date_parse(mysqli_result($abs_data,0,'date_fin'));
                // categorie
                $categorie = mysqli_result($abs_data,0,'categorie');
                // lieux
                $lid = mysqli_result($abs_data,0,'LID');
                //ordonateur
                $ordonateur = mysqli_result($abs_data,0,'ordonateur');
                //examen
                $examen = mysqli_result($abs_data,0,'examen');
                //detail
                $details = mysqli_result($abs_data,0,'details');
                //pré saisie
                if (mysqli_result($abs_data,0,'preset') == 1) $presaisie = 1; else $presaisie = 0;
                // gestionnaire de la convocation
                $gest = mysqli_result($abs_data,0,'gest');
                echo "EDIT OU COPY";
                // ############################## EDIT = rappel avec un UPDATE
                if ($_GET['action'] == 3)
                    {
                    $action=2;
                    echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
                    //echo "<br>EDIT"; //DEBUG
                    $edit_flag = true;
                    }

                //#############################  COPY = rappel avec un INSERT ##
                if ($_GET['action'] == 4)
                {
                    $action=1;
                    echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
                    //echo "<br>COPY"; //DEBUG
                }
            }

            //###################################################################################
            if($_GET['action'] == 5) // MISE A JOUR DES CONFLITS
                {
                foreach($_GET['cfid'] as $cfid)
                    {
                    echo $cfid;
                    echo " - ";
                    echo $status = $_GET['status'.$cfid];
                    echo " - ";
                    echo $comment = mysqli_real_escape_string($base_id,$_GET['comment'.$cfid]);
                    echo "<br>";
                    echo $update_conflit_query = "UPDATE conflits SET comment='".$comment."' , status=".$status." WHERE CFID=".$cfid;
                    echo "<br>";
                    mysqli_query($base_id,$update_conflit_query) or die();
                    }
                header('Location: absences.php'); // retour à la page de saisie des absences
                }

            //########################
            // RECHERCHE DE DOUBLONS
            //########################

            if($test_conflit)
            {
                //echo "<br>conflit testés"; // DEBUG
                $conflit_query = "SELECT * FROM absences,personnel,lieux WHERE absences.PRID = personnel.PRID AND absences.LID = lieux.LID AND absences.PRID = ".$prid." AND (
                (absences.date_debut >= '".$mysql_start_date."' AND absences.date_debut <= '".$mysql_end_date."')
                OR (absences.date_fin >= '".$mysql_start_date."' AND absences.date_fin <= '".$mysql_end_date."')
                OR ('".$mysql_start_date."' >= absences.date_debut AND '".$mysql_start_date."' <= absences.date_fin)
                OR ( '".$mysql_end_date."' >= absences.date_debut AND '".$mysql_end_date."' <= absences.date_fin )
                ) AND absences.ABID != ".$abid;
                // echo $conflit_query; // DEBUG
                $conflits = mysqli_query($base_id,$conflit_query) or die();
                $nb_conflits = mysqli_num_rows($conflits);  // nombre d'absences en conflit


                if ($nb_conflits >= 1) // si un conflit détecté
                {
                    $nb_conflits_non_resolu = 0;
                    echo "\n<fieldset id=\"fs_conflits\"><legend>conflit détecté</legend>";
                    echo "<table>";
                    for ($i=0;$i<$nb_conflits;$i++)  // pour chaque absence en conflit, recherhce l'enregistrement de conflit
                        {
                        echo "<tr><td>";
                        $ABID1 = $abid;
                        $ABID2 = mysqli_result($conflits,$i,'ABID');
                        $conflit_exist = mysqli_query($base_id,"SELECT * FROM conflits WHERE ((ABID1=".$ABID1." AND ABID2=".$ABID2.") OR (ABID1=".$ABID2." AND ABID2=".$ABID1."))");
                        if (mysqli_num_rows($conflit_exist) == 0) // pas encore d'enregistrment du conflit
                            {
                            $insert_conflit_query = "INSERT INTO conflits (ABID1,ABID2,comment,status) VALUES (".$ABID1.",".$ABID2.",'',0)";
                            $insert_conflit = mysqli_query($base_id,$insert_conflit_query) or die();
                            $status = 0;
                            $comment = "";
                            $CFID = mysqli_insert_id($base_id); // récupère le CFID de la dernière insertion
                            $abid_en_conflit = $ABID2;
                            }
                        else // enregistrement existant
                            {
                            $CFID = mysqli_result($conflit_exist,0,'CFID');
                            $status = mysqli_result($conflit_exist,0,'status');
                            $comment = mysqli_result($conflit_exist,0,'comment');
                            if (mysqli_result($conflit_exist,0,'ABID1')==$abid) $abid_en_conflit = mysqli_result($conflit_exist,0,'ABID2'); else $abid_en_conflit = mysqli_result($conflit_exist,0,'ABID1');
                            }
                        //echo "conflit n° ".$CFID." - abscence:".$abid_en_conflit." en conflit avec la saissie courante:".$abid; //DEBUG
                        echo "\n<input type=\"hidden\" name=\"cfid[]\" value=".$CFID.">";
                        echo "<table><tr>";
                        echo "\n<td valign=\"center\">explication</td>";
                        echo "\n<td valign=\"center\"><textarea rows='3' cols='80' name='comment".$CFID."'
                            onkeypress=\"javascript:this.value=this.value.substr(0,200);\"
                            onchange=\"javascript:this.value=this.value.substr(0,200);\"
                            >".$comment."</textarea></td>";
                        echo "\n<td valign=\"center\" align=\"center\">Cocher la case<br> pour marquer le conflit comme<br>résolu</td>";
                        echo "\n<td valign=\"center\">";

                        // basculement de la case statut conflit et mise à jour formulaire
                        echo "\n<input id=\"status".$CFID."\" type=\"hidden\" name=\"status".$CFID."\" value=".$status.">";
                        if ($status == 0) $on_off="off"; else $on_off = "on";
                        echo "<img id=\"status_check_".$CFID."\" border=0 src=\"ico/check_".$on_off.".gif\" type=image onclick=\"bascule_status(".$CFID.");\">";
                        ?>
                        <script type="text/javascript">
                        function bascule_status(CFID)
                        {
                        status_checkbox_id = "status"+CFID;
                        mem_status = document.getElementById(status_checkbox_id).value;
                        if(mem_status == 0)
                            {
                            document.getElementById("status_check_"+CFID).src = "ico/check_on.gif";
                            document.getElementById(status_checkbox_id).value = 1;
                            }
                        else
                            {
                            document.getElementById("status_check_"+CFID).src = "ico/check_off.gif";
                            document.getElementById(status_checkbox_id).value = 0;
                            }
                        }
                        </script>
                        <?php
                        echo "</td>";
                        echo "<td><img src=\"ico/bouton_valider.png\" onclick=\"document.dates.submit()\"
                            onmouseover=\"surligne('fs_saisie','vert');surligne('fs_conflits','vert');\"
                            onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\"></td>";
                        echo "</tr></table>";

                        mini_tableau($conflits,$i);
                        echo "</td><td valign='bottom' align='right'>";
                        echo "<img src=\"ico/bouton_echanger.png\" onclick=\"window.location.replace('absences_new.php?action=3&abid=".$abid_en_conflit."');\"
                        onmouseover=\"surligne('fs_saisie','bleu');surligne('fs_conflits','bleu');\"
                        onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\"><br>";
                        echo "<img src=\"ico/bouton_supprimer.png\" onclick=\"window.open('abs_delete.php?abid=".$abid_en_conflit."','launch','menubar=no, status=no, scrollbars=no, width=400, height=200');
                        window.location.replace('absences.php');\"
                        onmouseover=\"surligne('fs_saisie','vert');surligne('fs_conflits','rouge');\"
                        onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\">";
                        echo "</td></tr>";
                        }
                    echo "</table>";
                    $action = 5;
                    echo "\n</fieldset>";
                }
                else
                {
                echo "<br> pas de conflit";
                echo '<script type="text/javascript"> window.location.href = "absences.php"; </script>'; // retour à la page de saisie des absences après un insert ou update sans conflit
                }
            }
        }

        //###################################################################################
        // dessin du formulaire
        //###################################################################################

        ?>
            <fieldset id="fs_saisie"><legend>Saisie courante</legend>
                <fieldset id="fs_identite"><legend>Identite</legend>
                    Nom
                    <select id="prid" name="prid"><?php option_liste_prof(null);?></select>
                </fieldset>
                <fieldset id="fs_date"><legend>Date</legend>
                <table>
                    <tr>
                        <td valign="CENTER" align="CENTER">
                            Debut
                            <input id='start_date' name='start_date' class='select_date' type='text'>
                            Fin
                            <input id='end_date' name='end_date' class='select_date' type='text'>
                        </td>
                    </tr>
                    <tr>
                        <td valign="CENTER" align="CENTER">
                            Debut
                            <select name="bselHour" id="bselHour">
                                <option value="8" selected>8h</option>
                                <option value="9">9h</option>
                                <option value="10">10h</option>
                                <option value="11">11h</option>
                                <option value="12">12h</option>
                                <option value="13">13h</option>
                                <option value="14">14h</option>
                                <option value="15">15h</option>
                                <option value="16">16h</option>
                                <option value="17">17h</option>
                                <option value="18">18h</option>
                            </select>
                            <select name="bselMin" id="bselMin">
                                <option value="0" selected>00</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                            Fin
                            <select name="eselHour" id="eselHour">
                                <option value="8">8h</option>
                                <option value="9">9h</option>
                                <option value="10">10h</option>
                                <option value="11">11h</option>
                                <option value="12">12h</option>
                                <option value="13">13h</option>
                                <option value="14">14h</option>
                                <option value="15">15h</option>
                                <option value="16">16h</option>
                                <option value="17">17h</option>
                                <option value="18" selected>18h</option>
                            </select>
                            <select name="eselMin" id="eselMin">
                                <option value="0" selected>00</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign="CENTER" align="CENTER">
                            <button type="button" id="button_day">Journee</button>
                            <button type="button" id="button_morning">Matin</button>
                            <button type="button" id="button_afternoon">Apres-Midi</button>
                            <div id="demo_bg" title="Range slider">
                                <span id="demo_highlight"></span>
                                <div id="demo_min_thumb"><img src="yui/assets/l-thumb-round.gif"></div>
                                <div id="demo_max_thumb"><img src="yui/assets/r-thumb-round.gif"></div>
                            </div>
                        </td>
                    </tr>
                </table>
                </fieldset>
                <fieldset id="fs_details"><legend>Details de l'absence</legend>
                    <div valign="TOP">Categorie</div>
                    <select id="categorie" onchange='adaptForm()' name="categorie" style="width:20em">
                            <?php option_liste_categorie(null);?>
                    </select>

                    <label id='label_exam_nom' for='exam_nom'><br>Intitulé examen<br>
                    <input id='exam_nom' type='text' name='examen' size='50'>
                    </label>

                    <label id='label_details' for='details'><br>Details<br>
                    <input id='details' type='text' name='details' size='50'>
                    </label>

                    <label id='label_lieux' for='lieux'><br>Lieux<br>
                    <input id='lieux' type='text' name='lieux' size='50'>
                    </label>

                    <label id='label_ordonateur' for='ordonateur'><br>Ordonateur<br>
                    <input id='ordonateur' type='text' name='ordonateur'  size='50'>
                    </label>
                </fieldset>
                <fieldset id="fs_pré-saisie"><legend>Gestion de l'absence</legend>

                    <span id='label_gest'>gestionnaire de la convocation / lieu de retrait<br></span>
                    <select id="gest" onchange='adaptForm()' name="gest" style="width:20em">
                            <?php option_liste_gest(null);?>
                    </select>
                    <br>

                    <input type="checkbox" id="pre-saisie" name="pre-saisie" value="true">
                    <span id='label_pre-saisie'>pré-saisie en attente de la convocation officielle</span><br>

                    <?php
                    if (isset($nb_conflits) && $nb_conflits >= 1)
                        echo "<img src=\"ico/bouton_supprimer.png\" onclick=\"window.open('abs_delete.php?abid=".$abid."','launch','menubar=no, status=no, scrollbars=no, width=400, height=200');
                        window.location.replace('absences.php');\"
                        onmouseover=\"surligne('fs_saisie','rouge');surligne('fs_conflits','vert');\"
                        onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\">";
                    ?>
                    <img src="ico/bouton_valider.png" onclick="document.dates.submit()"
                    onmouseover="surligne('fs_saisie','vert');surligne('fs_conflits','vert');"
                    onmouseout="surligne('fs_saisie','none');surligne('fs_conflits','none');">

                    <input type="hidden" name="action" value="<?php echo $action?>">
                    <input type="hidden" name="send" value="true">
                </fieldset>
            </fieldset>
        </form>
        </div>
        <script type="text/javascript" src="js/windows.js"></script>
        <script type="text/javascript" src="yui/js/yahoo-dom-event.js"></script>
        <script type="text/javascript" src="yui/js/dragdrop-min.js"></script>
        <script type="text/javascript" src="yui/js/slider-min.js"></script>
        <script type="text/javascript" src="yui/js/container_core-min.js"></script>
        <script type="text/javascript" src="yui/js/element-min.js"></script>
        <script type="text/javascript" src="yui/js/button-min.js"></script>
        <script type="text/javascript" src="js/slider.js"></script>
        <script type="text/javascript" src="js/form_select.js"></script>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <script type="text/javascript">
            function surligne(id,color)
            {
            if (color == "vert")
                {
                document.getElementById(id).style.borderColor='green';
                document.getElementById(id).style.backgroundColor='#33FF00';
                }
            else if (color == 'rouge')
                {
                document.getElementById(id).style.borderColor='red';
                document.getElementById(id).style.backgroundColor='#FF3333';
                }
            else if (color == 'bleu')
                {
                document.getElementById(id).style.borderColor='blue';
                document.getElementById(id).style.backgroundColor='#3333FF';
                }
            else
                {
                document.getElementById(id).style.borderColor='black';
                document.getElementById(id).style.backgroundColor='#DEDEDE';
                }
            }
            // les fonctions setValue et setSelected font appel à GetElementById et doivent être appelé après la éfinition de id dans le HTML
            function setInput() {
                // remplis le formulaire
                //nom
                setSelected('prid', '<?php echo $prid ?>');
                // heure
                setSelected('bselHour', '<?php echo $array_debut['hour'] ?>');
                setSelected('bselMin', '<?php echo $array_debut['minute'] ?>');
                setSelected('eselHour', '<?php echo $array_fin['hour'] ?>');
                setSelected('eselMin', '<?php echo $array_fin['minute'] ?>');

                // dates de debut et de fin
                $('#start_date').val('<?php echo $start_date; ?>');
                $('#end_date').val('<?php echo $end_date; ?>');

                // categorie
                setSelected('categorie', '<?php echo $categorie ?>');
                // lieux
                setValue('lieux', '<?php echo addslashes($lieu) ?>');
                // ordonateur
                setValue('ordonateur', '<?php echo addslashes($ordonateur) ?>');
                // examen
                setValue('exam_nom', '<?php echo addslashes($examen) ?>');
                // details
                setValue('details', '<?php echo addslashes($details) ?>');
                // pré saisie
                document.getElementById('pre-saisie').checked = <?php echo $presaisie == 1 ? "true" : "false" ?>;
                // gestionnaire
                setSelected('gest', '<?php echo $gest ?>');
            }

            function adaptForm () {
                if (document.getElementById("categorie").value == "Examen")
                    {
                    document.getElementById("label_exam_nom").style.visibility = "visible"
                    document.getElementById("label_details").style.visibility = "visible"
                    document.getElementById("label_lieux").style.visibility = "visible"
                    document.getElementById("label_ordonateur").style.visibility = "visible"

                }
                else if (document.getElementById("categorie").value == "Stage")
                    {
                    document.getElementById("label_exam_nom").style.visibility = "hidden"
                    document.getElementById("label_details").style.visibility = "visible"
                    document.getElementById("label_lieux").style.visibility = "visible"
                    document.getElementById("label_ordonateur").style.visibility = "visible"

                }
                else if (document.getElementById("categorie").value == "Maladie" ||
                        document.getElementById("categorie").value == "Sortie/voyage" ||
                        document.getElementById("categorie").value == "Enfant malade")
                    {
                    document.getElementById("label_exam_nom").style.visibility = "hidden"
                    document.getElementById("label_details").style.visibility = "visible"
                    document.getElementById("label_lieux").style.visibility = "hidden"
                    document.getElementById("label_ordonateur").style.visibility = "hidden"

                }
                else
                    {
                    document.getElementById("label_exam_nom").style.visibility = "hidden"
                    document.getElementById("label_details").style.visibility = "visible"
                    document.getElementById("label_lieux").style.visibility = "hidden"
                    document.getElementById("label_ordonateur").style.visibility = "hidden"

                }
            }

            $( "#exam_nom" ).autocomplete({
                <?php autocomplete_source("examen","absences");?>
                });
            $( "#details" ).autocomplete({
                <?php autocomplete_source("details","absences");?>
                });
            $( "#lieux" ).autocomplete({
                <?php autocomplete_source("nom_lieu","lieux");?>
                });
            $( "#ordonateur" ).autocomplete({
                <?php autocomplete_source("ordonateur","absences");?>
                });
            $( ".select_date" ).datepicker({
                monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                monthNamesShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
                    'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                prevText: '&#x3c;Préc', prevStatus: 'Voir le mois précédent',
                prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: 'Voir l\'année précédent',
                nextText: 'Suiv&#x3e;', nextStatus: 'Voir le mois suivant',
                nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: 'Voir l\'année suivant',
                currentText: 'Courant', currentStatus: 'Voir le mois courant',
                todayText: 'Aujourd\'hui', todayStatus: 'Voir aujourd\'hui',
                clearText: 'Effacer', clearStatus: 'Effacer la date sélectionnée',
                closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
                yearStatus: 'Voir une autre année', monthStatus: 'Voir un autre mois',
                weekText: 'Sm', weekStatus: 'Semaine de l\'année',
                dayStatus: '\'Choisir\' le DD d MM',
                defaultStatus: 'Choisir la date',
                showButtonPanel: true,
                showWeek: false,
                showOtherMonths: true,
                constrainInput: true,
                yearRange: "-0:+2"
                });
            setInput();
            adaptForm();
        </script>
    </body>
</html>
