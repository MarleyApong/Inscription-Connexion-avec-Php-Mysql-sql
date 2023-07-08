<?php
session_start();
error_reporting(0);
   $database = new PDO('mysql:host=localhost; dbname=ama group', 'root', '');

   // On recupere le message depuis l'URL
   $message = $_GET['msg'];
   if (isset($_POST['inscr'])) {
      $nom = htmlspecialchars(htmlentities((strtolower($_POST['nom']))));
      $prenom = htmlspecialchars(htmlentities((strtolower($_POST['prenom']))));
      $email = htmlspecialchars(htmlentities((strtolower($_POST['email']))));
      $pass = htmlspecialchars(htmlentities((strtolower($_POST['pass']))));
      $confir_pass = htmlspecialchars(htmlentities((strtolower($_POST['confirm_pass']))));
      $pass_hash = password_hash("$pass",PASSWORD_BCRYPT);

      if ($pass === $confir_pass) {
         // Ici, on verifie s'il l'email n'existe pas deja dans la bd
         $check = $database-> prepare("SELECT email FROM users WHERE email = ?");
         $check->execute(array($email));
         $row = $check->rowCount();
         // Si "row" est egal a 0 alors il n'existe pas

         if($row == 0) {
            // Ici, on insere l'utilisateur dans la bd
            $req = $database->prepare("INSERT INTO users VALUES (null,?,?,?,?)");
            $req->execute(array($nom,$prenom,$email,$pass_hash));
            if($req) {
               $_SESSION['right'] = "yes";
               header('location: index.php?msg=ok');
            }
            else {
               header('location: index.php?msg=error_s');
            }
         }
         else {
            header('location: index.php?msg=user_ex');
         }
      }
      else {
         header('location: index.php?msg=error_p');
      }
   }

   if (isset($_POST['connect'])) {
      $nom = htmlspecialchars(htmlentities((strtolower($_POST['nom']))));
      $email = htmlspecialchars(htmlentities((strtolower($_POST['email']))));
      $pass = htmlspecialchars(htmlentities((strtolower($_POST['pass']))));
      // On selectionne le nom et l'email de la bd et on compare avec le formulaire

      $query = $database->prepare("SELECT * FROM users WHERE nom = ? AND email = ?");
      $query->execute(array($nom, $email));
      $data = $query->fetch();

      // Ici, on compare decrypte et on compare les mots de passe 
      if ($data && password_verify("$pass", $data['mot_passe'])) {
         $check = $database->prepare("SELECT email FROM users WHERE email = ?");
         $check->execute(array($email));

         // Si l'utilisateur existe alors il se connecte et on recupere son nom, prenom
         if ($check) {
            $_SESSION['right'] = "yes";
            $_SESSION['user'] = $data['nom'] . " " . $data['prenom'];
            header('location: home.php');
         }
         else {
            header('location: home.php?msg=error_c');
         }
      }
      else {
         header('location: home.php?msg=error_i');
      }
   }
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Connexion + Inscription PHP</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="style/style.css">
</head>
<body>
   <p class="reponse">
      <?php
         if($message === "error_p") {
            echo "Les mots de passe sont différents !";
         }
         else if ($message === "ok") {
            echo "Votre compte a été créé avec succès !";
         }
         else if ($message === "user_ex") {
            echo "Erreur: Cet email existe déja !";
         }
         else if($message === "error_s") {
            echo "erreur: Votre compte  n'a pas été créé !";
         }
         else if ($message === "error_c") {
            echo "Echec de l'authentification, réessayez !";
         }
         else if ($message === "error_i") {
            echo "Les identifiants entrés sont incorrects !";
         }
         else if ($message === "error_a") {
            echo "Accès refusé !";
         }
         else if ($message === "ss") {
            echo "Votre session a été fermée et sécurisée !";
         }
      ?>
   </p>
   <div class="container">
      <div class="left">
         <i class="fa-solid fa-users-viewfinder"></i>
      </div>

      <div class="right">
         <h2 id="title">Je crée mon compte</h2>
         <div class="content" id="content">

            <!-- Formulaire d'inscription -->
            <form method="POST" id="form-inscr">
               <input type="text" placeholder="Nom" name="nom" required autocomplete="off">
               <input type="text" placeholder="Prenom" name="prenom" required>
               <input type="text" placeholder="Email" name="email" required autocomplete="off">
               <input type="password" placeholder="Mot de passe" name="pass" required autocomplete="off">
               <input type="password" placeholder="confirmer mot de passe" name="confirm_pass" required autocomplete="off">
               <input type="submit" name="inscr" value="S'inscrire">
            </form>

            <!-- Formulaire de connexion -->
            <form method="POST" id="form-connect">
               <input type="text" placeholder="Nom" name="nom" required autocomplete="off">
               <input type="text" placeholder="Email" name="email" required autocomplete="off">
               <input type="password" placeholder="Mot de passe" name="pass" required autocomplete="off">
               <input type="submit" name="connect" value="Se connecter">
            </form>
         </div>
         <p>
            <span id="span">J'ai déja un compte </span>
            <button id="btn">Me connecter</button>
         </p>
      </div>
   </div>

   <script src="js/script.js"></script>
</body>
</html>