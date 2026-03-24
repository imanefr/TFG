<?php
session_start();
include 'conexion.php';

// Si ya hay sesión activa, redirigimos al panel (dashboard)
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Variable para almacenar el mensaje de error del login
$mensaje = '';

// Si se envió el formulario (POST)
if ($_POST) {
    // Limpiamos y recogemos los datos del formulario
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Preparamos la consulta para buscar el usuario activo
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND activo = 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verificamos que exista el usuario y que la contraseña coincida
    if ($user && password_verify($password, $user['password'])) {
        // Regeneramos el ID de sesión para evitar session fixation (seguridad extra)
        session_regenerate_id(true);

        // Guardamos en la sesión los datos del usuario
        $_SESSION['usuario_id']    = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['usuario_rol']    = $user['rol'];

        // Redirigimos al panel de control
        header('Location: dashboard.php');
        exit;
    } else {
        // Si usuario/contraseña no coinciden o está inactivo, mostramos error
        $mensaje = 'Credenciales incorrectas';
    }

    // Cerramos el statement
    $stmt->close();

    // Cerramos la conexión a la base de datos (buena práctica)
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IES La Arboleda</title>

    <!-- Hoja de estilos del dashboard/login (tu propio CSS) -->
    <link rel="stylesheet" href="style_dashboard.css">

    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Contenedor principal del login -->
    <div class="login_inicio-contenedor">
        <div class="login_inicio-card">
            <!-- Cabecera del login: icono + título -->
            <div class="login_inicio-header">
                <div class="login_inicio-logo"><i class="fas fa-users-cog"></i></div>
                <h1>Gestión de Usuarios</h1>
            </div>

            <!-- Si hay mensaje de error, lo mostramos aquí -->
            <?php if ($mensaje): ?>
                <div class="login_inicio-alert"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form method="POST" class="login_inicio-form">
                <!-- Grupo de usuario -->
                <div class="login_inicio-group">
                    <label class="login_inicio-label">Usuario</label>
                    <input type="text" name="usuario" class="login_inicio-input"
                           placeholder="Tu usuario" required autocomplete="username"
                           value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                    <i class="fas fa-user login_inicio-icon"></i>
                </div>

                <!-- Grupo de contraseña -->
                <div class="login_inicio-group">
                    <label class="login_inicio-label">Contraseña</label>
                    <input type="password" name="password" class="login_inicio-input"
                           placeholder="••••••••" required autocomplete="current-password">
                    <i class="fas fa-lock login_inicio-icon"></i>
                </div>

                <!-- Botón de acceso -->
                <button type="submit" class="login_inicio-btn">
                    <i class="fas fa-sign-in-alt"></i> Acceder
                </button>

                <!-- Enlace de "volver a la página principal" -->
                <div class="login_inicio-volver-contenedor">
                    <a href="index.php" class="login_inicio-link-volver">
                        <i class="fas fa-arrow-left"></i>
                        Volver a la página
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
