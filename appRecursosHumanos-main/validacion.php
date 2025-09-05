<?php
include_once('./conf/conf.php');

$correo = isset($_POST['email']) ? $_POST['email'] : "";
$pwd    = isset($_POST['pwd'])   ? $_POST['pwd']   : "";
$pwdFormat = md5($pwd);

$consulta  = "SELECT usuario, email FROM usuario WHERE email='$correo' AND pwd='$pwdFormat' LIMIT 1";
$ejecucion = mysqli_query($con, $consulta);

if (!$ejecucion) {
  header('Location: index.php?error=sql'); exit;
}

if (mysqli_num_rows($ejecucion) > 0) {
  $usuario = mysqli_fetch_assoc($ejecucion);
  session_start();
  $_SESSION['usuario'] = $usuario['usuario'];   
  $_SESSION['email']   = $usuario['email'];     
  header('Location: home.php'); exit;
} else {
  header('Location: index.php?error=error'); exit;
}
