<?php
session_start();
include 'conexion.php';

// Verificar sesión y rol admin
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$is_admin = ($_SESSION['usuario_rol'] === 'admin');
if (!$is_admin) {
    echo "<div class='dashboard_usuarios_no_admin'>Acceso denegado. Solo administradores.</div>";
    exit;
}

$titulo_dashboard = "Dashboard Usuarios";

$mensaje = '';
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int) $_POST['id'];
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = "Usuario eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar el usuario.";
    }
    $stmt->close();
}

// Consulta de usuarios
if (!empty($busqueda)) {
    $like = "%" . $busqueda . "%";
    $stmt = $conexion->prepare("SELECT * FROM usuarios 
        WHERE usuario LIKE ? OR nombre LIKE ? OR email LIKE ? 
        ORDER BY fecha_registro DESC");
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $stmt = $conexion->prepare("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
}
$stmt->execute();
$resultado = $stmt->get_result();
$usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Usuarios - Dashboard Admin</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">
    </head>
    <body>
        <div class="dashboard_usuarios_container">
            <!-- HEADER -->
            <?php include 'dashboard_head.php'; ?>


            <!-- BUSCADOR -->
            <form method="GET" class="dashboard_usuarios_buscar">
                <div class="dashboard_usuarios_buscar_group">
                    <input type="text" name="buscar" class="dashboard_usuarios_input_buscar" 
                           placeholder="Buscar por nombre, usuario o correo..." 
                           value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="dashboard_usuarios_btn_buscar">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if ($busqueda): ?>
                        <a href="dashboard_usuarios.php" class="dashboard_usuarios_btn_limpiar">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- MENSAJE -->
            <?php if ($mensaje): ?>
                <div class="dashboard_usuarios_mensaje <?php echo strpos($mensaje, 'eliminado') !== false ? 'dashboard_usuarios_mensaje_exito' : 'dashboard_usuarios_mensaje_error'; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- TABLA USUARIOS -->
            <?php if (count($usuarios) > 0): ?>
                <div class="dashboard_usuarios_tabla_container">
                    <div class="dashboard_usuarios_tabla_info">
                        <span>Mostrando <?php echo count($usuarios); ?> usuario<?php echo count($usuarios) !== 1 ? 's' : ''; ?></span>
                    </div>
                    <table class="dashboard_usuarios_tabla">
                        <thead>
                            <tr>
                                <th class="dashboard_usuarios_th_id">ID</th>
                                <th class="dashboard_usuarios_th_usuario">Usuario</th>
                                <th class="dashboard_usuarios_th_nombre">Nombre</th>
                                <th class="dashboard_usuarios_th_email">Email</th>
                                <th class="dashboard_usuarios_th_rol">Rol</th>
                                <th class="dashboard_usuarios_th_activo">Activo</th>
                                <th class="dashboard_usuarios_th_fecha">Fecha Registro</th>
                                <th class="dashboard_usuarios_th_acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="dashboard_usuarios_fila">
                                    <td class="dashboard_usuarios_celda_id"><?php echo $u['id']; ?></td>
                                    <td class="dashboard_usuarios_celda_usuario"><?php echo htmlspecialchars($u['usuario']); ?></td>
                                    <td class="dashboard_usuarios_celda_nombre"><?php echo htmlspecialchars($u['nombre']); ?></td>
                                    <td class="dashboard_usuarios_celda_email"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td class="dashboard_usuarios_celda_rol"><?php echo htmlspecialchars(ucfirst($u['rol'])); ?></td>
                                    <td class="dashboard_usuarios_celda_activo">
                                        <span class="dashboard_usuarios_estado <?php echo $u['activo'] ? 'dashboard_usuarios_estado_activo' : 'dashboard_usuarios_estado_inactivo'; ?>">
                                            <?php echo $u['activo'] ? 'Sí' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td class="dashboard_usuarios_celda_fecha"><?php echo date('d/m/Y H:i', strtotime($u['fecha_registro'])); ?></td>
                                    <td class="dashboard_usuarios_celda_acciones">
                                        <div class="dashboard_usuarios_acciones">
                                            <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" 
                                               class="dashboard_usuarios_btn_editar" title="Editar usuario">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" class="dashboard_usuarios_form_eliminar" 
                                                  onsubmit="return confirm('¿Seguro que quieres eliminar a <?php echo htmlspecialchars($u['nombre']); ?>?')">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                <button type="submit" class="dashboard_usuarios_btn_eliminar" title="Eliminar usuario">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="dashboard_usuarios_vacio">
                    <i class="fas fa-users-slash"></i>
                    <h3>No se encontraron usuarios</h3>
                    <?php if ($busqueda): ?>
                        <p>Intenta con otra búsqueda o <a href="dashboard_usuarios.php">ver todos</a></p>
                    <?php else: ?>
                        <p>No hay usuarios registrados en el sistema</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"> </i>  Volver
            </button>
        </form>
    </body>
</html>
