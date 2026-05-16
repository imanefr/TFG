<?php
session_start(); 
include 'conexion.php'; 

// Seguridad: solo permiten entrar usuarios autenticados
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Permisos: admin, profesor u otro
$is_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'profesor', 'otro']);
if (!$is_admin) {
    die("Acceso denegado. No tienes permisos suficientes.");
}

// Variables para mensajes y valores del formulario
$mensaje = '';
$tipo_alerta = '';

$val_nombre = '';
$val_rol = '';

// Función para validar DNI español
function validarDNI($dni) {
    $dni = strtoupper(trim($dni));
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
        return false;
    }
    $letra = substr($dni, -1);
    $numeros = substr($dni, 0, 8);
    $letrasValidas = "TRWAGMYFPDXBNJZSQVHLCKE";
    $letraCorrecta = $letrasValidas[$numeros % 23];
    return ($letra === $letraCorrecta);
}

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $val_nombre = trim($_POST['nombre']);   // Nombre del docente
    $dni = strtoupper(trim($_POST['dni']));  // DNI en mayúsculas
    $val_rol = trim($_POST['rol']);         // Rol o cargo
    $usuario_id = null;                     // Se deja null porque todavía no está vinculado a un usuario

    // Comprobar que no estén vacíos nombre y DNI
    if (!empty($val_nombre) && !empty($dni)) {
        // Validar formato del DNI
        if (!validarDNI($dni)) {
            $mensaje = "El DNI introducido no es válido.";
            $tipo_alerta = "error";
        } else {
            // Comprobar que el DNI no exista ya en la tabla profesores
            $checkDni = $conexion->prepare("SELECT id FROM profesores WHERE dni = ?");
            $checkDni->bind_param("s", $dni);
            $checkDni->execute();
            $result = $checkDni->get_result();

            if ($result->num_rows > 0) {
                $mensaje = "Error: Ya existe un profesor con el DNI $dni.";
                $tipo_alerta = "error";
            } else {
                // Insertar nuevo profesor en la base de datos
                $stmt = $conexion->prepare("INSERT INTO profesores (nombre, dni, rol, usuario_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $val_nombre, $dni, $val_rol, $usuario_id);

                if ($stmt->execute()) {
                    $mensaje = "Profesor registrado correctamente.";
                    $tipo_alerta = "success";
                    $val_nombre = '';
                    $val_rol = '';
                } else {
                    $mensaje = "Error al registrar: " . $conexion->error;
                    $tipo_alerta = "error";
                }

                $stmt->close();
            }

            $checkDni->close();
        }
    }
}
?>
    <?php include 'dashboard_head.php'; ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Alta de Docente - Dashboard</title>

        <!-- Estilos externos -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">   
    </head>
    <body>
        <div class="editar_personal_php">

            <div class="dashboard_usuarios_container">

                <div class="form-container">
                    <h2><i class="fas fa-chalkboard-teacher" style="color: #8b5cf6;"></i> Registro de Docente</h2>

                    <!-- Mensaje de éxito o error -->
                    <?php if ($mensaje): ?>
                        <div class="alerta alerta-<?php echo $tipo_alerta; ?>">
                            <i class="fas <?php echo ($tipo_alerta === 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i> 
                            <?php echo htmlspecialchars($mensaje); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario de alta -->
                    <form method="POST">
                        <div class="form-group">
                            <label>Nombre Completo *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Julián García" 
                                   value="<?php echo htmlspecialchars($val_nombre); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>DNI * (8 números y letra)</label>
                            <input type="text" name="dni" class="form-control" placeholder="12345678Z" maxlength="9" required>
                        </div>

                        <div class="form-group">
                            <label>Descripción del Rol / Cargo</label>
                            <input type="text" name="rol" class="form-control" placeholder="Ej: Tutor 2º ESO" 
                                   value="<?php echo htmlspecialchars($val_rol); ?>">
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-user-plus"></i> Registrar Docente
                        </button>
                    </form>
                </div>

                <!-- Botón volver -->
                <form method="POST" action="dashboard_usuarios.php" class="dashboard_universal_volver">
                    <button type="submit" class="dashboard_universal_btn_volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>