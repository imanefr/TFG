<?php
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%; max-width: 400px;
        }
        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-logo { 
            width: 70px; height: 70px; background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; font-size: 1.8rem; color: white;
        }
        h1 { color: #333; font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; }
        .login-form { display: flex; flex-direction: column; gap: 1.5rem; }
        .form-group { position: relative; }
        .form-label { display: block; font-weight: 600; color: #555; margin-bottom: 0.5rem; }
        .form-input { 
            width: 100%; padding: 1rem 1rem 1rem 3rem; border: 2px solid #e1e5e9; 
            border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; background: #f8f9fa;
        }
        .form-input:focus { outline: none; border-color: #667eea; background: white; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
        .form-icon { position: absolute; left: 1rem; top: 2.8rem; color: #888; font-size: 1.1rem; }
        .btn-login { 
            background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; 
            padding: 1.2rem; border-radius: 10px; font-size: 1.1rem; font-weight: 600; 
            cursor: pointer; transition: all 0.3s ease;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(102,126,234,0.3); }
        .alert { 
            background: #fee; color: #c33; padding: 1rem; border-radius: 8px; 
            border-left: 4px solid #f66; margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><i class="fas fa-users-cog"></i></div>
            <h1>Gestión de Usuarios</h1>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-input" placeholder="Tu usuario" required autocomplete="username" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                <i class="fas fa-user form-icon"></i>
            </div>
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                <i class="fas fa-lock form-icon"></i>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Acceder
            </button>
        </form>
    </div>
</body>
</html>
