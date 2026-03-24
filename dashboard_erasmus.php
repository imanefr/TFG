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
$is_admin = ($_SESSION['usuario_rol'] === 'admin'); // Solo admins gestionan
// PROCESAR ACCIONES - CRUD completo (Crear, Leer, Actualizar, Eliminar)
$mensaje = ''; // Variable para mensajes de éxito/error
if ($_POST && isset($_POST['accion'])) {
    // Switch principal según acción del formulario
    switch ($_POST['accion']) {

        // ELIMINAR noticia por ID
        case 'eliminar':
            $id = (int) $_POST['id']; // Convertir a entero (seguridad)
            $stmt = $conexion->prepare("DELETE FROM erasmus_news WHERE id = ?");
            $stmt->bind_param("i", $id); // Parámetro entero
            if ($stmt->execute())
                $mensaje = 'Noticia eliminada correctamente'; // Mensaje éxito
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
                    // Nombre único: erasmus_timestamp_random.ext
                    $new_filename = 'erasmus_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path; // Guardar ruta en BD
                    }
                }
            }

            // INSERTAR nueva noticia en base de datos
            $stmt = $conexion->prepare("INSERT INTO erasmus_news (titulo, contenido, fecha, enlace, imagen, video, pdf, activo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssssss", $titulo, $contenido, $fecha, $enlace, $imagen, $video, $pdf);
            if ($stmt->execute())
                $mensaje = 'Noticia añadida correctamente';
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
                        // Eliminar imagen anterior
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) {
                            unlink($_POST['imagen_existente']); // Borrar archivo viejo
                        }
                    }
                }
            }

            // ACTUALIZAR noticia en base de datos
            $stmt = $conexion->prepare("
    UPDATE erasmus_news 
    SET titulo=?, contenido=?, fecha=?, enlace=?, imagen=?, video=?, pdf=?, 
        ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? 
    WHERE id=?
");
            $stmt->bind_param("ssssssssi", $titulo, $contenido, $fecha, $enlace, $imagen, $video, $pdf, $_SESSION['usuario_id'], $id);

            if ($stmt->execute())
                $mensaje = 'Noticia actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR TODAS LAS NOTICIAS ACTIVAS CON AUDITORÍA
$stmt = $conexion->prepare("
    SELECT n.*, u.nombre AS ultima_edicion_usuario_nombre
    FROM erasmus_news n
    LEFT JOIN usuarios u ON n.ultima_edicion_usuario_id = u.id
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

<!DOCTYPE html> <!-- Documento HTML5 -->
<html lang="es"> <!-- Español para accesibilidad -->

    <head>
        <!-- Configuración básica página -->
        <meta charset="UTF-8"> <!-- UTF-8 para ñ y acentos -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive móviles -->
        <title>Gestión Erasmus+ - Dashboard Admin</title> <!-- Título pestaña -->

        <!-- Recursos externos CDN -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Iconos -->
        <link rel="stylesheet" href="style_dashboard.css"> <!-- CSS personalizado -->
    </head>

    <body>
        <!-- Contenedor principal dashboard Erasmus -->
        <div class="dashboard_erasmus_container">

            <!-- Header reutilizable con datos usuario -->
<?php include 'dashboard_head.php'; ?>

            <!-- RESTRICCION ACCESO - Solo administradores -->
<?php if (!$is_admin): ?>
                <div class="dashboard_erasmus_no_admin"> <!-- Mensaje bloqueo no-admin -->
                    <i class="fas fa-lock"></i> <!-- Icono candado -->
                    <h2>Solo administradores pueden gestionar el contenido</h2>
                </div>
<?php else: ?> <!-- Si ES admin, mostrar contenido -->

                <!-- MENSAJE ÉXITO/ERROR -->
    <?php if ($mensaje): ?> <!-- Solo si existe mensaje -->
                    <div class="dashboard_erasmus_alert dashboard_erasmus_alert_success"> <!-- Alerta verde -->
                    <?php echo htmlspecialchars($mensaje); ?> <!-- Mensaje seguro XSS -->
                    </div>
                    <?php endif; ?>

                <!-- FORMULARIO PRINCIPAL - Nueva o editar noticia -->
                <div class="dashboard_erasmus_seccion_form <?php echo $modo_edit ? 'dashboard_erasmus_modo_edit' : ''; ?>">
                    <!-- Título dinámico según modo -->
                    <h2>
    <?php if ($modo_edit): ?> <!-- MODO EDICIÓN -->
                            <i class="fas fa-edit"></i> Editar Noticia (ID: <?php echo $noticia_edit['id']; ?>)
                        <?php else: ?> <!-- MODO NUEVA -->
                            <i class="fas fa-plus"></i> Nueva Noticia Erasmus+
                        <?php endif; ?>
                    </h2>

                    <!-- Formulario con subida archivos -->
                    <form method="POST" class="dashboard_erasmus_form_grid" enctype="multipart/form-data">

                        <!-- Campos ocultos según modo -->
    <?php if ($modo_edit): ?> <!-- Edición: ID + imagen actual -->
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $noticia_edit['id']; ?>">
                            <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>">
    <?php else: ?> <!-- Nueva: solo acción -->
                            <input type="hidden" name="accion" value="nueva">
                        <?php endif; ?>

                        <!-- TÍTULO noticia (obligatorio) -->
                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Título *</label>
                            <input type="text" name="titulo" class="dashboard_erasmus_form_input" required 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                                   placeholder="Ej: 2025-26 Becas Erasmus+">
                        </div>

                        <!-- ENLACE externo opcional -->
                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Enlace (opcional)</label>
                            <input type="url" name="enlace" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['enlace'] : ($_POST['enlace'] ?? '')); ?>"
                                   placeholder="https://site.educa.madrid.org/...">
                        </div>

                        <!-- SUBIDA IMAGEN -->
                        <div class="dashboard_erasmus_form_group">
    <?php if ($modo_edit && $noticia_edit['imagen']): ?> <!-- Mostrar imagen actual -->
                                <label class="dashboard_erasmus_form_label">Imagen actual:</label>
                                <div class="dashboard_erasmus_imagen_actual">
                                    <img src="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>" alt="Imagen actual" class="dashboard_erasmus_imagen_actual_img">
                                    <p class="dashboard_erasmus_imagen_actual_nombre"><?php echo htmlspecialchars(basename($noticia_edit['imagen'])); ?></p>
                                </div>
    <?php endif; ?>
                            <label class="dashboard_erasmus_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label>
                            <input type="file" name="imagen" class="dashboard_erasmus_form_input" accept="image/*">
                            <small class="dashboard_erasmus_small_text">Máx 5MB. Deja vacío para mantener la actual</small>
                        </div>

                        <!-- VIDEO embebido opcional -->
                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Video (opcional)</label>
                            <input type="url" name="video" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['video'] : ($_POST['video'] ?? '')); ?>"
                                   placeholder="https://youtube.com/...">
                        </div>

                        <!-- ARCHIVO PDF opcional -->
                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">PDF (opcional)</label>
                            <input type="text" name="pdf" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['pdf'] : ($_POST['pdf'] ?? '')); ?>"
                                   placeholder="pdfs/documento.pdf">
                        </div>

                        <!-- CONTENIDO principal (obligatorio, textarea grande) -->
                        <div class="dashboard_erasmus_form_group dashboard_erasmus_form_group_full">
                            <label class="dashboard_erasmus_form_label">Contenido *</label>
                            <textarea name="contenido" class="dashboard_erasmus_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $noticia_edit['contenido'] : ($_POST['contenido'] ?? '')); ?></textarea>
                        </div>

                        <!-- BOTONES acción -->
                        <div class="dashboard_erasmus_btn_group">
                            <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                                <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Noticia
                            </button>
                        </div>
                    </form>
                </div>

                <!-- LISTADO COMPLETO noticias publicadas -->
                <div class="dashboard_erasmus_seccion_lista">
                    <h2><i class="fas fa-list"></i> Noticias Publicadas (<?php echo count($noticias); ?>)</h2>

    <?php if (!empty($noticias)): ?> <!-- Si hay noticias -->
                        <div class="dashboard_erasmus_noticias_grid"> <!-- Grid responsive tarjetas -->
                        <?php foreach ($noticias as $noticia): ?> <!-- Bucle cada noticia -->
                                <div class="dashboard_erasmus_noticia_card"> <!-- Tarjeta individual -->

            <?php if ($noticia['imagen']): ?> <!-- Si tiene imagen -->
                                        <div class="dashboard_erasmus_noticia_imagen">
                                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                        </div>
            <?php endif; ?>

                                    <!-- Título noticia -->
                                    <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>

                                    <!-- Fecha + auditoría última edición -->
                                    <div class="dashboard_erasmus_noticia_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
            <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                                            <br>
                                            <small class="dashboard_erasmus_auditoria_text">
                <?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?> <!-- Quién editó -->
                                            </small>
                                            <?php endif; ?>
                                    </div>

                                    <!-- Preview contenido (150 primeros caracteres) -->
                                    <div class="dashboard_erasmus_noticia_contenido">
            <?php echo htmlspecialchars(substr($noticia['contenido'], 0, 150)); ?>...
                                    </div>

                                    <!-- Enlaces multimedia -->
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

                                    <!-- BOTONES acción (editar/eliminar) -->
                                    <div class="dashboard_erasmus_acciones_botones">
                                        <!-- Editar noticia -->
                                        <a href="?editar=<?php echo $noticia['id']; ?>" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <!-- Eliminar con confirmación JavaScript -->
                                        <form class="dashboard_erasmus_form_inline" method="POST" onsubmit="return confirm('¿Eliminar esta noticia?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                            <button type="submit" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_delete">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div> <!-- Fin tarjeta noticia -->
        <?php endforeach; ?>
                        </div> <!-- Fin grid noticias -->
                        <?php else: ?> <!-- Si NO hay noticias -->
                        <div class="dashboard_erasmus_vacio"> <!-- Estado vacío -->
                            <i class="fas fa-plane"></i> <!-- Icono avión Erasmus -->
                            <h3>No hay noticias Erasmus+</h3>
                            <p>Añade la primera noticia con el formulario de arriba</p>
                        </div>
    <?php endif; ?>
                </div> <!-- Fin sección lista -->
                <?php endif; ?> <!-- Fin restricción admin -->

            <!-- BOTÓN VOLVER al dashboard principal -->
            <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
                <button type="submit" class="dashboard_universal_btn_volver">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </form>
        </div> <!-- Fin contenedor principal -->
    </body>
</html>
