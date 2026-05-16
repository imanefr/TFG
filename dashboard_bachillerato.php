<?php
session_start();  // Inicia la sesión del usuario
include 'conexion.php';  // Conecta a la base de datos

if (!isset($_SESSION['usuario_id'])) {  // Verifica si hay sesión activa
    header('Location: login.php');  // Redirige al login si no está autenticado
    exit;  // Termina la ejecución
}
$titulo_dashboard = "Dashboard Titulación Bachillerato";  // Título de la página

// Verifica si el usuario tiene permisos de admin/profesor/otro
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');

// PROCESAR ACCIONES - Maneja formularios POST
$mensaje = '';  // Variable para mensajes de éxito/error
if ($_POST && isset($_POST['accion'])) {  // Si se envió un formulario
    switch ($_POST['accion']) {
        case 'eliminar':  // Acción: eliminar noticia
            $id = (int) $_POST['id'];  // Convierte ID a entero
            $stmt = $conexion->prepare("DELETE FROM titulo_bach WHERE id = ?");  // Prepara DELETE
            $stmt->bind_param("i", $id);  // Vincula ID
            if ($stmt->execute())  // Ejecuta eliminación
                $mensaje = 'Sección eliminada correctamente';  // Mensaje de éxito
            $stmt->close();  // Cierra statement
            break;

        case 'nueva':  // Acción: crear nueva noticia
            $titulo = trim($_POST['titulo']);  // Limpia título
            $texto = trim($_POST['texto']);  // Limpia texto
            $fecha = $_POST['fecha'];  // Fecha de publicación
            $enlace = trim($_POST['enlace']);  // URL externa
            $texto_enlace = trim($_POST['texto_enlace']);  // Texto del enlace
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';  // Imagen existente
            $video = trim($_POST['video']);  // URL video
            $pdf = trim($_POST['pdf']);  // Ruta PDF

            // SUBIDA DE IMAGEN - Procesa archivo subido
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';  // Carpeta de destino
                if (!is_dir($upload_dir))  // Crea carpeta si no existe
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));  // Extensión archivo
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];  // Extensiones permitidas

                if (in_array($file_extension, $allowed)) {  // Valida extensión
                    $new_filename = 'bolsa_empleo_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;  // Nombre único
                    $upload_path = $upload_dir . $new_filename;  // Ruta completa

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {  // Mueve archivo
                        $imagen = $upload_path;  // Actualiza ruta imagen
                    }
                }
            }

            // Inserta nueva noticia en base de datos
            $stmt = $conexion->prepare("INSERT INTO titulo_bach (titulo, texto, link, texto_link, imagen, video, pdf, fecha_publicacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $titulo, $texto, $enlace, $texto_enlace, $imagen, $video, $pdf, $fecha);
            if ($stmt->execute())
                $mensaje = 'Noticia añadida correctamente';  // Mensaje éxito
            $stmt->close();
            break;

        case 'editar':  // Acción: editar noticia existente
            $id = (int) $_POST['id'];  // ID de la noticia
            $titulo = trim($_POST['titulo']);  // Nuevo título
            $texto = trim($_POST['texto']);  // Nuevo texto
            $fecha = $_POST['fecha'];  // Nueva fecha
            $enlace = trim($_POST['enlace']);  // Nuevo enlace
            $texto_enlace = trim($_POST['texto_enlace']);  // Nuevo texto enlace
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';  // Imagen actual
            $video = trim($_POST['video']);  // Nuevo video
            $pdf = trim($_POST['pdf']);  // Nuevo PDF

            // SUBIDA DE IMAGEN NUEVA - Reemplaza imagen anterior
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'bolsa_empleo_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;  // Nueva imagen
                        // Eliminar imagen anterior si existe
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) {
                            unlink($_POST['imagen_existente']);  // Borra archivo anterior
                        }
                    }
                }
            }

            // ACTUALIZAR noticia existente en base de datos
            $stmt = $conexion->prepare("
                UPDATE titulo_bach 
                SET titulo=?, texto=?, fecha_publicacion=?, link=?, texto_link=?, imagen=?, video=?, pdf=?,
                    ultima_edicion_usuario_id=?, ultima_edicion_fecha=NOW()
                WHERE id=?
            ");
            $stmt->bind_param("ssssssssii", $titulo, $texto, $fecha, $enlace, $texto_enlace, $imagen, $video, $pdf, $_SESSION['usuario_id'], $id);
            if ($stmt->execute())
                $mensaje = 'Noticia actualizada correctamente';  // Mensaje éxito
            $stmt->close();
            break;
    }
}

// CARGAR NOTICIAS CON NOMBRE DEL USUARIO - Consulta todas las noticias ordenadas
$stmt = $conexion->prepare("
    SELECT n.*, u.nombre AS ultima_edicion_usuario_nombre
    FROM titulo_bach n
    LEFT JOIN usuarios u ON n.ultima_edicion_usuario_id = u.id
    ORDER BY n.fecha_publicacion DESC
");

$stmt->execute();
$resultado = $stmt->get_result();  // Obtiene resultados
$noticias = [];  // Array para almacenar noticias
while ($fila = $resultado->fetch_assoc()) {  // Recorre todas las noticias
    $noticias[] = $fila;  // Agrega a array
}
$stmt->close();  // Cierra statement

// EDITAR MODO - Carga datos para edición
$modo_edit = false;  // Flag para modo edición
$noticia_edit = null;  // Datos de noticia a editar
if (isset($_GET['editar'])) {  // Si se pasa parámetro editar
    $id_edit = (int) $_GET['editar'];  // ID a editar
    $stmt = $conexion->prepare("SELECT * FROM titulo_bach WHERE id = ?");  // Consulta noticia específica
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $noticia_edit = $result->fetch_assoc();  // Carga datos noticia
    $modo_edit = $noticia_edit !== null;  // Activa modo edición
    $stmt->close();
}
?>
     <!-- HEADER - Incluye barra de navegación -->
        <?php include 'dashboard_head.php'; ?>
<!DOCTYPE html>  <!-- Declara documento HTML5 -->
<html lang="es">  <!-- Idioma español -->
<head>  <!-- Metadatos de la página -->
    <meta charset="UTF-8">  <!-- Codificación UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Diseño responsive -->
    <title>Gestión Titulación Bachillerato - Dashboard Admin</title>  <!-- Título navegador -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">  <!-- Iconos FontAwesome -->
    <link rel="stylesheet" href="style_dashboard.css">  <!-- Estilos personalizados -->
</head>
<body>  <!-- Cuerpo de la página -->
    <div class="dashboard_erasmus_container">  <!-- Contenedor principal dashboard -->
   

        <?php if (!$is_admin): ?>  <!-- Si no es admin/profesor -->
            <div class="dashboard_erasmus_no_admin">  <!-- Mensaje sin permisos -->
                <i class="fas fa-lock"></i>  <!-- Icono candado -->
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>  <!-- Si tiene permisos de admin -->

            <?php if ($mensaje): ?>  <!-- Muestra mensaje de éxito/error -->
                <div class="dashboard_erasmus_alert dashboard_erasmus_alert_success">
                    <?php echo htmlspecialchars($mensaje); ?>  <!-- Mensaje escapado -->
                </div>
            <?php endif; ?>

            <!-- FORMULARIO - Crea/edita noticias -->
            <div class="dashboard_erasmus_seccion_form <?php echo $modo_edit ? 'dashboard_erasmus_modo_edit' : ''; ?>">
                <h2>  <!-- Título formulario dinámico -->
                    <?php if ($modo_edit): ?>  <!-- Modo edición -->
                        <i class="fas fa-edit"></i> Editar Noticia (ID: <?php echo $noticia_edit['id']; ?>)
                    <?php else: ?>  <!-- Modo creación -->
                        <i class="fas fa-plus"></i> Nueva Noticia
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_erasmus_form_grid" enctype="multipart/form-data">  <!-- Formulario con subida archivos -->
                    <?php if ($modo_edit): ?>  <!-- Campos ocultos para edición -->
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $noticia_edit['id']; ?>">
                        <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>">
                    <?php else: ?>  <!-- Campo oculto para nueva -->
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <div class="dashboard_erasmus_form_group">  <!-- Campo título -->
                        <label class="dashboard_erasmus_form_label">Título *</label>
                        <input type="text" name="titulo" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Listado Admisiones 2025-26">
                    </div>

                    <div class="dashboard_erasmus_form_group">  <!-- Campo fecha -->
                        <label class="dashboard_erasmus_form_label">Fecha *</label>
                        <input type="date" name="fecha" class="dashboard_erasmus_form_input" required 
                               value="<?php echo $modo_edit ? $noticia_edit['fecha_publicacion'] : ($_POST['fecha'] ?? date('Y-m-d')); ?>">
                    </div>

                    <div class="dashboard_erasmus_form_group">  <!-- Campos enlace -->
                        <label class="dashboard_erasmus_form_label">Enlace (opcional)</label>
                        <input type="text" name="texto_enlace" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['texto_link'] : ($_POST['texto_enlace'] ?? '')); ?>"
                               placeholder="Texto del enlace">
                        <input type="url" name="enlace" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['link'] : ($_POST['enlace'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>

                    <!-- INPUT FILE NORMAL - Gestión de imagen -->
                    <div class="dashboard_erasmus_form_group">
                        <?php if ($modo_edit && $noticia_edit['imagen']): ?>  <!-- Muestra imagen actual en edición -->
                            <label class="dashboard_erasmus_form_label">Imagen actual:</label>
                            <div class="dashboard_erasmus_imagen_actual">
                                <img src="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>" alt="Imagen actual" style="max-width: 150px; max-height: 100px; border-radius: 8px;">
                                <p style="font-size: 0.9rem; color: var(--gris);"><?php echo htmlspecialchars(basename($noticia_edit['imagen'])); ?></p>
                            </div>
                        <?php endif; ?>
                        <label class="dashboard_erasmus_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label>
                        <input type="file" name="imagen" class="dashboard_erasmus_form_input" accept="image/*">
                        <small style="color: var(--gris);">Máx 5MB. Deja vacío para mantener la actual</small>
                    </div>

                    <div class="dashboard_erasmus_form_group">  <!-- Campo video -->
                        <label class="dashboard_erasmus_form_label">Video (opcional)</label>
                        <input type="url" name="video" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['video'] : ($_POST['video'] ?? '')); ?>"
                               placeholder="https://youtube.com/...">
                    </div>

                    <div class="dashboard_erasmus_form_group">  <!-- Campo PDF -->
                        <label class="dashboard_erasmus_form_label">PDF (opcional)</label>
                        <input type="text" name="pdf" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['pdf'] : ($_POST['pdf'] ?? '')); ?>"
                               placeholder="pdfs/documento.pdf">
                    </div>

                    
                    <div class="dashboard_erasmus_form_group" style="grid-column: 1 / -1;">  <!-- Campo texto principal (ocupa toda la fila) -->
                        <label class="dashboard_erasmus_form_label">Contenido *</label>
                        <textarea name="texto" class="dashboard_erasmus_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $noticia_edit['texto'] : ($_POST['contenido'] ?? '')); ?></textarea>
                    </div>

                    <div class="dashboard_erasmus_btn_group">  <!-- Botón enviar -->
                        <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Noticia
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTA DE NOTICIAS - Muestra todas las noticias existentes -->
            <div class="dashboard_erasmus_seccion_lista">
                <h2><i class="fas fa-list"></i> Lista de Noticias (<?php echo count($noticias); ?>)</h2>  <!-- Contador noticias -->
                <?php if (!empty($noticias)): ?>  <!-- Si hay noticias -->
                    <div class="dashboard_erasmus_noticias_grid">  <!-- Grid de tarjetas noticias -->
                        <?php foreach ($noticias as $noticia): ?>  <!-- Recorre cada noticia -->
                            <div class="dashboard_erasmus_noticia_card">  <!-- Tarjeta individual noticia -->
                                <?php if ($noticia['imagen']): ?>  <!-- Muestra imagen si existe -->
                                    <div class="dashboard_erasmus_noticia_imagen">
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                    </div>
                                <?php endif; ?>
                                <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>  <!-- Título noticia -->
                                <div class="dashboard_erasmus_noticia_fecha">  <!-- Fecha y editor -->
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?>
                                    <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                                        <br>
                                        <small style="color: #666; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?>  <!-- Nombre último editor -->
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <div class="dashboard_erasmus_noticia_contenido">  <!-- Resumen texto (150 chars) -->
                                    <?php echo htmlspecialchars(substr($noticia['texto'], 0, 150)); ?>...
                                </div>
                                <div class="dashboard_erasmus_noticia_medios">  <!-- Enlaces multimedia -->
                                    <?php if ($noticia['link']): ?>  <!-- Enlace externo -->
                                        <a href="solicitud_titulo_bach.php" class="dashboard_erasmus_noticia_enlace">
                                            <i class="fas fa-external-link-alt"></i> Ver completo
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($noticia['video']): ?>  <!-- Enlace video -->
                                        <a href="<?php echo htmlspecialchars($noticia['video']); ?>" class="dashboard_erasmus_noticia_video" target="_blank">
                                            <i class="fas fa-video"></i> Video
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($noticia['pdf']): ?>  <!-- Enlace PDF -->
                                        <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" class="dashboard_erasmus_noticia_pdf" target="_blank">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="dashboard_erasmus_acciones_botones">  <!-- Botones acción -->
                                    <a href="?editar=<?php echo $noticia['id']; ?>" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_editar">  <!-- Editar -->
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta noticia?')">  <!-- Eliminar con confirmación JS -->
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                        <button type="submit" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_delete">  <!-- Botón eliminar -->
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>  <!-- Mensaje cuando no hay noticias -->
                    <div class="dashboard_erasmus_vacio">
                        <i class="fas fa-plane"></i>  <!-- Icono avión (temática erasmus?) -->
                        <h3>No hay ofertas de empleo.</h3>
                        <p>Añade la primera oferta con el formulario de arriba.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Botón volver al dashboard principal -->
        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver  <!-- Flecha izquierda + texto -->
            </button>
        </form>
    </div>
</body>  <!-- Fin cuerpo página -->
</html>  <!-- Fin documento HTML -->