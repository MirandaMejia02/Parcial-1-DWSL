<?php
$server="localhost";
$user="root";
$pwd="";
$db="dbrrhh";

$con= new mysqli($server, $user,
$pwd, $db);
if($con)
{
    // echo "Conexion exitosa";
}else{
    echo "Ha ocurriod un error de conexión";
}
?>