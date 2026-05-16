<?php
session_start(); // Inicia la sesión para gestionar el acceso del usuario autenticado
include 'conexion.php'; // Incluye el archivo de conexión a la base de datos MySQL
// Verifica si el usuario ha iniciado sesión correctamente, redirige si no
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirige a página de login si no hay sesión
    exit; // Termina la ejecución del script inmediatamente
}

// Variables de configuración de la página y verificación de roles de administrador
$titulo_dashboard = "Dashboard AMPA"; // Título principal de la página dashboard
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro'); // Determina si el usuario tiene permisos de admin
$mensaje = ''; // Variable para almacenar mensajes de éxito o error
$nombre_profesor = $_SESSION['usuario_nombre'] ?? '';

// Si no existe el nombre en sesión, lo busca en la tabla profesores
if ($nombre_profesor === '') {
    $stmt_profesor = $conexion->prepare("SELECT nombre FROM profesores WHERE usuario_id = ? LIMIT 1");
    $stmt_profesor->bind_param("i", $_SESSION['usuario_id']);
    $stmt_profesor->execute();
    $res_profesor = $stmt_profesor->get_result();
    if ($fila_profesor = $res_profesor->fetch_assoc()) {
        $nombre_profesor = $fila_profesor['nombre'];
    }
    $stmt_profesor->close();
}

// Inicia el procesamiento de acciones enviadas por método POST
if ($_POST && isset($_POST['accion'])) { // Verifica si hay datos POST y acción especificada
    switch ($_POST['accion']) { // Switch para manejar diferentes acciones del formulario
// Acción para borrar una entrada específica de la base de datos AMPA
        case 'eliminar':
            $id = (int) $_POST['id']; // Convierte el ID a entero para evitar inyecciones SQL
            $stmt = $conexion->prepare("DELETE FROM ampa WHERE id = ?"); // Prepara consulta DELETE parametrizada
            $stmt->bind_param("i", $id); // Vincula el parámetro ID como entero
            if ($stmt->execute()) { // Ejecuta la consulta y verifica éxito
                header("Location: dashboard_ampa.php?msg=eliminado"); // Redirige con mensaje de éxito
                exit; // Termina la ejecución tras redirección
            }
            $stmt->close(); // Cierra la declaración preparada
            break;

// Acción para marcar una entrada como activa y desactivar todas las demás
        case 'activar':
            $stmt = $conexion->prepare("UPDATE ampa SET activo = 0"); // Desactiva todas las entradas AMPA primero
            $stmt->execute(); // Ejecuta la desactivación masiva
            $stmt->close(); // Cierra la primera declaración

            $id = (int) $_POST['id']; // Convierte el ID recibido a entero
            $stmt = $conexion->prepare("UPDATE ampa SET activo = 1, ultima_edicion_fecha=NOW(), ultima_edicion_nombre=? WHERE id = ?"); // Prepara activación de entrada específica
            $stmt->bind_param("si", $nombre_profesor, $id); // Vincula nombre del profesor e ID entrada
            if ($stmt->execute()) { // Verifica si la activación fue exitosa
                header("Location: dashboard_ampa.php?msg=activado"); // Redirige con mensaje de éxito
                exit; // Termina ejecución tras redirección
            }
            $stmt->close(); // Cierra la declaración de activación
            break;

// Acción para crear una nueva entrada AMPA en la base de datos
        case 'nueva':
            $titulo = trim($_POST['titulo']); // Limpia espacios del título recibido
            $texto = trim($_POST['texto']); // Limpia espacios del texto recibido
            $enlace_formulario = trim($_POST['enlace_formulario']); // Limpia espacios del enlace de formulario
            $enlace_video = trim($_POST['enlace_video']); // Limpia espacios del enlace de video
            $imagen = ''; // Variable para almacenar ruta de imagen subida
// Procesa la subida del archivo de imagen si se proporcionó correctamente
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) { // Verifica archivo válido sin errores
                $upload_dir = 'img/'; // Directorio destino para imágenes
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true); // Crea directorio si no existe
                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION)); // Obtiene extensión en minúsculas
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) { // Valida extensiones permitidas
                    $new_filename = 'ampa_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension; // Genera nombre único
                    $upload_path = $upload_dir . $new_filename; // Ruta completa del archivo destino
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) { // Mueve archivo temporal a destino
                        $imagen = $upload_path; // Almacena ruta si subida exitosa
                    }
                }
            }

// Inserta la nueva entrada AMPA en la base de datos
            $stmt = $conexion->prepare("INSERT INTO ampa (titulo, texto, imagen, enlace_formulario, enlace_video, fecha_actualizacion, ultima_edicion_fecha, ultima_edicion_nombre, activo) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, 0)"); // Prepara INSERT completo
            $stmt->bind_param("ssssss", $titulo, $texto, $imagen, $enlace_formulario, $enlace_video, $nombre_profesor); // Vincula todos los parámetros

            if ($stmt->execute()) { // Verifica inserción exitosa
                header("Location: dashboard_ampa.php?msg=creado"); // Redirige con mensaje de creación exitosa
                exit; // Termina ejecución tras redirección
            }
            $stmt->close(); // Cierra declaración de inserción
            break;

// Acción para actualizar una entrada AMPA existente
        case 'editar':
            $id = (int) $_POST['id']; // Convierte ID a entero para seguridad
            $titulo = trim($_POST['titulo']); // Limpia título recibido
            $texto = trim($_POST['texto']); // Limpia texto recibido
            $enlace_formulario = trim($_POST['enlace_formulario']); // Limpia enlace formulario
            $enlace_video = trim($_POST['enlace_video']); // Limpia enlace video
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : ''; // Mantiene imagen existente si no se sube nueva
// Procesa nueva imagen subida y elimina la anterior si existe
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) { // Verifica archivo válido
                $upload_dir = 'img/'; // Directorio de subida
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true); // Crea directorio si no existe
                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION)); // Extensión del nuevo archivo
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) { // Valida formato permitido
                    $new_filename = 'ampa_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension; // Nombre único para nueva imagen
                    $upload_path = $upload_dir . $new_filename; // Ruta destino completa
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) { // Sube nueva imagen
                        $imagen = $upload_path; // Actualiza ruta de imagen
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) { // Si hay imagen anterior
                            unlink($_POST['imagen_existente']); // Elimina imagen antigua del servidor
                        }
                    }
                }
            }

// Actualiza la entrada en la base de datos con nuevos datos
            $stmt = $conexion->prepare("UPDATE ampa SET titulo=?, texto=?, imagen=?, enlace_formulario=?, enlace_video=?, fecha_actualizacion=NOW(), ultima_edicion_fecha=NOW(), ultima_edicion_nombre=? WHERE id=?"); // Prepara UPDATE completo
            $stmt->bind_param("ssssssi", $titulo, $texto, $imagen, $enlace_formulario, $enlace_video, $nombre_profesor, $id); // Vincula parámetros en orden

            if ($stmt->execute()) { // Verifica actualización exitosa
                header("Location: dashboard_ampa.php?msg=editado"); // Redirige con mensaje de edición exitosa
                exit; // Termina ejecución tras redirección
            }
            $stmt->close(); // Cierra declaración de actualización
            break;
    } // Fin del switch de acciones
} // Fin del procesamiento POST
// Configura el mensaje de éxito según parámetro recibido por GET en URL
if (isset($_GET['msg'])) { // Verifica si hay mensaje en parámetros GET
    switch ($_GET['msg']) { // Switch para diferentes tipos de mensaje
        case 'creado': $mensaje = 'Entrada AMPA añadida correctamente';
            break; // Mensaje creación exitosa
        case 'editado': $mensaje = 'Entrada AMPA actualizada correctamente';
            break; // Mensaje edición exitosa
        case 'eliminado': $mensaje = 'Entrada AMPA eliminada correctamente';
            break; // Mensaje eliminación exitosa
        case 'activado': $mensaje = 'Entrada AMPA activada correctamente';
            break; // Mensaje activación exitosa
    }
}

// Obtiene todas las entradas AMPA ordenadas por fecha de actualización descendente
$stmt = $conexion->prepare("SELECT * FROM ampa ORDER BY fecha_actualizacion DESC"); // Consulta completa
$stmt->execute(); // Ejecuta consulta de listado
$resultado = $stmt->get_result(); // Obtiene resultados de la consulta
$entradas = []; // Array para almacenar todas las entradas
while ($fila = $resultado->fetch_assoc()) {
    $entradas[] = $fila;
} // Llena array con todas las filas
$stmt->close(); // Cierra declaración del listado
// Verifica si hay ID de edición en GET para cargar modo edición
$modo_edit = false; // Variable booleana para controlar modo edición
$entrada_edit = null; // Variable para datos de entrada en edición
if (isset($_GET['editar'])) { // Si se recibe parámetro 'editar' en GET
    $id_edit = (int) $_GET['editar']; // Convierte ID a entero
    $stmt = $conexion->prepare("SELECT * FROM ampa WHERE id = ?"); // Prepara consulta para entrada específica
    $stmt->bind_param("i", $id_edit); // Vincula ID de edición
    $stmt->execute(); // Ejecuta consulta
    $entrada_edit = $stmt->get_result()->fetch_assoc(); // Obtiene datos de la entrada
    $modo_edit = $entrada_edit !== null; // Activa modo edición si entrada existe
    $stmt->close(); // Cierra declaración de edición
}
?>
            <?php include 'dashboard_head.php'; ?> <!-- Incluye header común del dashboard -->

<!DOCTYPE html> <!-- Declara tipo de documento HTML5 -->
<html lang="es"> <!-- Define idioma español para accesibilidad -->
    <head> <!-- Sección de metadatos del documento -->
        <meta charset="UTF-8"> <!-- Codificación UTF-8 para caracteres especiales -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuración responsive para móviles -->
        <title>Gestión AMPA - Dashboard Admin</title> <!-- Título de la pestaña del navegador -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Iconos Font Awesome desde CDN -->
        <link rel="stylesheet" href="style_dashboard.css"> <!-- Hoja de estilos personalizada -->
    </head>
    <body> <!-- Inicio del cuerpo de la página -->
        <div class="dashboard_ampa_container"> <!-- Contenedor principal del dashboard AMPA -->

            <?php if (!$is_admin): ?> <!-- Verifica permisos de administrador -->
                <div class="dashboard_ampa_no_admin"> <!-- Mensaje de acceso denegado -->
                    <i class="fas fa-lock dashboard_ampa_no_admin_icono"></i> <!-- Icono de candado -->
                    <h2>Solo administradores pueden gestionar el contenido AMPA</h2> <!-- Mensaje de restricción -->
                </div> <!-- Fin contenedor acceso denegado -->
            <?php else: ?> <!-- Si usuario es admin, muestra contenido completo -->

                <?php if ($mensaje): ?> <!-- Muestra mensaje de éxito si existe -->
                    <div class="dashboard_ampa_alert dashboard_ampa_alert_success"> <!-- Alerta de éxito -->
                        <?php echo htmlspecialchars($mensaje); ?> <!-- Mensaje escapado para seguridad XSS -->
                    </div> <!-- Fin alerta de éxito -->
                <?php endif; ?>

                <div class="dashboard_ampa_seccion_form <?php echo $modo_edit ? 'dashboard_ampa_modo_edit' : ''; ?>"> <!-- Sección formulario con clase condicional -->
                    <h2> <!-- Título de la sección formulario -->
                        <?php if ($modo_edit): ?> <!-- Título condicional según modo -->
                            <i class="fas fa-edit"></i> Editar Entrada (ID: <?php echo $entrada_edit['id']; ?>) <!-- Título modo edición -->
                        <?php else: ?>
                            <i class="fas fa-plus"></i> Nueva Entrada AMPA <!-- Título modo creación -->
                        <?php endif; ?>
                    </h2> <!-- Fin título formulario -->

                    <form method="POST" class="dashboard_ampa_form_grid" enctype="multipart/form-data"> <!-- Formulario principal con soporte archivos -->
                        <?php if ($modo_edit): ?> <!-- Campos ocultos para modo edición -->
                            <input type="hidden" name="accion" value="editar"> <!-- Acción editar -->
                            <input type="hidden" name="id" value="<?php echo $entrada_edit['id']; ?>"> <!-- ID entrada a editar -->
                            <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($entrada_edit['imagen']); ?>"> <!-- Ruta imagen actual -->
                        <?php else: ?>
                            <input type="hidden" name="accion" value="nueva"> <!-- Acción nueva entrada -->
                        <?php endif; ?>

                        <div class="dashboard_ampa_form_group"> <!-- Grupo formulario título -->
                            <label class="dashboard_ampa_form_label">Título *</label> <!-- Etiqueta título requerido -->
                            <input type="text" name="titulo" class="dashboard_ampa_form_input" required value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['titulo'] : ''); ?>" placeholder="Ej: AMPA 2026 - Actividades Escolares"> <!-- Input título con validación HTML5 -->
                        </div> <!-- Fin grupo título -->

                        <div class="dashboard_ampa_form_group"> <!-- Grupo formulario enlace formulario -->
                            <label class="dashboard_ampa_form_label">Enlace Formulario (opcional)</label> <!-- Etiqueta enlace opcional -->
                            <input type="url" name="enlace_formulario" class="dashboard_ampa_form_input" value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['enlace_formulario'] : ''); ?>" placeholder="https://docs.google.com/forms/..."> <!-- Input URL con validación -->
                        </div> <!-- Fin grupo enlace formulario -->

                        <div class="dashboard_ampa_form_group"> <!-- Grupo formulario enlace video -->
                            <label class="dashboard_ampa_form_label">Enlace Video (opcional)</label> <!-- Etiqueta video opcional -->
                            <input type="url" name="enlace_video" class="dashboard_ampa_form_input" value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['enlace_video'] : ''); ?>" placeholder="https://youtube.com/..."> <!-- Input URL video -->
                        </div> <!-- Fin grupo enlace video -->

                        <div class="dashboard_ampa_form_group"> <!-- Grupo formulario imagen -->
                            <?php if ($modo_edit && $entrada_edit['imagen']): ?> <!-- Muestra imagen actual en modo edición -->
                                <label class="dashboard_ampa_form_label">Imagen actual:</label> <!-- Etiqueta imagen actual -->
                                <div class="dashboard_ampa_imagen_actual"> <!-- Contenedor preview imagen -->
                                    <img src="<?php echo htmlspecialchars($entrada_edit['imagen']); ?>" alt="Imagen actual" class="dashboard_ampa_img_preview"> <!-- Preview imagen actual -->
                                    <p class="dashboard_ampa_img_nombre"><?php echo htmlspecialchars(basename($entrada_edit['imagen'])); ?></p> <!-- Nombre archivo -->
                                </div> <!-- Fin preview imagen actual -->
                            <?php endif; ?>
                            <label class="dashboard_ampa_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label> <!-- Etiqueta nueva imagen -->
                            <input type="file" name="imagen" class="dashboard_ampa_form_input" accept="image/*"> <!-- Input archivo imagen -->
                            <small class="dashboard_ampa_form_hint">Máx 5MB. Deja vacío para mantener la actual</small> <!-- Ayuda tamaño y uso -->
                        </div> <!-- Fin grupo imagen -->

                        <div class="dashboard_ampa_form_group dashboard_ampa_form_group_text"> <!-- Grupo textarea texto -->
                            <label class="dashboard_ampa_form_label">Texto *</label> <!-- Etiqueta texto requerido -->
                            <textarea name="texto" class="dashboard_ampa_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $entrada_edit['texto'] : ''); ?></textarea> <!-- Textarea con contenido prellenado -->
                        </div> <!-- Fin grupo texto -->

                        <div class="dashboard_ampa_btn_group"> <!-- Grupo botones acción -->
                            <button type="submit" class="dashboard_ampa_btn dashboard_ampa_btn_primary"> <!-- Botón submit principal -->
                                <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Entrada <!-- Texto dinámico según modo -->
                            </button> <!-- Fin botón submit -->
                        </div> <!-- Fin grupo botones -->
                    </form> <!-- Fin formulario principal -->
                </div> <!-- Fin sección formulario -->

                <div class="dashboard_ampa_seccion_lista"> <!-- Sección listado de entradas -->
                    <h2><i class="fas fa-list"></i> Entradas AMPA (<?php echo count($entradas); ?>)</h2> <!-- Título con contador entradas -->
                    <?php if (!empty($entradas)): ?> <!-- Si existen entradas, muestra grid -->
                        <div class="dashboard_ampa_entradas_grid"> <!-- Grid contenedor tarjetas -->
                            <?php foreach ($entradas as $entrada): ?> <!-- Bucle por todas las entradas -->
                                <div class="dashboard_ampa_entrada_card <?php echo $entrada['activo'] ? 'dashboard_ampa_activa' : ''; ?>"> <!-- Tarjeta entrada con clase activa -->
                                    <?php if ($entrada['imagen']): ?> <!-- Si entrada tiene imagen -->
                                        <div class="dashboard_ampa_entrada_imagen"> <!-- Contenedor imagen entrada -->
                                            <img src="<?php echo htmlspecialchars($entrada['imagen']); ?>" alt="<?php echo htmlspecialchars($entrada['titulo']); ?>"> <!-- Imagen de entrada -->
                                        </div> <!-- Fin contenedor imagen -->
                                    <?php endif; ?>

                                    <h3 class="dashboard_ampa_entrada_titulo"><?php echo htmlspecialchars($entrada['titulo']); ?></h3> <!-- Título entrada escapado -->

                                    <div class="dashboard_ampa_entrada_fecha"> <!-- Sección fechas -->
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($entrada['fecha_actualizacion'])); ?> <!-- Fecha formateada -->
                                        <?php if (!empty($entrada['ultima_edicion_nombre'])): ?> <!-- Si hay editor registrado -->
                                            <br> <!-- Salto línea -->
                                            <small class="dashboard_ampa_fecha_editor">Editado por: <?php echo htmlspecialchars($entrada['ultima_edicion_nombre']); ?></small> <!-- Nombre último editor -->
                                        <?php endif; ?>
                                    </div> <!-- Fin sección fechas -->

                                    <div class="dashboard_ampa_entrada_texto"> <!-- Preview texto entrada -->
                                        <?php echo htmlspecialchars(substr($entrada['texto'], 0, 150)); ?>... <!-- Primeros 150 caracteres -->
                                    </div> <!-- Fin preview texto -->

                                    <div class="dashboard_ampa_entrada_enlaces"> <!-- Contenedor enlaces -->
                                        <?php if ($entrada['enlace_formulario']): ?> <!-- Si existe enlace formulario -->
                                            <a href="<?php echo htmlspecialchars($entrada['enlace_formulario']); ?>" class="dashboard_ampa_enlace_formulario" target="_blank"> <!-- Enlace externo -->
                                                <i class="fas fa-file-alt"></i> Formulario <!-- Icono y texto -->
                                            </a> <!-- Fin enlace formulario -->
                                        <?php endif; ?>
                                        <?php if ($entrada['enlace_video']): ?> <!-- Si existe enlace video -->
                                            <a href="<?php echo htmlspecialchars($entrada['enlace_video']); ?>" class="dashboard_ampa_enlace_video" target="_blank"> <!-- Enlace externo -->
                                                <i class="fas fa-video"></i> Video <!-- Icono y texto -->
                                            </a> <!-- Fin enlace video -->
                                        <?php endif; ?>
                                    </div> <!-- Fin contenedor enlaces -->

                                    <div class="dashboard_ampa_acciones_botones"> <!-- Grupo botones de acción -->
                                        <form method="POST" class="dashboard_ampa_activar_form" onsubmit="return confirm('¿Seleccionar esta entrada como activa? Se desactivarán las demás.')"> <!-- Formulario activar con confirmación JS -->
                                            <input type="hidden" name="accion" value="activar"> <!-- Acción activar oculta -->
                                            <input type="hidden" name="id" value="<?php echo $entrada['id']; ?>"> <!-- ID entrada oculta -->
                                            <button type="submit" class="dashboard_ampa_btn_small dashboard_ampa_btn_activar <?php echo $entrada['activo'] ? 'dashboard_ampa_activo' : ''; ?>"> <!-- Botón activar con estado visual -->
                                                <i class="<?php echo $entrada['activo'] ? 'fas' : 'far'; ?> fa-star"></i> <!-- Icono estrella sólida/vacia -->
                                                <?php echo $entrada['activo'] ? 'Activa' : 'Elegir'; ?> <!-- Texto dinámico -->
                                            </button> <!-- Fin botón activar -->
                                        </form> <!-- Fin formulario activar -->

                                        <a href="?editar=<?php echo $entrada['id']; ?>" class="dashboard_ampa_btn_small dashboard_ampa_btn_editar"> <!-- Enlace directo a edición -->
                                            <i class="fas fa-edit"></i> Editar <!-- Icono y texto editar -->
                                        </a> <!-- Fin enlace editar -->

                                        <form method="POST" class="dashboard_ampa_eliminar_form" onsubmit="return confirm('¿Eliminar esta entrada AMPA?')"> <!-- Formulario eliminar con confirmación -->
                                            <input type="hidden" name="accion" value="eliminar"> <!-- Acción eliminar oculta -->
                                            <input type="hidden" name="id" value="<?php echo $entrada['id']; ?>"> <!-- ID entrada oculta -->
                                            <button type="submit" class="dashboard_ampa_btn_small dashboard_ampa_btn_delete"> <!-- Botón eliminar -->
                                                <i class="fas fa-trash"></i> Eliminar <!-- Icono y texto -->
                                            </button> <!-- Fin botón eliminar -->
                                        </form> <!-- Fin formulario eliminar -->
                                    </div> <!-- Fin grupo acciones -->
                                </div> <!-- Fin tarjeta entrada -->
                            <?php endforeach; ?> <!-- Fin bucle entradas -->
                        </div> <!-- Fin grid entradas -->
                    <?php else: ?> <!-- Estado vacío sin entradas -->
                        <div class="dashboard_ampa_vacio"> <!-- Contenedor mensaje vacío -->
                            <i class="fas fa-users"></i> <!-- Icono usuarios -->
                            <h3>No hay entradas AMPA</h3> <!-- Título vacío -->
                            <p>Añade la primera entrada con el formulario de arriba</p> <!-- Instrucción creación -->
                        </div> <!-- Fin mensaje vacío -->
                    <?php endif; ?> <!-- Fin condicional entradas -->
                </div> <!-- Fin sección listado -->
            <?php endif; ?> <!-- Fin verificación admin -->

            <form method="POST" action="dashboard.php" class="dashboard_universal_volver"> <!-- Formulario botón volver -->
                <button type="submit" class="dashboard_universal_btn_volver"> <!-- Botón regreso dashboard principal -->
                    <i class="fas fa-arrow-left"></i> Volver <!-- Icono y texto volver -->
                </button> <!-- Fin botón volver -->
            </form> <!-- Fin formulario volver -->
        </div> <!-- Fin contenedor principal -->
    </body> <!-- Fin cuerpo documento -->
</html> <!-- Fin documento HTML -->