<?php
// editar_usuario.php
session_start();
require_once 'conexion.php';

// 1. SEGURIDAD: Solo administradores
if (!isset($_SESSION['usuario_rol']) || ($_SESSION['usuario_rol'] !== 'admin' && $_SESSION['usuario_rol'] !== 'otro')) {
    header('Location: login.php');
    exit;
}

// 2. OBTENER ID DEL USUARIO
if (!isset($_GET['id'])) {
    header('Location: dashboard_usuarios.php');
    exit;
}

$id_usuario = (int) $_GET['id'];
$mensaje = '';

// 3. PROCESAR FORMULARIO (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_activo = isset($_POST['activo']) ? 1 : 0;
    $nueva_pass = trim($_POST['password'] ?? ''); 

    try {
        if (!empty($nueva_pass)) {
            $passwordHash = password_hash($nueva_pass, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET activo = ?, password = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("isi", $nuevo_activo, $passwordHash, $id_usuario);
        } else {
            $sql = "UPDATE usuarios SET activo = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ii", $nuevo_activo, $id_usuario);
        }

        if ($stmt->execute()) {
            header("Location: dashboard_usuarios.php?msg=actualizado");
            exit;
        }
        $stmt->close();
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}

// 4. CONSULTA DE DATOS ACTUALES
$stmt = $conexion->prepare("SELECT usuario, nombre, email, activo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario_data)
    die("Usuario no encontrado.");
?>
<?php include 'dashboard_head.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Acceso - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
    <link rel="stylesheet" href="style_editar_usuario.css">
</head>
<body>
    <div class="dashboard_editar_usuarios_container">

        <div class="dashboard_editar_usuarios_card">
            <div class="dashboard_editar_usuarios_card_header">
                <h2>Editar Acceso</h2>
            </div>

            <form method="POST">
                <div class="dashboard_editar_usuarios_form_group">
                    <label class="dashboard_editar_usuarios_form_label">Usuario / DNI</label>
                    <input type="text" class="dashboard_editar_usuarios_form_input dashboard_editar_usuarios_form_input_readonly" value="<?= htmlspecialchars($usuario_data['usuario']) ?>" readonly>
                </div>

                <div class="dashboard_editar_usuarios_form_group">
                    <label class="dashboard_editar_usuarios_form_label">Nombre Completo</label>
                    <input type="text" class="dashboard_editar_usuarios_form_input dashboard_editar_usuarios_form_input_readonly" value="<?= htmlspecialchars($usuario_data['nombre']) ?>" readonly>
                </div>

                <div class="dashboard_editar_usuarios_form_group">
                    <label class="dashboard_editar_usuarios_form_label">Nueva Contraseña</label>
                    <input type="password" name="password" class="dashboard_editar_usuarios_form_input" placeholder="Dejar vacío para no cambiar">
                    <small class="dashboard_editar_usuarios_form_help_text">Solo rellene si desea cambiar la clave actual.</small>
                </div>

                <div class="dashboard_editar_usuarios_form_group">
                    <label class="dashboard_editar_usuarios_form_label">Estado del Acceso</label>
                    <div class="dashboard_editar_usuarios_status_box">
                        <input type="checkbox" id="activo" name="activo" value="1" <?= $usuario_data['activo'] ? 'checked' : '' ?>>
                        <span id="label-estado"></span>
                    </div>
                </div>

                <button type="submit" class="dashboard_editar_usuarios_btn_guardar">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    <form method="POST" action="dashboard_usuarios.php" class="dashboard_universal_volver">
        <button type="submit" class="dashboard_universal_btn_volver">
            <i class="fas fa-arrow-left"></i> Volver 
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('activo');
            const label = document.getElementById('label-estado');

            function actualizarTexto() {
                if (checkbox.checked) {
                    label.textContent = 'ACTIVO (Puede entrar)';
                    label.className = 'dashboard_editar_usuarios_text_success';
                } else {
                    label.textContent = 'INACTIVO (Acceso bloqueado)';
                    label.className = 'dashboard_editar_usuarios_text_danger';
                }
            }

            actualizarTexto();
            checkbox.addEventListener('change', actualizarTexto);
        });
    </script>
</body>
</html>