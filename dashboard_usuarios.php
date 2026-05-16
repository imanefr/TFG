<?php
// DASHBOARD GESTIÓN USUARIOS
session_start(); 
include 'conexion.php'; 

// SEGURIDAD CRÍTICA
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['admin', 'profesor', 'otro']);if (!$is_admin) {
    echo "<div style='padding:20px; color:red; font-family:sans-serif;'>Acceso denegado. Solo administradores.</div>";
    exit;
}

$mensaje = ''; 
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : ''; 

// PROCESAR ELIMINACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_usuario = (int) $_POST['id']; 
    
    if ($id_usuario === 1) {
        $mensaje = "Error: El administrador principal es un usuario del sistema y no puede ser eliminado.";
    } else {
        $conexion->begin_transaction();
        try {
            // 1. DESVINCULAR AL PROFESOR
            $stmt_desvincular = $conexion->prepare("UPDATE profesores SET usuario_id = NULL WHERE usuario_id = ?");
            $stmt_desvincular->bind_param("i", $id_usuario);
            $stmt_desvincular->execute();
            $stmt_desvincular->close();

            // 2. ELIMINAR LOS ACCESOS
            $stmt_acc = $conexion->prepare("DELETE FROM usuarios_accesos WHERE usuario_id = ?");
            $stmt_acc->bind_param("i", $id_usuario);
            $stmt_acc->execute();
            $stmt_acc->close();

            // 3. ELIMINAR EL USUARIO
            $stmt_u = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt_u->bind_param("i", $id_usuario);
            $stmt_u->execute();

            if ($stmt_u->affected_rows > 0) {
                $conexion->commit();
                $mensaje = "Cuenta de usuario eliminada correctamente.";
            } else {
                $mensaje = "Usuario no encontrado.";
            }
            $stmt_u->close();
        } catch (Exception $e) {
            $conexion->rollback();
            $mensaje = "Error al procesar: " . $e->getMessage();
        }
    }
}

// CONSULTA DE USUARIOS
$sql_base = "SELECT u.*, r.nombre_rol FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id WHERE u.id != 1";

if (!empty($busqueda)) {
    $like = "%" . $busqueda . "%";
    $sql_final = $sql_base . " AND (u.usuario LIKE ? OR u.nombre LIKE ? OR u.email LIKE ?) ORDER BY u.fecha_registro DESC";
    $stmt = $conexion->prepare($sql_final);
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $sql_final = $sql_base . " ORDER BY u.fecha_registro DESC";
    $stmt = $conexion->prepare($sql_final);
}

$stmt->execute();
$resultado = $stmt->get_result();
$usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
        <?php include 'dashboard_head.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">

</head>
<body>
    <div class="dashboard_usuarios_container">


        <form method="GET" class="dashboard_usuarios_buscar">
            <div class="dashboard_usuarios_buscar_group">
                <input type="text" name="buscar" class="dashboard_usuarios_input_buscar" 
                       placeholder="Buscar por nombre, usuario o correo..." 
                       value="<?php echo htmlspecialchars($busqueda); ?>">
                
                <button type="submit" class="dashboard_usuarios_btn_buscar">
                    <i class="fas fa-search"></i>
                </button>

                <a href="usuarios_profesores_alta.php" class="dashboard_usuarios_btn_añadir">
                    <i class="fas fa-plus"></i> Nuevo Acceso
                </a>
                
                <a href="editar_personal.php" class="dashboard_usuarios_btn_añadir">
            <i class="fas fa-user-plus"></i> Nuevo Docente
        </a>

                <?php if ($busqueda): ?>
                    <a href="dashboard_usuarios.php" class="dashboard_usuarios_btn_limpiar">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($mensaje): ?>
            <div class="<?php echo (strpos($mensaje, 'Error') !== false) ? 'dashboard_usuarios_mensaje_error' : 'dashboard_usuarios_mensaje_exito'; ?>">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (count($usuarios) > 0): ?>
            <div class="dashboard_usuarios_tabla_container">
                <div class="dashboard_usuarios_tabla_info">
                    <span>Mostrando <?php echo count($usuarios); ?> usuarios</span>
                </div>

                <table class="dashboard_usuarios_tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th> 
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr class="dashboard_usuarios_fila">
                            <td><?php echo $u['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($u['usuario']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td class="badge_rol"><?php echo htmlspecialchars($u['nombre_rol'] ?? 'Sin Rol'); ?></td>
                            <td>
                                <span class="dashboard_usuarios_estado <?php echo $u['activo'] ? 'dashboard_usuarios_estado_activo' : 'dashboard_usuarios_estado_inactivo'; ?>">
                                    <?php echo $u['activo'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="dashboard_usuarios_celda_acciones">
                                <div class="dashboard_usuarios_acciones">
                                    <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="dashboard_usuarios_btn_editar"><i class="fas fa-edit"></i></a>
                                    
                                    <form method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar a <?php echo htmlspecialchars($u['nombre']); ?>?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="dashboard_usuarios_btn_eliminar"><i class="fas fa-trash"></i></button>
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
                <a href="dashboard_usuarios.php">Ver todos</a>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
        <button type="submit" class="dashboard_universal_btn_volver">
            <i class="fas fa-arrow-left"></i> Volver
        </button>
    </form>
</body>
</html>