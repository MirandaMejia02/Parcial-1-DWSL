<?php
declare(strict_types=1);

include_once __DIR__ . '/conf/conf.php';

// FIX: en desarrollo, muestra errores de MySQL (coméntalo en producción)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/** Sanitiza y devuelve solo el nombre de archivo (sin ruta, ascii-safe, corto) */
function nombreArchivoSeguro(string $filesField): string {
  if (!isset($_FILES[$filesField]) || empty($_FILES[$filesField]['name'])) return '';
  $base = basename((string)$_FILES[$filesField]['name']);
  $base = trim($base);
  $base = preg_replace('/[^A-Za-z0-9._-]/', '_', $base) ?? '';
  if ($base === '.' || $base === '..') $base = '';
  if (strlen($base) > 80) $base = substr($base, -80);
  return $base;
}

function go(string $url): void {
  header("Location: {$url}");
  exit;
}

/** Helpers */
function val_str(string $key, string $default = ''): string {
  $v = $_POST[$key] ?? $default;
  $v = trim((string)$v);
  $v = preg_replace('/\s+/', ' ', $v) ?? $v;
  return $v;
}
function val_int(string $key, int $default = 0): int {
  return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
}
function non_empty(?string $v): ?string {
  $t = trim((string)($v ?? ''));
  return ($t === '') ? null : $t;
}

// FIX: normalizador de DUI uniforme
function normalizar_dui(string $raw): string {
  $dui = strtoupper(trim($raw));
  // quitar espacios internos
  $dui = preg_replace('/\s+/', '', $dui) ?? '';
  // si viene 9 dígitos, formatear 8-1
  if (preg_match('/^\d{9}$/', $dui)) {
    $dui = substr($dui,0,8) . '-' . substr($dui,8,1);
  }
  return $dui;
}

// Solo aceptar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  go('personal.php');
}

$bandera = isset($_POST['bandera']) ? (int)$_POST['bandera'] : 0;
if (!in_array($bandera, [1,2,3], true)) {
  go('personal.php');
}

if ($bandera === 1) {
  // INSERT
  $nombre   = val_str('nombre');
  $telefono = val_str('telefono');
  // FIX: normaliza antes de todo
  $dui      = normalizar_dui(val_str('dui'));
  $fnac     = val_str('fecha_nacimiento');
  $dep      = val_str('departamento');
  $dist     = val_str('distrito');
  $colonia  = val_str('colonia');
  $calle    = val_str('calle');
  $casa     = val_str('casa');
  $ecivil   = val_str('estado_civil', 'Soltero');

  // FIX: DUI no puede ir vacío
  if ($dui === '') {
    $params = http_build_query([
      'error' => 'dui',
      'nombre' => $nombre, 'telefono' => $telefono, 'dui' => $dui,
      'fecha_nacimiento' => $fnac, 'departamento' => $dep, 'distrito' => $dist,
      'colonia' => $colonia, 'calle' => $calle, 'casa' => $casa, 'estado_civil' => $ecivil
    ]);
    go("agregar-persona.php?{$params}");
  }

  // Validar formato
  if (!preg_match('/^\d{8}-\d$/', $dui)) {
    $params = http_build_query([
      'error' => 'dui',
      'nombre' => $nombre, 'telefono' => $telefono, 'dui' => $dui,
      'fecha_nacimiento' => $fnac, 'departamento' => $dep, 'distrito' => $dist,
      'colonia' => $colonia, 'calle' => $calle, 'casa' => $casa, 'estado_civil' => $ecivil
    ]);
    go("agregar-persona.php?{$params}");
  }

  // Solo guardamos el NOMBRE del archivo (no se sube)
  $img = nombreArchivoSeguro('imagen');

  // DUI único
  $stmt = mysqli_prepare($con, "SELECT 1 FROM persona WHERE dui = ? LIMIT 1");
  mysqli_stmt_bind_param($stmt, "s", $dui);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) >= 1) {
    mysqli_stmt_close($stmt);
    $params = http_build_query([
      'error' => 'dui',
      'nombre' => $nombre, 'telefono' => $telefono, 'dui' => $dui,
      'fecha_nacimiento' => $fnac, 'departamento' => $dep, 'distrito' => $dist,
      'colonia' => $colonia, 'calle' => $calle, 'casa' => $casa, 'estado_civil' => $ecivil
    ]);
    go("agregar-persona.php?{$params}");
  }
  mysqli_stmt_close($stmt);

  // Insert (fecha puede ser NULL)
  $sql = "INSERT INTO persona
          (nombre, telefono, dui, fecha_nacimiento, departamento, distrito, colonia, calle, casa, estado_civil, imagen_ruta, fecha_registro)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"; // FIX: agrega fecha_registro aquí si tu tabla la tiene
  $stmt = mysqli_prepare($con, $sql);

  $fnacParam = non_empty($fnac);
  mysqli_stmt_bind_param(
    $stmt,
    "sssssssssss",
    $nombre, $telefono, $dui, $fnacParam, $dep, $dist, $colonia, $calle, $casa, $ecivil, $img
  );

  try {
    mysqli_stmt_execute($stmt);
  } catch (mysqli_sql_exception $e) {
    // FIX: si por carrera se duplica, MySQL tira 1062 (duplicate)
    if ($e->getCode() === 1062) {
      mysqli_stmt_close($stmt);
      $params = http_build_query([
        'error' => 'dui',
        'nombre' => $nombre, 'telefono' => $telefono, 'dui' => $dui,
        'fecha_nacimiento' => $fnac, 'departamento' => $dep, 'distrito' => $dist,
        'colonia' => $colonia, 'calle' => $calle, 'casa' => $casa, 'estado_civil' => $ecivil
      ]);
      go("agregar-persona.php?{$params}");
    }
    throw $e;
  }
  mysqli_stmt_close($stmt);

  go('personal.php');

} elseif ($bandera === 2) {
  // UPDATE
  $id       = val_int('id');
  $nombre   = val_str('nombre');
  $telefono = val_str('telefono');
  $dui      = normalizar_dui(val_str('dui')); // FIX: normaliza también en update
  $fnac     = val_str('fecha_nacimiento');
  $dep      = val_str('departamento');
  $dist     = val_str('distrito');
  $colonia  = val_str('colonia');
  $calle    = val_str('calle');
  $casa     = val_str('casa');
  $ecivil   = val_str('estado_civil', 'Soltero');

  if ($id <= 0) go('personal.php');

  if (!preg_match('/^\d{8}-\d$/', $dui)) {
    go("editar-persona.php?error=dui&id={$id}");
  }

  // Traer DUI e imagen actual
  $stmt = mysqli_prepare($con, "SELECT dui, imagen_ruta FROM persona WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $cur = mysqli_stmt_get_result($stmt);
  $row = $cur ? mysqli_fetch_assoc($cur) : null;
  mysqli_stmt_close($stmt);

  if (!$row) go('personal.php');

  // Si cambió DUI, validar duplicado
  if ($row['dui'] !== $dui) {
    $stmt = mysqli_prepare($con, "SELECT 1 FROM persona WHERE dui = ? AND id <> ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "si", $dui, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $duiDuplicado = mysqli_stmt_num_rows($stmt) >= 1;
    mysqli_stmt_close($stmt);

    if ($duiDuplicado) {
      go("editar-persona.php?error=dui&id={$id}");
    }
  }

  $prevImg = $row['imagen_ruta'] ?? '';
  $imgNew  = nombreArchivoSeguro('imagen');
  $img     = ($imgNew !== '') ? $imgNew : $prevImg;

  $sql = "UPDATE persona SET
            nombre = ?, telefono = ?, dui = ?, fecha_nacimiento = ?,
            departamento = ?, distrito = ?, colonia = ?, calle = ?, casa = ?,
            estado_civil = ?, imagen_ruta = ?
          WHERE id = ?";
  $stmt = mysqli_prepare($con, $sql);

  $fnacParam = non_empty($fnac);
  mysqli_stmt_bind_param(
    $stmt,
    "sssssssssssi",
    $nombre, $telefono, $dui, $fnacParam, $dep, $dist, $colonia, $calle, $casa, $ecivil, $img, $id
  );
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  go('personal.php');

} elseif ($bandera === 3) {
  // DELETE
  $id = val_int('id');
  if ($id <= 0) go('personal.php');

  $stmt = mysqli_prepare($con, "DELETE FROM persona WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  go('personal.php');
}

go('personal.php');
