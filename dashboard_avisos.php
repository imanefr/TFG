<?php
// Dashboard completo para gestión de avisos del IES La Arboleda
// Iniciar sesión PHP para manejar autenticación del usuario
session_start();

// Importar archivo de conexión a base de datos MySQLi preparada
require_once 'conexion.php';

// Verificar que el usuario esté autenticado en la sesión
// Si no existe usuario_id en sesión, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit; // Termina la ejecución del script inmediatamente
}

// Título dinámico para el header global del dashboard
$titulo_dashboard = "Dashboard Avisos";

// Determinar si el usuario actual tiene rol de administrador
// Solo los admins pueden crear/editar/eliminar avisos
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// Variable que almacenará mensajes de éxito/error tras operaciones
$mensaje = '';

// PROCESADOR CENTRAL DE ACCIONES - Maneja CRUD completo (solo administradores)
// Verifica: admin + método POST + acción específica
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    // Switch principal que ejecuta acción según valor de $_POST['accion']
    switch ($_POST['accion']) {

        // Acción: Eliminar aviso específico por ID
        case 'eliminar':
            // Convertir ID a entero para evitar inyecciones SQL
            $id = (int) $_POST['id'];

            // Consulta preparada para eliminar aviso de forma segura
            $stmt = $conexion->prepare("DELETE FROM avisos WHERE id = ?");
            $stmt->bind_param("i", $id); // 'i' = integer parameter
            // Verificar que se eliminó al menos 1 fila
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $mensaje = 'Aviso eliminado correctamente';
            }
            $stmt->close(); // Liberar recursos del statement
            break;

        // Acción: Crear nuevo aviso completo
        case 'nueva':
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $enlace = trim($_POST['enlace'] ?? '');
            $importante = isset($_POST['importante']) ? 1 : 0;

            if ($titulo && $texto) {
                $stmt = $conexion->prepare("
            INSERT INTO avisos (titulo, texto, enlace, fecha, importante, ultima_edicion_fecha, ultima_edicion_usuario_id) 
            VALUES (?, ?, ?, NOW(), ?, NOW(), ?)
        ");

                $stmt->bind_param("sssii", $titulo, $texto, $enlace, $importante, $_SESSION['usuario_id']);

                if ($stmt->execute()) {
                    $mensaje = 'Aviso añadido correctamente';
                }
                $stmt->close();
            } else {
                $mensaje = 'Título y texto son obligatorios';
            }
            break;

        // Acción: Editar aviso existente
        case 'editar':
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $enlace = trim($_POST['enlace'] ?? '');
            $importante = isset($_POST['importante']) ? 1 : 0;

            $stmt = $conexion->prepare("
        UPDATE avisos 
        SET titulo=?, texto=?, enlace=?, fecha=NOW(), importante=?, 
            ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? 
        WHERE id=?
    ");
            $stmt->bind_param("sssiii", $titulo, $texto, $enlace, $importante, $_SESSION['usuario_id'], $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $mensaje = 'Aviso actualizado correctamente';
            }
            $stmt->close();
            break;
    }
}

// CARGAR LISTADO COMPLETO DE AVISOS PARA MOSTRAR
// Consulta con JOIN para mostrar nombre del último editor
// Orden: importantes primero, luego por fecha descendente
$stmt = $conexion->prepare("
    SELECT a.*, u.nombre as ultima_edicion_usuario_nombre
    FROM avisos a 
    LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id
    ORDER BY a.importante DESC, a.fecha DESC
");
$stmt->execute();
$avisos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Array asociativo completo
$stmt->close();

// DETECTAR MODO EDICIÓN - Carga datos de aviso específico para editar
// Solo accesible para administradores
$modo_edit = false; // Flag que indica si estamos editando
$aviso_edit = null; // Datos del aviso que se edita

if ($is_admin && isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar']; // Sanitizar ID de URL

    $stmt = $conexion->prepare("
        SELECT a.*, u.nombre as ultima_edicion_usuario_nombre
        FROM avisos a 
        LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cargar datos del aviso y activar modo edición si existe
    if ($aviso_edit = $result->fetch_assoc()) {
        $modo_edit = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <!-- Configuración básica del documento HTML5 -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión Avisos - Dashboard Admin</title>

        <!-- Recursos externos: iconos y estilos CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">
    </head>
    <body>
        <!-- Contenedor principal de todo el dashboard -->
        <div class="dashboard_avisos_container">

            <?php include 'dashboard_head.php'; // Header reutilizable con datos usuario     ?>

            <!-- RESTRICCION DE ACCESO - Solo administradores pueden gestionar avisos -->
            <?php if (!$is_admin): ?>
                <div class="dashboard_avisos_no_admin">
                    <i class="fas fa-lock dashboard_avisos_no_admin_icono"></i>
                    <h2>Solo administradores pueden gestionar los avisos</h2>
                </div>
            <?php else: ?>

                <!-- MOSTRAR MENSAJE DE ÉXITO/ERROR si existe -->
                <?php if ($mensaje): ?>
                    <!-- Detecta tipo de mensaje por contenido para aplicar clase CSS correcta -->
                    <div class="dashboard_avisos_alert <?php
                    echo strpos($mensaje, 'eliminado') !== false ||
                    strpos($mensaje, 'añadido') !== false ||
                    strpos($mensaje, 'actualizado') !== false ? 'dashboard_avisos_alert_success' : 'dashboard_avisos_alert_error';
                    ?>">
                             <?php echo htmlspecialchars($mensaje); // Escapa HTML por seguridad ?>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO PRINCIPAL - Nueva entrada o Editar existente -->
                <div class="dashboard_avisos_seccion_form <?php echo $modo_edit ? 'dashboard_avisos_modo_edit' : ''; ?>">
                    <h2>
                        <?php if ($modo_edit): ?>
                            <!-- Modo edición: muestra ID del aviso -->
                            <i class="fas fa-edit"></i> Editar Aviso (ID: <?php echo $aviso_edit['id']; ?>)
                        <?php else: ?>
                            <!-- Modo creación: nuevo aviso -->
                            <i class="fas fa-plus"></i> Nuevo Aviso
                        <?php endif; ?>
                    </h2>

                    <!-- Formulario principal con validación HTML5 -->
                    <form method="POST" class="dashboard_avisos_form_grid">

                        <!-- Campos ocultos que determinan la acción a ejecutar -->
                        <?php if ($modo_edit): ?>
                            <!-- Modo edición: campos necesarios para UPDATE -->
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $aviso_edit['id']; ?>">
                        <?php else: ?>
                            <!-- Modo nueva: solo indica acción INSERT -->
                            <input type="hidden" name="accion" value="nueva">
                        <?php endif; ?>

                        <!-- Campo Título - Requerido -->
                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">Título *</label>
                            <input type="text" name="titulo" class="dashboard_avisos_form_input" required 
                                   value="<?php echo htmlspecialchars($modo_edit ? $aviso_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                                   placeholder="Ej: Reunión Consejo Escolar">
                        </div>

                        <!-- Campo Enlace - Opcional -->
                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">Enlace (opcional)</label>
                            <input type="url" name="enlace" class="dashboard_avisos_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $aviso_edit['enlace'] : ($_POST['enlace'] ?? '')); ?>"
                                   placeholder="documentos/matriculacion.pdf">
                        </div>

                        <!-- Checkbox para marcar como IMPORTANTE -->
                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">
                                <!-- Estado del checkbox según modo edición o POST -->
                                <input type="checkbox" class="dashboard_avisos_form_checkbox" name="importante" <?php echo ($modo_edit && $aviso_edit['importante']) || isset($_POST['importante']) ? 'checked' : ''; ?>>
                                Marcar como IMPORTANTE
                            </label>
                        </div>

                        <!-- Campo Texto - Requerido (ocupa toda la anchura del grid) -->
                        <div class="dashboard_avisos_form_group dashboard_avisos_form_group_wide">
                            <label class="dashboard_avisos_form_label">Texto del Aviso *</label>
                            <textarea name="texto" class="dashboard_avisos_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $aviso_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                        </div>

                        <!-- Botones de acción del formulario -->
                        <div class="dashboard_avisos_btn_group">
                            <!-- Botón principal: Guardar -->
                            <button type="submit" class="dashboard_avisos_btn dashboard_avisos_btn_primary">
                                <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Aviso
                            </button>

                            <!-- Botón Cancelar solo en modo edición -->
                            <?php if ($modo_edit): ?>
                                <a href="dashboard_avisos.php" class="dashboard_avisos_btn dashboard_avisos_btn_secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- LISTADO COMPLETO DE AVISOS PUBLICADOS -->
                <!-- Grid responsive con tarjetas individuales -->
                <div class="dashboard_avisos_seccion_avisos">
                    <!-- Contador dinámico de avisos existentes -->
                    <h2><i class="fas fa-list"></i> Avisos Publicados (<?php echo count($avisos); ?>)</h2>

                    <?php if (!empty($avisos)): ?>
                        <!-- Grid responsive de tarjetas de avisos -->
                        <div class="dashboard_avisos_noticias_grid">
                            <?php foreach ($avisos as $aviso): ?>
                                <!-- Tarjeta individual de cada aviso -->
                                <div class="dashboard_avisos_noticia_card <?php echo $aviso['importante'] ? 'dashboard_avisos_importante' : ''; ?>">

                                    <!-- Título del aviso + indicador IMPORTANTE -->
                                    <h3 class="dashboard_avisos_noticia_titulo">
                                        <?php echo htmlspecialchars($aviso['titulo']); ?>
                                        <?php if ($aviso['importante']): ?>
                                            <span class="dashboard_avisos_etiqueta_importante">IMPORTANTE</span>
                                        <?php endif; ?>
                                    </h3>

                                    <!-- Fecha de publicación + nombre del último editor -->
                                    <div class="dashboard_avisos_noticia_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($aviso['fecha'])); ?>
                                        <?php if (!empty($aviso['ultima_edicion_usuario_nombre'])): ?>
                                            <!-- Auditoría: quién modificó por última vez -->
                                            <br><small class="dashboard_avisos_fecha_editor"><?php echo htmlspecialchars($aviso['ultima_edicion_usuario_nombre']); ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Preview del contenido (primeros 150 caracteres) -->
                                    <div class="dashboard_avisos_noticia_contenido">
                                        <?php echo htmlspecialchars(substr($aviso['texto'], 0, 150)); ?>...
                                    </div>

                                    <!-- Enlace externo si existe -->
                                    <?php if ($aviso['enlace']): ?>
                                        <a href="<?php echo htmlspecialchars($aviso['enlace']); ?>" class="dashboard_avisos_noticia_enlace" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Ver documento
                                        </a>
                                    <?php endif; ?>

                                    <!-- BOTONES DE ACCIÓN - Solo iconos para espacio reducido -->
                                    <div class="dashboard_avisos_acciones_botones">
                                        <!-- Botón Editar - Enlace directo con parámetro GET -->
                                        <a href="?editar=<?php echo $aviso['id']; ?>" class="dashboard_avisos_btn_small dashboard_avisos_btn_editar" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Botón Eliminar con confirmación JavaScript -->
                                        <form method="POST" class="dashboard_avisos_eliminar_form" onsubmit="return confirm('¿Eliminar este aviso?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $aviso['id']; ?>">
                                            <button type="submit" class="dashboard_avisos_btn_small dashboard_avisos_btn_delete" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Estado vacío - No hay avisos creados -->
                        <div class="dashboard_avisos_vacio">
                            <i class="fas fa-bell-slash"></i>
                            <h3>No hay avisos</h3>
                            <p>Añade el primer aviso con el formulario de arriba</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; // Fin restricción admin     ?>

            <!-- Botón universal para volver al dashboard anterior -->
            <form method="POST" action="dashboard_secretaria.php" class="dashboard_universal_volver">
                <button type="submit" class="dashboard_universal_btn_volver">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </form>
        </div>
    </body>
</html>
