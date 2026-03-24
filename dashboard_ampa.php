<?php
// Solo accesible para usuarios con rol 'admin'
// Iniciar sesión PHP para manejar autenticación del usuario
session_start();

// Importar archivo de conexión a base de datos MySQLi preparada
include 'conexion.php';

// Verificar que el usuario esté autenticado en la sesión
// Si no existe usuario_id en sesión, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit; // Termina la ejecución del script inmediatamente
}

// Definir título de la página para el dashboard
$titulo_dashboard = "Dashboard AMPA";

// Determinar si el usuario actual tiene rol de administrador
// Solo los admins pueden crear/editar/eliminar entradas
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// Variable que almacenará mensajes de éxito tras operaciones
$mensaje = '';

// PROCESADOR CENTRAL DE ACCIONES - Maneja CRUD completo
// Detecta si se envió un formulario POST con acción específica
if ($_POST && isset($_POST['accion'])) {
    
    // Switch principal que ejecuta acción según valor de $_POST['accion']
    switch ($_POST['accion']) {
        
        // Acción: Eliminar entrada específica por ID
        case 'eliminar':
            // Convertir ID a entero para evitar inyecciones SQL
            $id = (int) $_POST['id'];
            
            // Consulta preparada para eliminar entrada segura
            $stmt = $conexion->prepare("DELETE FROM ampa WHERE id = ?");
            $stmt->bind_param("i", $id); // 'i' = integer parameter
            
            // Si la eliminación fue exitosa, mostrar mensaje
            if ($stmt->execute()) {
                $mensaje = 'Entrada AMPA eliminada correctamente';
            }
            $stmt->close(); // Liberar recursos del statement
            break;

        // Acción: Activar entrada como única visible
        // Lógica: Primero desactiva TODAS, luego activa solo la seleccionada
        case 'activar':
            // Paso 1: Desactivar todas las entradas AMPA existentes
            $stmt = $conexion->prepare("UPDATE ampa SET activo = 0");
            $stmt->execute();
            $stmt->close();
            
            // Paso 2: Activar solo la entrada seleccionada + auditoría
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("UPDATE ampa SET activo = 1, ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? WHERE id = ?");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id); // Usuario actual + ID entrada
            if ($stmt->execute()) {
                $mensaje = 'Entrada AMPA activada correctamente (única visible)';
            }
            $stmt->close();
            break;

        // Acción: Crear nueva entrada AMPA completa
        case 'nueva':
            // Sanitizar y limpiar datos del formulario
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_formulario = trim($_POST['enlace_formulario']);
            $enlace_video = trim($_POST['enlace_video']);
            
            // Si hay imagen existente, mantenerla (modo editar)
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';

            // PROCESO DE SUBIDA DE IMAGEN - Validación completa
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/'; // Directorio destino para imágenes
                
                // Crear directorio si no existe
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Extraer extensión del archivo original
                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                
                // Extensiones permitidas por seguridad
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Validar extensión antes de procesar
                if (in_array($file_extension, $allowed)) {
                    // Generar nombre único para evitar colisiones
                    $new_filename = 'ampa_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    // Mover archivo temporal a destino permanente
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path; // Actualizar ruta de imagen
                    }
                }
            }

            // Insertar nueva entrada en base de datos con datos de auditoría
            $stmt = $conexion->prepare("INSERT INTO ampa (titulo, texto, imagen, enlace_formulario, enlace_video, fecha_actualizacion, ultima_edicion_fecha, ultima_edicion_usuario_id, activo) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, 0)");
            $stmt->bind_param("ssssi", $titulo, $texto, $imagen, $enlace_formulario, $enlace_video, $_SESSION['usuario_id']);
            
            if ($stmt->execute()) {
                $mensaje = 'Entrada AMPA añadida correctamente';
            }
            $stmt->close();
            break;

        // Acción: Editar entrada existente
        case 'editar':
            $id = (int) $_POST['id']; // ID de entrada a editar
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_formulario = trim($_POST['enlace_formulario']);
            $enlace_video = trim($_POST['enlace_video']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';

            // REEMPLAZO DE IMAGEN - Si se sube nueva, borrar anterior
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'ampa_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                        
                        // Eliminar imagen anterior del servidor
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) {
                            unlink($_POST['imagen_existente']);
                        }
                    }
                }
            }

            // Actualizar entrada con nueva información + auditoría
            $stmt = $conexion->prepare("UPDATE ampa SET titulo=?, texto=?, imagen=?, enlace_formulario=?, enlace_video=?, fecha_actualizacion=NOW(), ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? WHERE id=?");
            $stmt->bind_param("sssssii", $titulo, $texto, $imagen, $enlace_formulario, $enlace_video, $_SESSION['usuario_id'], $id);
            
            if ($stmt->execute()) {
                $mensaje = 'Entrada AMPA actualizada correctamente';
            }
            $stmt->close();
            break;
    }
}

// CARGAR LISTADO COMPLETO DE ENTRADAS PARA MOSTRAR
// Consulta con JOIN para mostrar nombre del último editor
$stmt = $conexion->prepare("
    SELECT a.*, u.nombre AS ultima_edicion_usuario_nombre
    FROM ampa a
    LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id
    ORDER BY a.fecha_actualizacion DESC
");
$stmt->execute();
$resultado = $stmt->get_result();

// Array que almacena todas las entradas para el listado
$entradas = [];
while ($fila = $resultado->fetch_assoc()) {
    $entradas[] = $fila;
}
$stmt->close();

// DETECTAR MODO EDICIÓN - Carga datos de entrada específica para editar
$modo_edit = false; // Flag que indica si estamos editando
$entrada_edit = null; // Datos de la entrada que se edita

// Si viene parámetro 'editar' en URL, cargar datos para formulario
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar']; // Sanitizar ID
    
    $stmt = $conexion->prepare("SELECT * FROM ampa WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $entrada_edit = $result->fetch_assoc(); // Cargar datos entrada
    
    // Si existe la entrada, activar modo edición
    $modo_edit = $entrada_edit !== null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración básica del documento HTML5 -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión AMPA - Dashboard Admin</title>
    
    <!-- Recursos externos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <!-- Contenedor principal de todo el dashboard -->
    <div class="dashboard_ampa_container">
        
        <?php include 'dashboard_head.php'; // Header reutilizable con datos usuario ?>

        <!-- RESTRICCION DE ACCESO - Solo administradores -->
        <?php if (!$is_admin): ?>
            <div class="dashboard_ampa_no_admin">
                <i class="fas fa-lock dashboard_ampa_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar el contenido AMPA</h2>
            </div>
        <?php else: ?>

            <!-- MOSTRAR MENSAJE DE ÉXITO si existe -->
            <?php if ($mensaje): ?>
                <div class="dashboard_ampa_alert dashboard_ampa_alert_success">
                    <?php echo htmlspecialchars($mensaje); // Escapa HTML por seguridad ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO PRINCIPAL - Nueva entrada o Editar existente -->
            <div class="dashboard_ampa_seccion_form <?php echo $modo_edit ? 'dashboard_ampa_modo_edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Entrada (ID: <?php echo $entrada_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Entrada AMPA
                    <?php endif; ?>
                </h2>
                
                <!-- Formulario con soporte para subida de archivos -->
                <form method="POST" class="dashboard_ampa_form_grid" enctype="multipart/form-data">
                    
                    <!-- Campos ocultos que determinan la acción a ejecutar -->
                    <?php if ($modo_edit): ?>
                        <!-- Modo edición: campos necesarios para UPDATE -->
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $entrada_edit['id']; ?>">
                        <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($entrada_edit['imagen']); ?>">
                    <?php else: ?>
                        <!-- Modo nueva: solo indica acción INSERT -->
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <!-- Campo Título - Requerido -->
                    <div class="dashboard_ampa_form_group">
                        <label class="dashboard_ampa_form_label">Título *</label>
                        <input type="text" name="titulo" class="dashboard_ampa_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: AMPA 2026 - Actividades Escolares">
                    </div>

                    <!-- Campo Enlace Formulario - Opcional -->
                    <div class="dashboard_ampa_form_group">
                        <label class="dashboard_ampa_form_label">Enlace Formulario (opcional)</label>
                        <input type="url" name="enlace_formulario" class="dashboard_ampa_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['enlace_formulario'] : ($_POST['enlace_formulario'] ?? '')); ?>"
                               placeholder="https://docs.google.com/forms/...">
                    </div>

                    <!-- Campo Enlace Video - Opcional -->
                    <div class="dashboard_ampa_form_group">
                        <label class="dashboard_ampa_form_label">Enlace Video (opcional)</label>
                        <input type="url" name="enlace_video" class="dashboard_ampa_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['enlace_video'] : ($_POST['enlace_video'] ?? '')); ?>"
                               placeholder="https://youtube.com/...">
                    </div>

                    <!-- Gestión de Imagen -->
                    <div class="dashboard_ampa_form_group">
                        <?php if ($modo_edit && $entrada_edit['imagen']): ?>
                            <!-- Mostrar imagen actual en modo edición -->
                            <label class="dashboard_ampa_form_label">Imagen actual:</label>
                            <div class="dashboard_ampa_imagen_actual">
                                <img src="<?php echo htmlspecialchars($entrada_edit['imagen']); ?>" alt="Imagen actual" class="dashboard_ampa_img_preview">
                                <p class="dashboard_ampa_img_nombre"><?php echo htmlspecialchars(basename($entrada_edit['imagen'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Input para nueva imagen -->
                        <label class="dashboard_ampa_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label>
                        <input type="file" name="imagen" class="dashboard_ampa_form_input" accept="image/*">
                        <small class="dashboard_ampa_form_hint">Máx 5MB. Deja vacío para mantener la actual</small>
                    </div>

                    <!-- Campo Texto - Requerido (ocupa toda la anchura) -->
                    <div class="dashboard_ampa_form_group dashboard_ampa_form_group_text">
                        <label class="dashboard_ampa_form_label">Texto *</label>
                        <textarea name="texto" class="dashboard_ampa_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $entrada_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                    </div>

                    <!-- Botón de envío del formulario -->
                    <div class="dashboard_ampa_btn_group">
                        <button type="submit" class="dashboard_ampa_btn dashboard_ampa_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Entrada
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTADO COMPLETO DE ENTRADAS EXISTENTES -->
            <!-- Grid responsive con tarjetas individuales -->
            <div class="dashboard_ampa_seccion_lista">
                <!-- Contador dinámico de entradas -->
                <h2><i class="fas fa-list"></i> Entradas AMPA (<?php echo count($entradas); ?>)</h2>
                
                <?php if (!empty($entradas)): ?>
                    <!-- Grid de tarjetas de entradas -->
                    <div class="dashboard_ampa_entradas_grid">
                        <?php foreach ($entradas as $entrada): ?>
                            <!-- Tarjeta individual de cada entrada -->
                            <div class="dashboard_ampa_entrada_card <?php echo $entrada['activo'] ? 'dashboard_ampa_activa' : ''; ?>">
                                
                                <!-- Imagen preview si existe -->
                                <?php if ($entrada['imagen']): ?>
                                    <div class="dashboard_ampa_entrada_imagen">
                                        <img src="<?php echo htmlspecialchars($entrada['imagen']); ?>" alt="<?php echo htmlspecialchars($entrada['titulo']); ?>">
                                    </div>
                                <?php endif; ?>

                                <!-- Título de la entrada -->
                                <h3 class="dashboard_ampa_entrada_titulo"><?php echo htmlspecialchars($entrada['titulo']); ?></h3>

                                <!-- Fecha de actualización + nombre del editor -->
                                <div class="dashboard_ampa_entrada_fecha">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($entrada['fecha_actualizacion'])); ?>
                                    <?php if (!empty($entrada['ultima_edicion_usuario_nombre'])): ?>
                                        <br>
                                        <small class="dashboard_ampa_fecha_editor"><?php echo htmlspecialchars($entrada['ultima_edicion_usuario_nombre']); ?></small>
                                    <?php endif; ?>
                                </div>

                                <!-- Preview del texto (primeros 150 caracteres) -->
                                <div class="dashboard_ampa_entrada_texto">
                                    <?php echo htmlspecialchars(substr($entrada['texto'], 0, 150)); ?>...
                                </div>

                                <!-- Enlaces externos asociados a la entrada -->
                                <div class="dashboard_ampa_entrada_enlaces">
                                    <?php if ($entrada['enlace_formulario']): ?>
                                        <a href="<?php echo htmlspecialchars($entrada['enlace_formulario']); ?>" class="dashboard_ampa_enlace_formulario" target="_blank">
                                            <i class="fas fa-file-alt"></i> Formulario
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($entrada['enlace_video']): ?>
                                        <a href="<?php echo htmlspecialchars($entrada['enlace_video']); ?>" class="dashboard_ampa_enlace_video" target="_blank">
                                            <i class="fas fa-video"></i> Video
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- BOTONES DE ACCIÓN - Cada entrada tiene 3 opciones -->
                                <div class="dashboard_ampa_acciones_botones">
                                    
                                    <!-- Botón Activar/Desactivar (solo una entrada activa) -->
                                    <form method="POST" class="dashboard_ampa_activar_form" onsubmit="return confirm('¿Seleccionar esta entrada como activa? Se desactivarán las demás.')">
                                        <input type="hidden" name="accion" value="activar">
                                        <input type="hidden" name="id" value="<?php echo $entrada['id']; ?>">
                                        <button type="submit" class="dashboard_ampa_btn_small dashboard_ampa_btn_activar <?php echo $entrada['activo'] ? 'dashboard_ampa_activo' : ''; ?>">
                                            <i class="<?php echo $entrada['activo'] ? 'fas' : 'far'; ?> fa-star"></i> 
                                            <?php echo $entrada['activo'] ? 'Activa' : 'Elegir'; ?>
                                        </button>
                                    </form>
                                    
                                    <!-- Botón Editar - Cambia a modo formulario con datos precargados -->
                                    <a href="?editar=<?php echo $entrada['id']; ?>" class="dashboard_ampa_btn_small dashboard_ampa_btn_editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    
                                    <!-- Botón Eliminar con confirmación JavaScript -->
                                    <form method="POST" class="dashboard_ampa_eliminar_form" onsubmit="return confirm('¿Eliminar esta entrada AMPA?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $entrada['id']; ?>">
                                        <button type="submit" class="dashboard_ampa_btn_small dashboard_ampa_btn_delete">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Estado vacío - No hay entradas creadas -->
                    <div class="dashboard_ampa_vacio">
                        <i class="fas fa-users"></i>
                        <h3>No hay entradas AMPA</h3>
                        <p>Añade la primera entrada con el formulario de arriba</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; // Fin restricción admin ?>

        <!-- Botón universal para volver al dashboard principal -->
        <form method="POST" action="dashboard_nuestro_centro.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </form>
    </div>
</body>
</html>
