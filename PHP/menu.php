<?php
require_once("./checkauth.php");
    function affiche_menu()
    {
        $tab_menu_lien = array( "absences", "personnel", "lieux", "textes", "planning","conflits","users");
        $tab_menu_texte = array( "Absences", "Personnel", "Lieux", "Textes", "Planning","Conflits","gestion utilisateurs");
        $tab_menu_niveau = array( 3,1,1,1,3,2,1 );

        //$info = pathinfo($_SERVER['PHP_SELF']);

        $menu = "<div align=\"RIGHT\">Utilisateur: <B>".$_SESSION['username']."</B> niveau : <B>". $_SESSION['userType']."</B></div>";
        $menu .= "<div align=\"RIGHT\"><FORM METHOD=POST ACTION=\"index.php\"><input type=\"hidden\" name=\"logout\" value=\"true\"><INPUT type=\"submit\" value=\"Déconnexion\"></FORM></div>";
        $menu .= "\n<div id=\"menu\">\n    <ul id=\"onglets\">\n";

        foreach($tab_menu_lien as $cle=>$base_id)
        {

            if ($_SESSION['userType'] <= $tab_menu_niveau[$cle])
            {
                $menu .= "    <li";
                //if (strpos($info['basename']) !== false)
                    $menu .= " class=\"active\"";
                $menu .= "><a href=\"" . $base_id . ".php\">" . $tab_menu_texte[$cle] .  "</a></li>\n";
            }
        }

        $menu .= "</ul>\n</div>";

        return $menu;
    }
?>

