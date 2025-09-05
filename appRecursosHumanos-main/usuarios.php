<?php
session_start();
if(isset($_SESSION['usuario'])== null){
    Header('Location: index.php');
}
include_once('./conf/conf.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
   <title>Usuarios</title>
   <style>
    .contenido{
        margin: 40px;
    }
   </style>
</head>
<body>
    <?php
include_once('nav.php');


$consulta= "SELECT * FROM usuario";
$ejecutar_consulta= mysqli_query($con, $consulta);
$i=1;


?>
<div class="contenido">
<br>
<a href="agregar-usuario.php" class="btn btn-success">Nuevo Usuario</a>
<br>

<table class="table" >
    <tr>
        <th>N°</th>
        <th>Usuario</th>
        <th>Correo</th>
        <th>Acción</th>
        <th></th>
        <th></th>
        <th>Admin Contraseña</th>
    </tr>
        <?php
        while($lista= mysqli_fetch_array($ejecutar_consulta)){
        ?>
        <tr>
        <td><?php echo $i++ ?></td>
        <td><?php echo $lista['usuario'] ?></td>
        <td><?php echo $lista['email'] ?></td>
        <td><a href="editar-usuario.php?id=<?php echo $lista['id'] ?>" class="btn btn-primary" value="Editar">Editar</a> 
        </td>  
        <td><form action="crud-usuarios.php" method="POST"> 
            <input type="hidden" name="bandera" value="3">
            <input type="hidden" name="id" value="<?php echo $lista['id'] ?>">
        <button  class="btn btn-danger" >Eliminar</button>
        </form>
        <td> 
            <td>
                <button class="btn btn-warning" value="Eliminar" title="Modificar contraseña">Contraseña</button>
           </td>
     </tr>
    <?php
      }
    ?>
   
</table>
</div>
<?php
mysqli_close($con);
?>
</body>
</html>