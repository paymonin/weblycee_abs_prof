<?php
// Inialize session
session_start();
// Check, if user is already login, then jump to secured page
if (isset($_SESSION['username']))
    {
    header('Location: absences.php');
    }
require ('../config.inc.php');
?>

<html>
    <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
        <title>authentification gestion des absences</title>
        <link href="css/login.css" type="text/css" rel="stylesheet" />
    </head>
    <body>

        <div> Gestion des absences du personnel du <?php echo $cste_etab_name; ?> </div>

        <?php
        include('db_fonction.php');
        if (!$base_id = connecti_db()) echo "erreur de connexion à la base de donnée";

        if (isset($_POST['username']) && isset($_POST['password']))
            {
            $user = $_POST['username'];
            $passmd5 = $_POST['password'];
            $logquery = "SELECT * FROM users WHERE username='".$user."' AND password='".$passmd5."'";
            $logresult = mysqli_query($base_id,$logquery);
            if (!$logresult) {
                die('Requête invalide : ' . mysqli_error());
            }
            echo $logquery;
            //echo mysqli_num_rows($logresult);
            if (mysqli_num_rows($logresult) == 1)
                {
                session_start();
                echo $_SESSION['username'] = mysqli_result($logresult,0,"username");
                echo $_SESSION['userType'] = mysqli_result($logresult,0,"usertype");
                header('Location: absences.php');
                }
            echo "<br><br>UTILISATEUR OU MOT DE PASSE INCONNU <br><br>";
            }

        if (isset($_POST['logout']))
            {
                // On démarre la session
                session_start ();
                // On détruit les variables de notre session
                session_unset ();
                // On détruit notre session
                session_destroy ();
                // On redirige le visiteur vers la page d'accueil
                header ('location: index.php');
            }


        ?>
        <div id="login_box">
            <h3>veuillez saisir votre identifiant et mot de passe</h3>
            <img id="logo" src='../logo.png'>  <?php // imposer la taille du logo ?>
            <form id="input"  method="POST" onsubmit="encodePassword();" action="index.php">
                <div>
                    Utilisateur <input type="text" name="username" size="20">
                </div>
                <div>
                    Mot de passe <input id="mot_de_passe" type="hidden" name="password" size="20"><input id="password" type="password" name="password_clair" size="20">
                </div>
                <div>
                    <input type="submit" value="Login">
                </div>
            </form>
        </div>
        <script language="javascript" src="js/md5.js"></script>
        <script language="javascript">
          function encodePassword() {
            str = document.getElementById("password").value;
            document.getElementById("mot_de_passe").value = MD5(str);
           }
        </script>
    </body>
</html>

