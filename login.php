<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$mensaje = '';
if ($_POST) {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND activo = 1");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['usuario_rol'] = $user['rol'];
        header('Location: dashboard.php');
        exit;
    } else {
        $mensaje = 'Credenciales incorrectas';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - IES La Arboleda</title>
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
                    <div class="login_inicio-alert"><?php echo htmlspecialchars($mensaje); ?></div>
                <?php endif; ?>

                <form method="POST" class="login_inicio-form">
                    <div class="login_inicio-group">
                        <label class="login_inicio-label">Usuario</label>
                        <input type="text" name="usuario" class="login_inicio-input" placeholder="Tu usuario" required autocomplete="username" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                        <i class="fas fa-user login_inicio-icon"></i>
                    </div>
                    <div class="login_inicio-group">
                        <label class="login_inicio-label">Contraseña</label>
                        <input type="password" name="password" class="login_inicio-input" placeholder="••••••••" required autocomplete="current-password">
                        <i class="fas fa-lock login_inicio-icon"></i>
                    </div>
                    <button type="submit" class="login_inicio-btn">
                        <i class="fas fa-sign-in-alt"></i> Acceder
                    </button>

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
