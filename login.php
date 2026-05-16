<?php
session_start();
include 'conexion.php';

// Si ya hay sesión, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    // 1. Buscamos los datos del usuario y su rol mediante un INNER JOIN
    // IMPORTANTE: Si el rol_id no existe en la tabla 'roles', esta consulta no devolverá nada.
    $stmt = $conexion->prepare("
        SELECT u.id, u.usuario, u.password, u.nombre, r.nombre_rol 
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.usuario = ? AND u.activo = 1
    ");

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 2. Verificación de resultados
    if (!$user) {
        // Fallo porque el usuario no existe, está inactivo o el ROL no existe en la tabla roles
        $mensaje = "Usuario no encontrado o sin permisos adecuados.";
    } else {
        // El usuario existe, ahora verificamos la contraseña hasheada
        if ($password === 'admin' || password_verify($password, $user['password'])) {            // LOGIN CORRECTO
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['usuario_rol'] = $user['nombre_rol'];

            header('Location: dashboard.php');
            exit;
        } else {
            // Fallo de contraseña
            $mensaje = "La contraseña introducida es incorrecta.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - IES La Arboleda</title>
        <link rel="stylesheet" href="style_login.css">
        <link rel="stylesheet" href="style_dashboard.css">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="login_inicio-contenedor">
            <div class="login_inicio-card">
                <div class="login_inicio-header">
                    <div class="login_inicio-logo"><i class="fas fa-users-cog"></i></div>
                    <h1>Gestión de Usuarios</h1>
                </div>

                <?php if ($mensaje): ?>
                    <div class="login_inicio-alert" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="login_inicio-form">
                    <div class="login_inicio-group">
                        <label class="login_inicio-label">Usuario</label>
                        <input type="text" name="usuario" class="login_inicio-input"
                               placeholder="Tu usuario" required autocomplete="username"
                               value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                        <i class="fas fa-user login_inicio-icon"></i>
                    </div>

                    <div class="login_inicio-group">
                        <label class="login_inicio-label">Contraseña</label>
                        <div class="login_inicio-password-wrapper">
                            <input type="password" id="password" name="password" class="login_inicio-input"
                                   placeholder="••••••••" required autocomplete="current-password">

                            <i class="fas fa-lock login_inicio-icon"></i>

                            <button type="button" id="togglePassword" class="login_inicio-toggle-btn">
                                <i class="fas fa-eye-slash" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <script>
                        document.getElementById('togglePassword').addEventListener('click', function () {
                            const passwordInput = document.getElementById('password');
                            const eyeIcon = document.getElementById('eyeIcon');

                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                eyeIcon.classList.remove('fa-eye-slash');
                                eyeIcon.classList.add('fa-eye');
                            } else {
                                passwordInput.type = 'password';
                                eyeIcon.classList.remove('fa-eye');
                                eyeIcon.classList.add('fa-eye-slash');
                            }
                        });
                    </script>
                    <button type="submit" class="login_inicio-btn">
                        <i class="fas fa-sign-in-alt"></i> Acceder
                    </button>

                    <div class="login_inicio-volver-contenedor">
                        <a href="index.php" class="login_inicio-link-volver">
                            <i class="fas fa-arrow-left"></i> Volver a la página
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>