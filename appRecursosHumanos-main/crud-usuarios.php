<?php
include_once('./conf/conf.php');
    $id=isset($_POST['id']) ? $_POST['id']:"";
    $nombre=isset($_POST['usuario']) ? $_POST['usuario']:"";
    $usuario=isset($_POST['correo']) ? $_POST['correo']:"";
    $pwd=isset($_POST['pwd']) ? $_POST['pwd']:"";
    $passformat=MD5($pwd);
    $bandera=isset($_POST['bandera']) ? $_POST['bandera']:"";
if($bandera==1){
    $vericar_nombre="SELECT * from usuario WHERE usuario='$nombre'";
    $verificar_sql= mysqli_query($con, $vericar_nombre);
    if(mysqli_num_rows($verificar_sql)>=1){
          header('Location: agregar-usuario.php?error=1&usuario='.$nombre.'&correo='.$usuario);
    }else {
        $insertar= "INSERT INTO usuario (id, usuario, email,pwd) VALUES 
        (NULL, '$nombre', '$usuario','$passformat')";
        $ejecucion= mysqli_query($con, $insertar);
            if($ejecucion){
            header('Location: usuarios.php');
            }else{
            header('Location: agregar-usuario.php');
            }
    }
}elseif($bandera==2){
    $estado_nombre="SELECT usuario from usuario WHERE id=$id";

    $verificar_sql= mysqli_query($con, $estado_nombre);
    $verificador=mysqli_fetch_assoc($verificar_sql);
    $var_usuario=$verificador['usuario'];

    // $consulta_nombre= "SELECT usuario from usuario where usuario='$var_usuario'";
    // $ejecucion=mysqli_query($con,$consulta_nombre);
    if($var_usuario == $nombre){
    //sin cambio de usuario
    $update_usuario= "UPDATE usuario SET usuario= '$nombre', email='$usuario' where id=$id";
    $update_ejecucion= mysqli_query($con, $update_usuario);
            if($update_ejecucion){
             header('Location: usuarios.php');
            }
        }
        else{
    //cambio de usuario, verificar disponibilidad.
  $verificar_nuevo= "SELECT usuario FROM usuario where usuario = '$nombre'";
  $exec_verificacion= mysqli_query($con, $verificar_nuevo);
        if(mysqli_num_rows($exec_verificacion) >=1){
        header('Location: editar-usuario.php?error=1&id='.$id);
        }else {
            $update_usuario= "UPDATE usuario SET usuario= '$nombre', email='$usuario' where id=$id";
            $update_ejecucion= mysqli_query($con, $update_usuario);
            if($update_ejecucion){
             header('Location: usuarios.php');
            }
        }
        }

}elseif($bandera==3){
 $eliminar="DELETE from usuario where id= $id";
    $ejecutar_eliminar= mysqli_query($con, $eliminar);
    if($ejecutar_eliminar){
        header('Location: usuarios.php');
    }
}
?>