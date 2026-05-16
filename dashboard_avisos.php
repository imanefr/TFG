<?php
// Inicia la sesión para autenticación de usuario
session_start();

// Conecta con la base de datos
require_once 'conexion.php';

// Verifica autenticación del usuario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Configura título de la página
$titulo_dashboard = "Dashboard Avisos";

// Verifica permisos de administrador
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');

// Capturar el nombre del profesor desde la sesión (si no existe por algún motivo, un fallback seguro)
$nombre_profesor_sesion = $_SESSION['usuario_nombre'] ?? 'Usuario Desconocido';

// Inicializa variable de mensaje
$mensaje = '';
if (isset($_GET['msg'])) {
    // Procesa diferentes tipos de mensajes
    switch ($_GET['msg']) {
        case 'creado': $mensaje = 'Aviso añadido correctamente'; break;
        case 'editado': $mensaje = 'Aviso actualizado correctamente'; break;
        case 'eliminado': $mensaje = 'Aviso eliminado correctamente'; break;
        case 'error': $mensaje = 'Error al procesar la solicitud'; break;
        case 'campos': $mensaje = 'Título y texto son obligatorios'; break;
    }
}

// Procesa acciones del formulario
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    switch ($_POST['accion']) {

        case 'eliminar':
            // Elimina aviso específico
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM avisos WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header("Location: dashboard_avisos.php?msg=eliminado");
                exit;
            }
            break;

        case 'nueva':
            // Crea nuevo aviso
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $enlace = trim($_POST['enlace'] ?? '');
            $importante = isset($_POST['importante']) ? 1 : 0;

            if ($titulo && $texto) {
                // CORRECCIÓN: Inserta el nombre plano de la sesión en ultima_edicion_nombre
                $stmt = $conexion->prepare("
                    INSERT INTO avisos (titulo, texto, enlace, fecha, importante, ultima_edicion_fecha, ultima_edicion_nombre) 
                    VALUES (?, ?, ?, NOW(), ?, NOW(), ?)
                ");
                // Parámetros: sssis -> $nombre_profesor_sesion es un string ('s')
                $stmt->bind_param("sssis", $titulo, $texto, $enlace, $importante, $nombre_profesor_sesion);
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: dashboard_avisos.php?msg=creado");
                    exit;
                }
            } else {
                header("Location: dashboard_avisos.php?msg=campos");
                exit;
            }
            break;

        case 'editar':
            // Actualiza aviso existente
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $enlace = trim($_POST['enlace'] ?? '');
            $importante = isset($_POST['importante']) ? 1 : 0;

            // CORRECCIÓN: Actualiza directamente la columna como texto plano
            $stmt = $conexion->prepare("
                UPDATE avisos 
                SET titulo=?, texto=?, enlace=?, fecha=NOW(), importante=?, 
                    ultima_edicion_fecha=NOW(), ultima_edicion_nombre=? 
                WHERE id=?
            ");
            // Parámetros: sssisi -> El penúltimo es el nombre ('s'), el último el ID ('i')
            $stmt->bind_param("sssisi", $titulo, $texto, $enlace, $importante, $nombre_profesor_sesion, $id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: dashboard_avisos.php?msg=editado");
                exit;
            }
            break;
    }
}

// CORRECCIÓN LÍNEA 100: Consulta simplificada sin JOIN y leyendo la columna de texto plano directo
$stmt = $conexion->prepare("
    SELECT id, titulo, texto, enlace, fecha, importante, ultima_edicion_fecha, ultima_edicion_nombre
    FROM avisos 
    ORDER BY importante DESC, fecha DESC
");
$stmt->execute();
$avisos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Verifica modo de edición
$modo_edit = false;
$aviso_edit = null;

if ($is_admin && isset($_GET['editar'])) {
    // Carga datos para edición
    $id_edit = (int) $_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM avisos WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($aviso_edit = $result->fetch_assoc()) {
        $modo_edit = true;
    }
    $stmt->close();
}
?>
        <?php include 'dashboard_head.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Avisos - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_avisos_container">


        <?php if (!$is_admin): ?>
            <div class="dashboard_avisos_no_admin">
                <i class="fas fa-lock dashboard_avisos_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar los avisos</h2>
            </div>
        <?php else: ?>

            <?php if ($mensaje): ?>
                <div class="dashboard_avisos_alert <?php echo (strpos($mensaje, 'correctamente') !== false) ? 'dashboard_avisos_alert_success' : 'dashboard_avisos_alert_error'; ?>">
                     <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <div class="dashboard_avisos_seccion_form <?php echo $modo_edit ? 'dashboard_avisos_modo_edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Aviso (ID: <?php echo $aviso_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nuevo Aviso
                    <?php endif; ?>
                </h2> 

                <form method="POST" class="dashboard_avisos_form_grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $aviso_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <div class="dashboard_avisos_form_group">
                        <label class="dashboard_avisos_form_label">Título *</label>
                        <input type="text" name="titulo" class="dashboard_avisos_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $aviso_edit['titulo'] : ''); ?>"
                               placeholder="Ej: Reunión Consejo Escolar">
                    </div>

                    <div class="dashboard_avisos_form_group">
                        <label class="dashboard_avisos_form_label">Enlace (opcional)</label>
                        <input type="url" name="enlace" class="dashboard_avisos_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $aviso_edit['enlace'] : ''); ?>"
                               placeholder="https://ejemplo.com/archivo.pdf">
                    </div>

                    <div class="dashboard_avisos_form_group">
                        <label class="dashboard_avisos_form_label">
                            <input type="checkbox" class="dashboard_avisos_form_checkbox" name="importante" <?php echo ($modo_edit && $aviso_edit['importante']) ? 'checked' : ''; ?>>
                            Marcar como IMPORTANTE
                        </label>
                    </div>

                    <div class="dashboard_avisos_form_group dashboard_avisos_form_group_wide">
                        <label class="dashboard_avisos_form_label">Texto del Aviso *</label>
                        <textarea name="texto" class="dashboard_avisos_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $aviso_edit['texto'] : ''); ?></textarea>
                    </div>

                    <div class="dashboard_avisos_btn_group">
                        <button type="submit" class="dashboard_avisos_btn dashboard_avisos_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Aviso
                        </button>

                        <?php if ($modo_edit): ?>
                            <a href="dashboard_avisos.php" class="dashboard_avisos_btn dashboard_avisos_btn_secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="dashboard_avisos_seccion_avisos">
                <h2><i class="fas fa-list"></i> Avisos Publicados (<?php echo count($avisos); ?>)</h2>

                <?php if (!empty($avisos)): ?>
                    <div class="dashboard_avisos_noticias_grid">
                        <?php foreach ($avisos as $aviso): ?>
                            <div class="dashboard_avisos_noticia_card <?php echo $aviso['importante'] ? 'dashboard_avisos_importante' : ''; ?>">
                                
                                <h3 class="dashboard_avisos_noticia_titulo">
                                    <?php echo htmlspecialchars($aviso['titulo']); ?>
                                    <?php if ($aviso['importante']): ?>
                                        <span class="dashboard_avisos_etiqueta_importante">(IMPORTANTE)</span>
                                    <?php endif; ?>
                                </h3> 

                                <div class="dashboard_avisos_noticia_fecha">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($aviso['fecha'])); ?>
                                    
                                    <?php if (!empty($aviso['ultima_edicion_nombre'])): ?>
                                        <br><small class="dashboard_avisos_fecha_editor">Editado por: <?php echo htmlspecialchars($aviso['ultima_edicion_nombre']); ?></small>
                                    <?php endif; ?>
                                </div>

                                <div class="dashboard_avisos_noticia_contenido">
                                    <?php echo htmlspecialchars(substr($aviso['texto'], 0, 150)); ?>...
                                </div>

                                <?php if ($aviso['enlace']): ?>
                                    <a href="<?php echo htmlspecialchars($aviso['enlace']); ?>" class="dashboard_avisos_noticia_enlace" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Ver documento
                                    </a>
                                <?php endif; ?>

                                <div class="dashboard_avisos_acciones_botones">
                                    <a href="?editar=<?php echo $aviso['id']; ?>" class="dashboard_avisos_btn_small dashboard_avisos_btn_editar" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

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
                    <div class="dashboard_avisos_vacio">
                        <i class="fas fa-bell-slash"></i>
                        <h3>No hay avisos</h3>
                        <p>Añade el primer aviso con el formulario de arriba</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver 
            </button>
        </form>
    </div>
</body>
</html>