<?php
   session_start();
   session_destroy();
   error_reporting(0);
   if ($_SESSION['right'] != "yes") {
      header('location: index.php?msg=error_a');
   }

?>

<h1>Welcome <?= $_SESSION['user']?></h1>

<a href="index.php?msg=ss">Se déconnecter</a>