<?php
// DASHBOARD GESTIÓN USUARIOS
session_start(); // Iniciar sesión para autenticación global
include 'conexion.php'; // Conexión MySQLi preparada

// SEGURIDAD CRÍTICA: Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir si expiró
    exit; // Parar ejecución inmediatamente
}

// CONTROL ACCESO ADMIN: Doble verificación permisos
$is_admin = ($_SESSION['usuario_rol'] === 'admin');
if (!$is_admin) {
    // BLOQUEO INMEDIATO no-admin con mensaje claro
    echo "<div class='dashboard_usuarios_no_admin'>Acceso denegado. Solo administradores.</div>";
    exit;
}

// CONFIGURACIÓN DASHBOARD
$titulo_dashboard = "Dashboard Usuarios"; // Título para header global

// ESTADO FORMULARIOS
$mensaje = ''; // Mensajes éxito/error
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : ''; // Filtro búsqueda

// PROCESAR ELIMINACIÓN USUARIO - Acción POST segura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int) $_POST['id']; // Sanitizar ID entero
    
    // PREPARED STATEMENT DELETE - Protección SQL injection
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' = integer
    
    if ($stmt->execute()) {
        // Verificar filas afectadas (importante para UX)
        $mensaje = $stmt->affected_rows > 0 ? 
            "Usuario eliminado correctamente." : 
            "Usuario no encontrado o ya eliminado.";
    } else {
        $mensaje = "Error al eliminar el usuario: " . $stmt->error;
    }
    $stmt->close();
}

// CONSULTA USUARIOS - Búsqueda inteligente o todos
if (!empty($busqueda)) {
    // BÚSQUEDA MULTI-CAMPO - Usuario, nombre, email
    $like = "%" . $busqueda . "%";
    $stmt = $conexion->prepare("
        SELECT * FROM usuarios 
        WHERE usuario LIKE ? OR nombre LIKE ? OR email LIKE ? 
        ORDER BY fecha_registro DESC
    ");
    $stmt->bind_param("sss", $like, $like, $like); // 3 strings
} else {
    // TODOS LOS USUARIOS - Orden reciente primero
    $stmt = $conexion->prepare("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
}
$stmt->execute();
$resultado = $stmt->get_result();
$usuarios = $resultado->fetch_all(MYSQLI_ASSOC); // Array asociativo
$stmt->close();
?>

<!DOCTYPE html> <!-- HTML5 semántico -->
<html lang="es">
<head>
    <!-- META TÉCNICOS -->
    <meta charset="UTF-8"> <!-- Ñ + acentos -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Gestión de Usuarios - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>

<body>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="dashboard_usuarios_container">
        
        <!-- HEADER GLOBAL -->
        <?php include 'dashboard_head.php'; ?>

        <!-- BUSCADOR GLOBAL - Live search por 3 campos -->
        <form method="GET" class="dashboard_usuarios_buscar">
            <div class="dashboard_usuarios_buscar_group">
                <!-- INPUT BÚSQUEDA persistente -->
                <input type="text" name="buscar" class="dashboard_usuarios_input_buscar" 
                       placeholder="Buscar por nombre, usuario o correo..." 
                       value="<?php echo htmlspecialchars($busqueda); ?>">
                
                <!-- BOTÓN SEARCH -->
                <button type="submit" class="dashboard_usuarios_btn_buscar">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- LIMPIAR RESULTADOS (solo si hay búsqueda) -->
                <?php if ($busqueda): ?>
                    <a href="dashboard_usuarios.php" class="dashboard_usuarios_btn_limpiar">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- ALERTAS SISTEMA - Éxito/Error dinámicas -->
        <?php if ($mensaje): ?>
            <div class="dashboard_usuarios_mensaje 
                <?php echo strpos($mensaje, 'eliminado') !== false ? 
                    'dashboard_usuarios_mensaje_exito' : 
                    'dashboard_usuarios_mensaje_error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- TABLA USUARIOS - Responsive + sortable -->
        <?php if (count($usuarios) > 0): ?>
            <div class="dashboard_usuarios_tabla_container">
                <!-- INFO PAGINACIÓN -->
                <div class="dashboard_usuarios_tabla_info">
                    <span>
                        Mostrando <?php echo count($usuarios); ?> 
                        usuario<?php echo count($usuarios) !== 1 ? 's' : ''; ?>
                    </span>
                </div>

                <!-- TABLA PRINCIPAL 8 columnas -->
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
                                <!-- DATOS USUARIO - XSS protegidos -->
                                <td class="dashboard_usuarios_celda_id"><?php echo $u['id']; ?></td>
                                <td class="dashboard_usuarios_celda_usuario">
                                    <?php echo htmlspecialchars($u['usuario']); ?>
                                </td>
                                <td class="dashboard_usuarios_celda_nombre">
                                    <?php echo htmlspecialchars($u['nombre']); ?>
                                </td>
                                <td class="dashboard_usuarios_celda_email">
                                    <?php echo htmlspecialchars($u['email']); ?>
                                </td>
                                <td class="dashboard_usuarios_celda_rol">
                                    <?php echo htmlspecialchars(ucfirst($u['rol'])); ?> <!-- Admin, User... -->
                                </td>
                                
                                <!-- ESTADO VISUAL - Badge dinámico -->
                                <td class="dashboard_usuarios_celda_activo">
                                    <span class="dashboard_usuarios_estado 
                                        <?php echo $u['activo'] ? 
                                            'dashboard_usuarios_estado_activo' : 
                                            'dashboard_usuarios_estado_inactivo'; ?>">
                                        <?php echo $u['activo'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                                
                                <!-- FECHA FORMATEADA -->
                                <td class="dashboard_usuarios_celda_fecha">
                                    <?php echo date('d/m/Y H:i', strtotime($u['fecha_registro'])); ?>
                                </td>
                                
                                <!-- ACCIONES CRUD -->
                                <td class="dashboard_usuarios_celda_acciones">
                                    <div class="dashboard_usuarios_acciones">
                                        <!-- EDITAR - Enlace GET seguro -->
                                        <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" 
                                           class="dashboard_usuarios_btn_editar" 
                                           title="Editar usuario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- ELIMINAR - Form POST + confirm -->
                                        <form method="POST" class="dashboard_usuarios_form_eliminar" 
                                              onsubmit="return confirm('¿Seguro que quieres eliminar a <?php echo htmlspecialchars($u['nombre']); ?>?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="dashboard_usuarios_btn_eliminar" 
                                                    title="Eliminar usuario">
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
            <!-- ESTADO VACÍO - UX inteligente -->
            <div class="dashboard_usuarios_vacio">
                <i class="fas fa-users-slash"></i>
                <h3>No se encontraron usuarios</h3>
                <?php if ($busqueda): ?>
                    <!-- BÚSQUEDA SIN RESULTADOS -->
                    <p>Intenta con otra búsqueda o 
                        <a href="dashboard_usuarios.php">ver todos</a>
                    </p>
                <?php else: ?>
                    <!-- TABLA VACÍA -->
                    <p>No hay usuarios registrados en el sistema</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- BOTÓN VOLVER PRINCIPAL -->
    <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
        <button type="submit" class="dashboard_universal_btn_volver">
            <i class="fas fa-arrow-left"></i> Volver
        </button>
    </form>
</body>
</html>
