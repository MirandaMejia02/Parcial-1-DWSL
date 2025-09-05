<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit;
}

include_once __DIR__ . '/conf/conf.php';

/** Helper de escape */
function e(null|string $v): string {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

/** GET saneado (sin romper tu lógica actual) */
$get = filter_input_array(INPUT_GET, [
  'error'             => FILTER_DEFAULT,
  'nombre'            => FILTER_DEFAULT,
  'telefono'          => FILTER_DEFAULT,
  'dui'               => FILTER_DEFAULT,
  'fecha_nacimiento'  => FILTER_DEFAULT,
  'departamento'      => FILTER_DEFAULT,
  'distrito'          => FILTER_DEFAULT,
  'colonia'           => FILTER_DEFAULT,
  'calle'             => FILTER_DEFAULT,
  'casa'              => FILTER_DEFAULT,
  'estado_civil'      => FILTER_DEFAULT,
], false) ?: [];

$err   = $get['error'] ?? '';
$nombre   = $get['nombre'] ?? '';
$telefono = $get['telefono'] ?? '';
$dui      = $get['dui'] ?? '';
$fnac     = $get['fecha_nacimiento'] ?? '';
$dep      = $get['departamento'] ?? '';
$dist     = $get['distrito'] ?? '';
$colonia  = $get['colonia'] ?? '';
$calle    = $get['calle'] ?? '';
$casa     = $get['casa'] ?? '';

$ecivil   = $get['estado_civil'] ?? 'Soltero';
$opciones_civil = ['Soltero','Casado','Divorciado','Viudo'];
if (!in_array($ecivil, $opciones_civil, true)) {
  $ecivil = 'Soltero';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Persona</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.contenido{margin:40px;max-width:900px}</style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<?php if ($err === 'dui'): ?>
<script>
  alert('El DUI ya está registrado.');
</script>
<?php endif; ?>

<div class="contenido">
  <h3>Registrar Persona</h3>

  <!-- Importante: enctype para poder leer $_FILES en crud-personas.php -->
  <form action="crud-personas.php" method="POST" enctype="multipart/form-data" class="p-3 border rounded bg-light" novalidate>
    <input type="hidden" name="bandera" value="1">

    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label" for="nombre">Nombre</label>
        <input id="nombre" name="nombre" class="form-control" required value="<?= e($nombre) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="telefono">Teléfono</label>
        <input id="telefono" name="telefono" class="form-control" inputmode="tel" value="<?= e($telefono) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="dui">DUI</label>
        <input id="dui" name="dui" class="form-control" required placeholder="00000000-0"
               pattern="^\d{8}-\d$" title="Formato esperado: 00000000-0" value="<?= e($dui) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="fnac">Fecha de Nacimiento</label>
        <input id="fnac" type="date" name="fecha_nacimiento" class="form-control" value="<?= e($fnac) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="ecivil">Estado Civil</label>
        <select id="ecivil" name="estado_civil" class="form-select">
          <?php foreach ($opciones_civil as $o): ?>
            <option value="<?= e($o) ?>" <?= $ecivil === $o ? 'selected' : '' ?>><?= e($o) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="departamento">Departamento</label>
        <input id="departamento" name="departamento" class="form-control" value="<?= e($dep) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label" for="distrito">Distrito</label>
        <input id="distrito" name="distrito" class="form-control" value="<?= e($dist) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="colonia">Colonia</label>
        <input id="colonia" name="colonia" class="form-control" value="<?= e($colonia) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="calle">Calle</label>
        <input id="calle" name="calle" class="form-control" value="<?= e($calle) ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="casa">Casa</label>
        <input id="casa" name="casa" class="form-control" value="<?= e($casa) ?>">
      </div>

      <div class="col-md-12">
        <label class="form-label" for="imagen">Fotografía</label>
        <input id="imagen" type="file" name="imagen" class="form-control" accept="image/*">
        <small class="text-muted">Solo se guardará el <b>nombre del archivo</b>, no la imagen real.</small>
      </div>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Guardar</button>
      <a class="btn btn-secondary" href="personal.php">Volver</a>
    </div>
  </form>
</div>
</body>
</html>
