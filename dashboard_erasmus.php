<?php
// Dashboard completo gestión noticias Erasmus+ IES La Arboleda
session_start(); // Iniciar sesión para autenticación y mensajes
include 'conexion.php'; // Conexión segura MySQLi
// Verificar usuario autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir si no está logueado
    exit; // Parar ejecución
}

$titulo_dashboard = "Dashboard Erasmus"; // Título para header
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro'); // PROCESAR ACCIONES

$mensaje = isset($_GET['msj']) ? $_GET['msj'] : '';
$nombre_profesor = $_SESSION['usuario_nombre'] ?? '';

// Si no existe el nombre en sesión, lo busca en la tabla profesores
if ($nombre_profesor === '') {
    $stmt_nombre = $conexion->prepare("SELECT nombre FROM profesores WHERE usuario_id = ? LIMIT 1");
    $stmt_nombre->bind_param("i", $_SESSION['usuario_id']);
    $stmt_nombre->execute();
    $res_nombre = $stmt_nombre->get_result();
    if ($fila_nombre = $res_nombre->fetch_assoc()) {
        $nombre_profesor = $fila_nombre['nombre'];
    }
    $stmt_nombre->close();
}

if ($_POST && isset($_POST['accion'])) {
    // Switch principal según acción del formulario
    switch ($_POST['accion']) {

        // ELIMINAR noticia por ID
        case 'eliminar':
            $id = (int) $_POST['id']; // Convertir a entero (seguridad)
            $stmt = $conexion->prepare("DELETE FROM erasmus_news WHERE id = ?");
            $stmt->bind_param("i", $id); // Parámetro entero
            if ($stmt->execute()) {
                // CAMBIO: Redirección para limpiar
                header("Location: dashboard_erasmus.php?msj=Noticia eliminada correctamente");
                exit;
            }
            $stmt->close();
            break;

        // CREAR nueva noticia completa
        case 'nueva':
            $titulo = trim($_POST['titulo']); // Limpiar espacios
            $contenido = trim($_POST['contenido']);
            $fecha = $_POST['fecha'];
            $enlace = trim($_POST['enlace']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';
            $video = trim($_POST['video']);
            $pdf = trim($_POST['pdf']);

            // SUBIDA DE IMAGEN NUEVA
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/'; // Carpeta destino
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true); // Crear si no existe

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Formatos permitidos

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'erasmus_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path; // Guardar ruta en BD
                    }
                }
            }

            // INSERTAR nueva noticia en base de datos
            $stmt = $conexion->prepare("INSERT INTO erasmus_news (titulo, contenido, fecha, enlace, imagen, video, pdf, ultima_edicion_fecha, ultima_edicion_nombre, activo) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, 1)");
            $stmt->bind_param("ssssssss", $titulo, $contenido, $fecha, $enlace, $imagen, $video, $pdf, $nombre_profesor);
            if ($stmt->execute()) {
                // CAMBIO: Redirección para resetear formulario
                header("Location: dashboard_erasmus.php?msj=Noticia añadida correctamente");
                exit;
            }
            $stmt->close();
            break;

        // EDITAR noticia existente
        case 'editar':
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $contenido = trim($_POST['contenido']);
            $fecha = $_POST['fecha'];
            $enlace = trim($_POST['enlace']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';
            $video = trim($_POST['video']);
            $pdf = trim($_POST['pdf']);

            // SUBIDA IMAGEN NUEVA (reemplaza anterior)
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'erasmus_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) {
                            unlink($_POST['imagen_existente']); // Borrar archivo viejo
                        }
                    }
                }
            }

            // ACTUALIZAR noticia en base de datos
            $stmt = $conexion->prepare("UPDATE erasmus_news SET titulo=?, contenido=?, fecha=?, enlace=?, imagen=?, video=?, pdf=?, ultima_edicion_fecha=NOW(), ultima_edicion_nombre=? WHERE id=?");
            $stmt->bind_param("ssssssssi", $titulo, $contenido, $fecha, $enlace, $imagen, $video, $pdf, $nombre_profesor, $id);

            if ($stmt->execute()) {
                // CAMBIO: Redirección para limpiar modo edición
                header("Location: dashboard_erasmus.php?msj=Noticia actualizada correctamente");
                exit;
            }
            $stmt->close();
            break;
    }
}

// CARGAR TODAS LAS NOTICIAS ACTIVAS CON AUDITORÍA
$stmt = $conexion->prepare("
    SELECT n.*
    FROM erasmus_news n
    WHERE n.activo = 1 
    ORDER BY n.fecha DESC
"); // Más recientes primero
$stmt->execute();
$resultado = $stmt->get_result();
$noticias = []; // Array vacío para noticias
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila; // Añadir cada noticia al array
}
$stmt->close();

// MODO EDICIÓN - Cargar noticia específica desde URL ?editar=ID
$modo_edit = false; // Flag para formulario edición
$noticia_edit = null; // Datos de noticia a editar
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar']; // ID desde URL
    $stmt = $conexion->prepare("SELECT * FROM erasmus_news WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $noticia_edit = $result->fetch_assoc(); // Cargar datos noticia
    $modo_edit = $noticia_edit !== null; // Activar modo edición si existe
    $stmt->close();
}
?>
<?php include 'dashboard_head.php'; ?>

<!DOCTYPE html> <html lang="es"> <head>
        <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Gestión Erasmus+ - Dashboard Admin</title> <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <link rel="stylesheet" href="style_dashboard.css"> </head>
    <body>
        <div class="dashboard_erasmus_container">


            <?php if (!$is_admin): ?>
                <div class="dashboard_erasmus_no_admin"> <i class="fas fa-lock"></i> <h2>Solo administradores pueden gestionar el contenido</h2>
                </div>
            <?php else: ?> <?php if ($mensaje): ?> <div class="dashboard_erasmus_alert dashboard_erasmus_alert_success"> <?php echo htmlspecialchars($mensaje); ?> </div>
                <?php endif; ?>

                <div class="dashboard_erasmus_seccion_form <?php echo $modo_edit ? 'dashboard_erasmus_modo_edit' : ''; ?>">
                    <h2>
                        <?php if ($modo_edit): ?> <i class="fas fa-edit"></i> Editar Noticia (ID: <?php echo $noticia_edit['id']; ?>)
                        <?php else: ?> <i class="fas fa-plus"></i> Nueva Noticia Erasmus+
                        <?php endif; ?>
                    </h2>

                    <form method="POST" class="dashboard_erasmus_form_grid" enctype="multipart/form-data">

                        <?php if ($modo_edit): ?> <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $noticia_edit['id']; ?>">
                            <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>">
                        <?php else: ?> <input type="hidden" name="accion" value="nueva">
                        <?php endif; ?>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Título *</label>
                            <input type="text" name="titulo" class="dashboard_erasmus_form_input" required 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                                   placeholder="Ej: 2025-26 Becas Erasmus+">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Fecha noticia *</label>
                            <input type="date" name="fecha" class="dashboard_erasmus_form_input" required 
                                   value="<?php echo $modo_edit ? $noticia_edit['fecha'] : date('Y-m-d'); ?>">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Enlace (opcional)</label>
                            <input type="url" name="enlace" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['enlace'] : ($_POST['enlace'] ?? '')); ?>"
                                   placeholder="https://site.educa.madrid.org/...">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <?php if ($modo_edit && $noticia_edit['imagen']): ?> <label class="dashboard_erasmus_form_label">Imagen actual:</label>
                                <div class="dashboard_erasmus_imagen_actual">
                                    <img src="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>" alt="Imagen actual" class="dashboard_erasmus_imagen_actual_img">
                                    <p class="dashboard_erasmus_imagen_actual_nombre"><?php echo htmlspecialchars(basename($noticia_edit['imagen'])); ?></p>
                                </div>
                            <?php endif; ?>
                            <label class="dashboard_erasmus_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label>
                            <input type="file" name="imagen" class="dashboard_erasmus_form_input" accept="image/*">
                            <small class="dashboard_erasmus_small_text">Máx 5MB. Deja vacío para mantener la actual</small>
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Video (opcional)</label>
                            <input type="url" name="video" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['video'] : ($_POST['video'] ?? '')); ?>"
                                   placeholder="https://youtube.com/...">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">PDF (opcional)</label>
                            <input type="text" name="pdf" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['pdf'] : ($_POST['pdf'] ?? '')); ?>"
                                   placeholder="pdfs/documento.pdf">
                        </div>

                        <div class="dashboard_erasmus_form_group dashboard_erasmus_form_group_full">
                            <label class="dashboard_erasmus_form_label">Contenido *</label>
                            <textarea name="contenido" class="dashboard_erasmus_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $noticia_edit['contenido'] : ($_POST['contenido'] ?? '')); ?></textarea>
                        </div>

                        <div class="dashboard_erasmus_btn_group">
                            <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                                <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Noticia
                            </button>
                            <?php if ($modo_edit): ?>
                                <a href="dashboard_erasmus.php" style="text-decoration:none; background:#888;" class="dashboard_erasmus_btn">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="dashboard_erasmus_seccion_lista">
                    <h2><i class="fas fa-list"></i> Noticias Publicadas (<?php echo count($noticias); ?>)</h2>

                    <?php if (!empty($noticias)): ?> <div class="dashboard_erasmus_noticias_grid"> <?php foreach ($noticias as $noticia): ?> <div class="dashboard_erasmus_noticia_card"> <?php if ($noticia['imagen']): ?> <div class="dashboard_erasmus_noticia_imagen">
                                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                        </div>
                                    <?php endif; ?>

                                    <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>

                                    <div class="dashboard_erasmus_noticia_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                        <?php if (!empty($noticia['ultima_edicion_nombre'])): ?>
                                            <br>
                                            <small class="dashboard_erasmus_auditoria_text">
                                                Editado por: <?php echo htmlspecialchars($noticia['ultima_edicion_nombre']); ?> </small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="dashboard_erasmus_noticia_contenido">
                                        <?php echo htmlspecialchars(substr($noticia['contenido'], 0, 150)); ?>...
                                    </div>

                                    <div class="dashboard_erasmus_noticia_medios">
                                        <?php if ($noticia['enlace']): ?>
                                            <a href="<?php echo htmlspecialchars($noticia['enlace']); ?>" class="dashboard_erasmus_noticia_enlace" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> Ver completo
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($noticia['video']): ?>
                                            <a href="<?php echo htmlspecialchars($noticia['video']); ?>" class="dashboard_erasmus_noticia_video" target="_blank">
                                                <i class="fas fa-video"></i> Video
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($noticia['pdf']): ?>
                                            <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" class="dashboard_erasmus_noticia_pdf" target="_blank">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <div class="dashboard_erasmus_acciones_botones">
                                        <a href="?editar=<?php echo $noticia['id']; ?>" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form class="dashboard_erasmus_form_inline" method="POST" onsubmit="return confirm('¿Eliminar esta noticia?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                            <button type="submit" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_delete">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div> <?php endforeach; ?>
                        </div> <?php else: ?> <div class="dashboard_erasmus_vacio"> <i class="fas fa-plane"></i> <h3>No hay noticias Erasmus+</h3>
                            <p>Añade la primera noticia con el formulario de arriba</p>
                        </div>
                    <?php endif; ?>
                </div> <?php endif; ?> <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
                <button type="submit" class="dashboard_universal_btn_volver">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </form>
        </div> </body>
</html>