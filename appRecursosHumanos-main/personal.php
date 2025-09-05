<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit;
}

include_once __DIR__ . '/conf/conf.php';

/** Escape helper */
function e(null|string $v): string {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Personal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.contenido{margin:40px}</style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="contenido">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Listado de Personal</h3>
    <a href="agregar-persona.php" class="btn btn-success">Nueva Persona</a>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Avatar</th>
          <th>Nombre</th>
          <th>DUI</th>
          <th>Teléfono</th>
          <th>Fecha Registro</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $i = 1;
        $sql = "SELECT id, nombre, dui, telefono, fecha_registro FROM persona ORDER BY id DESC";
        $rs = mysqli_query($con, $sql);

        // Avatar SVG embebido
        $avatar = 'data:image/svg+xml;utf8,' . urlencode(
          '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50">
             <circle cx="25" cy="25" r="25" fill="#e5e7eb"/>
             <circle cx="25" cy="18" r="10" fill="#9ca3af"/>
             <rect x="10" y="30" width="30" height="15" rx="8" fill="#9ca3af"/>
           </svg>'
        );

        if ($rs && mysqli_num_rows($rs) > 0):
          while ($row = mysqli_fetch_assoc($rs)):
            $id = (int)$row['id']; // cast duro para evitar XSS en atributos/URLs
      ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><img src="<?= $avatar ?>" width="50" height="50" alt="avatar"></td>
          <td><?= e($row['nombre']) ?></td>
          <td><?= e($row['dui']) ?></td>
          <td><?= e($row['telefono']) ?></td>
          <td><?= e($row['fecha_registro']) ?></td>
          <td class="d-flex gap-2">
            <a class="btn btn-primary btn-sm" href="editar-persona.php?id=<?= $id ?>">Editar</a>
            <form action="crud-personas.php" method="POST" onsubmit="return confirm('¿Eliminar registro?');">
              <input type="hidden" name="bandera" value="3">
              <input type="hidden" name="id" value="<?= $id ?>">
              <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php
          endwhile;
        else:
      ?>
        <tr>
          <td colspan="7" class="text-center text-muted">No hay registros aún.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
