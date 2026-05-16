<?php
// Iniciar sesión PHP para manejar autenticación y mensajes
session_start();

// RECUPERAR MENSAJE FLASH DE SESIÓN 
// Ver si existe mensaje guardado en sesión del POST anterior
if (isset($_SESSION['mensaje'])) {
    // Asignar mensaje de sesión a variable local
    $mensaje = $_SESSION['mensaje'];
    // Borrar mensaje de sesión para que no se repita
    unset($_SESSION['mensaje']);
} else {
    // Si no hay mensaje en sesión, crear variable vacía
    $mensaje = '';
}

// Cargar archivo con conexión MySQLi segura
include 'conexion.php';

// Comprobar si usuario está logueado (existe ID en sesión)
if (!isset($_SESSION['usuario_id'])) {
    // Si NO está logueado, mandar a página login
    header('Location: login.php');
    // Parar ejecución del script (IMPORTANTE seguridad)
    exit;
}

// Título que aparecerá en header (usado en dashboard_head.php)
$titulo_dashboard = "Dashboard Contacto";

// Comprobar si usuario es admin (solo admins editan)
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');// PROCESAR ACCIONES


// Preparar consulta SQL segura para 1 registro (id=1)
$stmt = $conexion->prepare("SELECT * FROM contacto_secretaria WHERE id = 1");
// Ejecutar consulta preparada (protegida contra SQL injection)
$stmt->execute();
// Obtener resultado de la consulta
$resultado = $stmt->get_result();
// Cargar 1 fila como array (nombre_campo => valor)
$contacto_data = $resultado->fetch_assoc();
// Liberar memoria del statement
$stmt->close();

// PROCESAR FORMULARIO
// Si llega POST Y tiene campo 'accion'=editar
if ($_POST && isset($_POST['accion'])) {
    
    // Quitar espacios al inicio/final de cada campo
    $telefono = trim($_POST['telefono']);
    $fax = trim($_POST['fax']);
    $horario = trim($_POST['horario']);
    $correo = trim($_POST['correo']);
    $aviso = trim($_POST['aviso']);

    // ACTUALIZAR BASE DE DATOS
    // Preparar consulta UPDATE segura
    $stmt = $conexion->prepare("UPDATE contacto_secretaria SET telefono=?, fax=?, horario=?, correo=?, aviso=? WHERE id=1");
    // Vincular 5 campos del formulario (todos texto)
    $stmt->bind_param("sssss", $telefono, $fax, $horario, $correo, $aviso);
    
    // Ejecutar UPDATE en base de datos
    if ($stmt->execute()) {
        // Si UPDATE OK, guardar mensaje en sesión
        $_SESSION['mensaje'] = 'Datos de contacto actualizados correctamente';
        // Redirigir a esta misma página (refrescar)
        header('Location: dashboard_contacto.php');
        // Parar script (IMPORTANTE para seguridad)
        exit;
    }
    // Si UPDATE falla, cerrar statement
    $stmt->close();
}
?>
 <!-- Header con datos usuario -->
        <?php include 'dashboard_head.php'; ?>
<!DOCTYPE html>
<!-- Declara documento HTML5 -->

<html lang="es">
<!-- Idioma español para lectores de pantalla -->

<head>

    <meta charset="UTF-8">
    <!-- Responsive en móviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título en pestaña navegador -->
    <title>Dashboard Contacto</title>
    <!-- Iconos FontAwesome desde CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>

<body>
    <div class="dashboard_contacto_container">
        
       

        <!--  CONTROL DE PERMISOS - SOLO ADMIN  -->
        <?php if (!$is_admin): ?>
            <!-- Si NO es admin, mostrar mensaje bloqueo -->
            <div class="dashboard_contacto_no_admin">
                <!-- Icono candado grande -->
                <i class="fas fa-lock dashboard_contacto_no_admin_icono"></i>
                <!-- Mensaje de acceso denegado -->
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>

            <!--  MOSTRAR MENSAJE DE ÉXITO (verde) -->
            <?php if ($mensaje): ?>
                <!-- Solo muestra si $mensaje no está vacío -->
                <div class="dashboard_contacto_alert dashboard_contacto_alert_success">
                    <!-- Mensaje seguro (protegido XSS) -->
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO PRINCIPAL - EDITAR CONTACTO -->
            <!-- Sección del formulario -->
            <div class="dashboard_contacto_seccion_form">
                <!-- Título con icono editar -->
                <h2><i class="fas fa-edit"></i> Editar Datos de Contacto</h2>
                
                <!-- Formulario POST con clases CSS -->
                <form method="POST" class="dashboard_contacto_form_grid">
                    
                    <!-- Campo oculto que activa procesamiento -->
                    <input type="hidden" name="accion" value="editar">

                    <!-- CAMPOS DEL FORMULARIO -->
                    <!-- Campo teléfono -->
                    <div class="dashboard_contacto_form_group">
                        <label class="dashboard_contacto_form_label">Teléfono</label>
                        <!-- Precargado con datos actuales de BD -->
                        <input type="text" name="telefono" class="dashboard_contacto_form_input" required 
                               value="<?php echo htmlspecialchars($contacto_data['telefono'] ?? ''); ?>">
                    </div>

                    <!-- Campo fax -->
                    <div class="dashboard_contacto_form_group">
                        <label class="dashboard_contacto_form_label">Fax</label>
                        <input type="text" name="fax" class="dashboard_contacto_form_input" required 
                               value="<?php echo htmlspecialchars($contacto_data['fax'] ?? ''); ?>">
                    </div>

                    <!-- Campo horario -->
                    <div class="dashboard_contacto_form_group">
                        <label class="dashboard_contacto_form_label">Horario</label>
                        <input type="text" name="horario" class="dashboard_contacto_form_input" required 
                               value="<?php echo htmlspecialchars($contacto_data['horario'] ?? ''); ?>">
                    </div>

                    <!-- Campo email con validación HTML5 -->
                    <div class="dashboard_contacto_form_group">
                        <label class="dashboard_contacto_form_label">Correo Electrónico</label>
                        <input type="email" name="correo" class="dashboard_contacto_form_input" required 
                               value="<?php echo htmlspecialchars($contacto_data['correo'] ?? ''); ?>">
                    </div>

                    <!-- Textarea aviso (ocupa toda la anchura) -->
                    <div class="dashboard_contacto_form_group dashboard_contacto_form_group_wide">
                        <label class="dashboard_contacto_form_label">Aviso Importante</label>
                        <textarea name="aviso" class="dashboard_contacto_form_textarea" required rows="3">
                            <?php echo htmlspecialchars($contacto_data['aviso'] ?? ''); ?>
                        </textarea>
                    </div>

                    <!-- BOTÓN GUARDAR -->
                    <!-- Grupo de botones -->
                    <div class="dashboard_contacto_btn_group">
                        <!-- Botón submit principal -->
                        <button type="submit" class="dashboard_contacto_btn dashboard_contacto_btn_primary">
                            <i class="fas fa-save"></i> Actualizar Contacto
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; // Fin if admin ?>

        <!-- BOTÓN VOLVER -->
        <!-- Formulario para volver a dashboard anterior -->
        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver 
            </button>
        </form>
    </div> <!-- Fin contenedor principal -->
</body>
</html>
