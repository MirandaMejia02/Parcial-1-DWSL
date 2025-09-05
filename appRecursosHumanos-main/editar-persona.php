<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit;
}

include_once __DIR__ . '/conf/conf.php';

/** Escape helper */
function e(null|string $v): string {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

/** GET */
$id  = (int) (filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? 0);
$err = (string) (filter_input(INPUT_GET, 'error', FILTER_DEFAULT) ?? '');

if ($id <= 0) {
  header('Location: personal.php');
  exit;
}

/** Traer persona con prepared statement */
$stmt = mysqli_prepare($con, "SELECT * FROM persona WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$p   = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$p) {
  header('Location: personal.php');
  exit;
}

// Alerta por DUI duplicado
if ($err === "dui") {
  echo "<script>alert('Ese DUI ya pertenece a otra persona.');</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Persona</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.contenido{margin:40px;max-width:900px}</style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="contenido">
  <h3>Editar Persona</h3>

  <!-- IMPORTANTE: enctype para poder leer $_FILES -->
  <form action="crud-personas.php" method="POST" enctype="multipart/form-data" class="p-3 border rounded bg-light" novalidate>
    <input type="hidden" name="bandera" value="2">
    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label" for="nombre">Nombre</label>
        <input id="nombre" name="nombre" class="form-control" required value="<?= e($p['nombre']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="telefono">Teléfono</label>
        <input id="telefono" name="telefono" class="form-control" inputmode="tel" value="<?= e($p['telefono']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="dui">DUI</label>
        <input id="dui" name="dui" class="form-control" required
               placeholder="00000000-0" pattern="^\d{8}-\d$" title="Formato esperado: 00000000-0"
               value="<?= e($p['dui']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="fnac">Fecha de Nacimiento</label>
        <input id="fnac" type="date" name="fecha_nacimiento" class="form-control" value="<?= e($p['fecha_nacimiento']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="ecivil">Estado Civil</label>
        <select id="ecivil" name="estado_civil" class="form-select">
          <?php foreach (['Soltero','Casado','Divorciado','Viudo'] as $o): ?>
            <option value="<?= e($o) ?>" <?= ($p['estado_civil'] === $o) ? 'selected' : '' ?>><?= e($o) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="departamento">Departamento</label>
        <input id="departamento" name="departamento" class="form-control" value="<?= e($p['departamento']) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label" for="distrito">Distrito</label>
        <input id="distrito" name="distrito" class="form-control" value="<?= e($p['distrito']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="colonia">Colonia</label>
        <input id="colonia" name="colonia" class="form-control" value="<?= e($p['colonia']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="calle">Calle</label>
        <input id="calle" name="calle" class="form-control" value="<?= e($p['calle']) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="casa">Casa</label>
        <input id="casa" name="casa" class="form-control" value="<?= e($p['casa']) ?>">
      </div>

      <div class="col-md-12">
        <label class="form-label" for="imagen">Fotografía</label>
        <input id="imagen" type="file" name="imagen" class="form-control" accept="image/*">
        <small class="text-muted d-block mt-1">
          Actualmente guardado: <b><?= e($p['imagen_ruta'] ?: 'Ninguno') ?></b><br>
          Solo se guardará el nombre del archivo, no la imagen real.
        </small>
      </div>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary">Guardar cambios</button>
      <a class="btn btn-secondary" href="personal.php">Volver</a>
    </div>
  </form>
</div>
</body>
</html>
