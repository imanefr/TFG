<?php
// Inicia una nueva sesión o reanuda la existente para rastrear al usuario
session_start();

// Incluye el archivo que contiene la configuración de la conexión a la Base de Datos ($conexion)
include 'conexion.php';

// Control de acceso: Si no existe el ID de usuario en la sesión, se asume que no está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Redirecciona inmediatamente a la página de login
    header('Location: login.php');
    // Finaliza la ejecución del script para evitar que se procese el resto del archivo
    exit;
}

// Variable que define el título dinámico que se mostrará en el dashboard general
$titulo_dashboard = "Dashboard Organigrama";

// Control de roles: Verifica si el rol del usuario actual tiene permisos administrativos o de gestión
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');

// Captura un mensaje de éxito/error pasado por la URL (método GET), si no existe inicializa vacío
$mensaje = isset($_GET['msj']) ? $_GET['msj'] : '';

// Comprueba si se ha enviado el formulario mediante POST y si contiene un parámetro 'accion'
if ($_POST && isset($_POST['accion'])) {
    
    // Evalúa qué tipo de acción se solicitó desde el cliente
    switch ($_POST['accion']) {
        
        // --- ACCIÓN: ELIMINAR REGISTRO ---
        case 'eliminar':
            // Fuerza la conversión del ID recibido a entero por seguridad (Evita SQL Injection básico)
            $id = (int) $_POST['id'];
            
            // Prepara la consulta SQL de borrado de forma segura
            $stmt = $conexion->prepare("DELETE FROM organigrama WHERE id = ?");
            // Vincula el parámetro entero ('i') a la sentencia armada
            $stmt->bind_param("i", $id);
            
            // Ejecuta la consulta en la base de datos
            if ($stmt->execute()) {
                // Redirecciona al mismo archivo limpiando variables POST y pasando un mensaje por GET
                header("Location: dashboard_organigrama.php?msj=Sección eliminada correctamente");
                exit; // Detiene el script tras la redirección
            }
            // Cierra el statement para liberar memoria
            $stmt->close();
            break;

        // --- ACCIÓN: CREAR NUEVO REGISTRO ---
        case 'nueva':
            // Sanitiza los datos de entrada eliminando espacios en blanco innecesarios en los extremos
            $seccion = trim($_POST['seccion']); 
            $cargo = trim($_POST['cargo']);
            $nombre = trim($_POST['nombre']);

            // Prepara la consulta para insertar los tres campos en la tabla organigrama
            $stmt = $conexion->prepare("INSERT INTO organigrama (seccion, cargo, nombre) VALUES (?, ?, ?)");
            // Vincula las tres variables como cadenas de texto ('sss')
            $stmt->bind_param("sss", $seccion, $cargo, $nombre);
            
            // Ejecuta la inserción
            if ($stmt->execute()) {
                // Redirecciona notificando el éxito de la creación
                header("Location: dashboard_organigrama.php?msj=Entrada añadida correctamente");
                exit;
            }
            $stmt->close();
            break;

        // --- ACCIÓN: EDITAR REGISTRO EXISTENTE ---
        case 'editar':
            // Captura el ID único del registro a modificar asegurando un valor entero
            $id = (int) $_POST['id'];
            // Sanitiza las cadenas de texto del formulario
            $seccion = trim($_POST['seccion']);
            $cargo = trim($_POST['cargo']);
            $nombre = trim($_POST['nombre']);

            // Prepara la sentencia de actualización filtrando por el ID
            $stmt = $conexion->prepare("UPDATE organigrama SET seccion=?, cargo=?, nombre=? WHERE id=?");
            // Vincula los tipos de datos: string, string, string, integer ('sssi')
            $stmt->bind_param("sssi", $seccion, $cargo, $nombre, $id);
            
            // Ejecuta la actualización de datos
            if ($stmt->execute()) {
                // Redirecciona notificando que se guardaron los cambios correctamente
                header("Location: dashboard_organigrama.php?msj=Entrada actualizada correctamente");
                exit;
            }
            $stmt->close();
            break;
    }
}

// Prepara la consulta para extraer todo el organigrama ordenado secuencialmente por ID
$stmt = $conexion->prepare("SELECT * FROM organigrama ORDER BY id DESC");
$stmt->execute();
// Obtiene el set de resultados devuelto por MySQL
$resultado = $stmt->get_result();
$noticias = []; // Inicializa un array vacío para volcar los registros

// Recorre cada fila del resultado como un array asociativo y lo añade a la colección '$noticias'
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila;
}
$stmt->close();

// Inicializa las variables de control visual para saber si el formulario debe actuar como "Nuevo" o "Editar"
$modo_edit = false;
$noticia_edit = null;

// Si se detecta el parámetro 'editar' en la URL, significa que el usuario pulsó el botón "Editar" de alguna fila
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar']; // Fuerza conversión a entero
    
    // Busca exclusivamente los datos de ese registro en particular
    $stmt = $conexion->prepare("SELECT * FROM organigrama WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    
    // Obtiene los datos del registro coincidente
    $noticia_edit = $stmt->get_result()->fetch_assoc();
    
    // Cambia el estado del formulario a TRUE si el registro realmente existe en la BD
    $modo_edit = $noticia_edit !== null;
    $stmt->close();
}
?>
        <?php include 'dashboard_head.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Organigrama - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_erasmus_container">

        <?php if (!$is_admin): ?>
            <div class="dashboard_erasmus_no_admin">
                <i class="fas fa-lock"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>
            <?php if ($mensaje): ?>
                <div class="dashboard_erasmus_alert dashboard_erasmus_alert_success">
                    <?php echo htmlspecialchars($mensaje); // htmlspecialchars previene ataques XSS al imprimir variables de URL ?>
                </div>
            <?php endif; ?>

            <div class="dashboard_erasmus_seccion_form <?php echo $modo_edit ? 'dashboard_erasmus_modo_edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Entrada (ID: <?php echo $noticia_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Entrada
                    <?php endif; ?>
                </h2>
                
                <form method="POST" class="dashboard_erasmus_form_grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $noticia_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Sección *</label>
                        <input type="text" name="seccion" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['seccion'] : ''); ?>">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Cargo *</label>
                        <input type="text" name="cargo" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['cargo'] : ''); ?>">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Nombre *</label>
                        <input type="text" name="nombre" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['nombre'] : ''); ?>">
                    </div>

                    <div class="dashboard_erasmus_btn_group">
                        <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Entrada
                        </button>
                        <?php if($modo_edit): ?>
                            <a href="dashboard_organigrama.php" class="dashboard_erasmus_btn" style="background:#888; text-decoration:none;">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="dashboard_erasmus_seccion_lista">
                <h2><i class="fas fa-list"></i> Lista de Entradas (<?php echo count($noticias); ?>)</h2>
                
                <?php if (!empty($noticias)): ?>
                    <div class="dashboard_organigrama_lista">
                        <?php foreach ($noticias as $noticia): ?>
                            <div class="dashboard_organigrama_seccion_card" style="border-bottom: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                                <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['seccion']); ?></h3>
                                <p><strong>Cargo:</strong> <?php echo htmlspecialchars($noticia['cargo']); ?></p>
                                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($noticia['nombre']); ?></p>
                                
                                <div class="dashboard_erasmus_acciones_botones">
                                    <a href="?editar=<?php echo $noticia['id']; ?>" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta entrada?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                        <button type="submit" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_delete">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard_erasmus_vacio">
                        <h3>No hay datos en el organigrama.</h3>
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