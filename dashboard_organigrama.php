<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$titulo_dashboard = "Dashboard Organigrama";

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM organigrama WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute())
                $mensaje = 'Sección eliminada correctamente';
            $stmt->close();
            break;

        case 'nueva':
            $seccion = trim($_POST['titulo']);
            $cargo = trim($_POST['texto']);
            $nombre = $_POST['fecha'];

            $stmt = $conexion->prepare("INSERT INTO organigrama (seccion, cargo, nombre) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sss", $seccion, $cargo, $nombre);
            if ($stmt->execute())
                $mensaje = 'Noticia añadida correctamente';
            $stmt->close();
            break;

        case 'editar':
            $id = (int) $_POST['id'];
            $seccion = trim($_POST['titulo']);
            $cargo = trim($_POST['texto']);
            $nombre = $_POST['fecha'];

            // ACTUALIZAR noticia existente
            $stmt = $conexion->prepare("
                UPDATE organigrama 
                SET seccion=?, cargo=?, nombre=?
                WHERE id=?
            ");
            $stmt->bind_param("sssi", $seccion, $cargo, $nombre, $id);
            if ($stmt->execute())
                $mensaje = 'Noticia actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR NOTICIAS CON NOMBRE DEL USUARIO - CONSULTA CORREGIDA ✅
$stmt = $conexion->prepare("
    SELECT n.*
    FROM organigrama n
    ORDER BY n.id ASC
");

$stmt->execute();
$resultado = $stmt->get_result();
$noticias = [];
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$noticia_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM organigrama WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $noticia_edit = $result->fetch_assoc();
    $modo_edit = $noticia_edit !== null;
    $stmt->close();
}
?>

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
        <!-- HEADER -->
        <?php include 'dashboard_head.php'; ?>

        <?php if (!$is_admin): ?>
            <div class="dashboard_erasmus_no_admin">
                <i class="fas fa-lock"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>

            <?php if ($mensaje): ?>
                <div class="dashboard_erasmus_alert dashboard_erasmus_alert_success">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <div class="dashboard_erasmus_seccion_form <?php echo $modo_edit ? 'dashboard_erasmus_modo_edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Entrada (ID: <?php echo $noticia_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Entrada
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_erasmus_form_grid" enctype="multipart/form-data">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $noticia_edit['id']; ?>">
                        <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Sección *</label>
                        <input type="text" name="seccion" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['seccion'] : ($_POST['seccion'] ?? '')); ?>"
                               placeholder="Ej: Listado Admisiones 2025-26">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Cargo *</label>
                        <input type="text" name="cargo" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['cargo'] : ($_POST['cargo'] ?? '')); ?>"
                               placeholder="Ej: Listado Admisiones 2025-26">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Nombre *</label>
                        <input type="text" name="nombre" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['nombre'] : ($_POST['nombre'] ?? '')); ?>"
                               placeholder="Ej: Listado Admisiones 2025-26">
                    </div>

                    <div class="dashboard_erasmus_btn_group">
                        <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Entrada
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTA DE NOTICIAS -->
            <div class="dashboard_erasmus_seccion_lista">
                <h2><i class="fas fa-list"></i> Lista de Entradas (<?php echo count($noticias); ?>)</h2>
                <?php if (!empty($noticias)): ?>
                    <div class="dashboard_organigrama_lista">
                        <?php foreach ($noticias as $noticia): ?>
                            <div class="dashboard_organigrama_seccion_card">
                                <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['seccion']); ?></h3>

                                <div class="dashboard_erasmus_noticia_contenido">
                                    <?php echo htmlspecialchars(substr($noticia['cargo'], 0, 150)); ?>...
                                </div>
                                <div class="dashboard_erasmus_noticia_contenido">
                                    <?php echo htmlspecialchars(substr($noticia['nombre'], 0, 150)); ?>...
                                </div>
                                <div class="dashboard_erasmus_acciones_botones">
                                    <a href="?editar=<?php echo $noticia['id']; ?>" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta noticia?')">
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
                        <i class="fas fa-plane"></i>
                        <h3>No hay ofertas de empleo.</h3>
                        <p>Añade la primera oferta con el formulario de arriba.</p>
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
