<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int)$_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM documentos_institucionales WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) $mensaje = 'Documento eliminado correctamente';
            $stmt->close();
            break;
            
        case 'nueva':
            $titulo = trim($_POST['titulo']);
            $descripcion = trim($_POST['descripcion']);
            $fecha = $_POST['fecha_publicacion'];
            $tipo = trim($_POST['tipo_archivo']);
            $enlace = trim($_POST['url']);
            $orden = (int)$_POST['orden'];
            $stmt = $conexion->prepare("INSERT INTO documentos_institucionales (titulo, descripcion, fecha_publicacion, tipo_archivo, url, orden, activo) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssssi", $titulo, $descripcion, $fecha, $tipo, $enlace, $orden);
            if ($stmt->execute()) $mensaje = 'Documento añadido correctamente';
            $stmt->close();
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $titulo = trim($_POST['titulo']);
            $descripcion = trim($_POST['descripcion']);
            $fecha = $_POST['fecha_publicacion'];
            $tipo = trim($_POST['tipo_archivo']);
            $enlace = trim($_POST['url']);
            $orden = (int)$_POST['orden'];
            $stmt = $conexion->prepare("UPDATE documentos_institucionales SET titulo=?, descripcion=?, fecha_publicacion=?, tipo_archivo=?, url=?, orden=? WHERE id=?");
            $stmt->bind_param("ssssi i", $titulo, $descripcion, $fecha, $tipo, $enlace, $orden, $id);
            if ($stmt->execute()) $mensaje = 'Documento actualizado correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR DOCUMENTOS
$stmt = $conexion->prepare("SELECT * FROM documentos_institucionales WHERE activo = 1 ORDER BY orden ASC, fecha_publicacion DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$documentos = [];
while ($fila = $resultado->fetch_assoc()) {
    $documentos[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$documento_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM documentos_institucionales WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $documento_edit = $result->fetch_assoc();
    $modo_edit = $documento_edit !== null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Documentos Institucionales - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --morado: #8B5CF6; --morado-oscuro: #7C3AED; --morado-claro: #C4B5FD;
            --blanco: #FFFFFF; --gris: #6B7280; --gris-oscuro: #1F2937; --naranja: #F59E0B;
            --verde: #10B981;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: linear-gradient(135deg, #F8FAFC, #EDE9FE); min-height: 100vh; padding: 2rem; }
        .container { max-width: 1400px; margin: 0 auto; }
        
        .header-actions { position: absolute; top: 2.5rem; left: 2rem; z-index: 1000; }
        .btn-volver { background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(139,92,246,0.3); }
        .btn-volver:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(139,92,246,0.4); }
        
        .header { background: var(--blanco); padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(139,92,246,0.1); margin-bottom: 2rem; text-align: center; border: 1px solid var(--morado-claro); position: relative; }
        .saludo { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap; }
        .info-usuario { background: var(--morado-claro); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 1.1rem; font-weight: 600; }
        
        .seccion-form { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); margin-bottom: 2rem; border-top: 5px solid var(--naranja); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-label { font-weight: 600; color: var(--gris-oscuro); }
        .form-input, .form-textarea, .form-select { padding: 1rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; transition: all 0.3s; background: #f9fafb; }
        .form-input:focus, .form-textarea:focus, .form-select:focus { outline: none; border-color: var(--naranja); box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
        .btn-group { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn { padding: 1rem 2rem; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: linear-gradient(135deg, var(--naranja), #f97316); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(245,158,11,0.3); }
        .btn-secondary { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); color: white; }
        
        .documentos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; }
        .documento-card { background: var(--blanco); border-radius: 15px; padding: 2rem; box-shadow: 0 8px 25px rgba(139,92,246,0.08); border-left: 5px solid var(--naranja); }
        .documento-titulo { font-size: 1.3rem; font-weight: 700; color: var(--gris-oscuro); margin-bottom: 0.5rem; }
        .documento-fecha { color: var(--gris); font-size: 0.9rem; margin-bottom: 1rem; }
        .documento-descripcion { color: var(--gris-oscuro); margin-bottom: 1rem; line-height: 1.6; }
        .documento-enlace { color: var(--morado); text-decoration: none; font-weight: 600; }
        .documento-enlace:hover { text-decoration: underline; }
        .documento-tipo { font-size: 0.8rem; text-transform: uppercase; font-weight: 600; color: var(--naranja); margin-bottom: 0.5rem; }
        .documento-orden { font-size: 0.8rem; color: var(--gris); margin-bottom: 0.5rem; }

        .acciones-botones { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }
        .btn-small { padding: 0.75rem 1.5rem; font-size: 0.95rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; border: none; cursor: pointer; }
        .btn-editar { background: linear-gradient(135deg, var(--verde), #059669); color: white; }
        .btn-editar:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16,185,129,0.4); }
        .btn-delete { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .btn-delete:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(239,68,68,0.4); }

        .modo-edit { background: linear-gradient(135deg, #fef3c7, #fde68a) !important; border-top-color: var(--verde) !important; }
        .form-textarea { min-height: 100px; resize: vertical; font-family: inherit; }
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; font-weight: 600; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .no-admin { text-align: center; padding: 4rem; background: var(--blanco); border-radius: 20px; color: var(--gris); margin: 2rem 0; border: 1px solid var(--morado-claro); }
        .btn-logout { background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; border: none; padding: 1.2rem 2.5rem; border-radius: 15px; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: block; margin: 3rem auto 0; transition: all 0.3s ease; }
        .btn-logout:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.4); }
        
        @media (max-width: 768px) { 
            .header-actions { left: 1rem; top: 1rem; }
            .form-grid, .documentos-grid { grid-template-columns: 1fr; } 
            .btn-group, .acciones-botones { flex-direction: column; align-items: center; }
            .btn-small { width: 100%; max-width: 250px; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-actions">
                <a href="dashboard.php" class="btn-volver" title="Volver al Dashboard">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <h1 class="saludo">
                <i class="fas fa-file-pdf"></i>
                Gestión Documentos Institucionales
                <span class="info-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> (<?php echo ucfirst($_SESSION['usuario_rol']); ?>)</span>
            </h1>
        </div>

        <?php if (!$is_admin): ?>
            <div class="no-admin">
                <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <!-- FORMULARIO NUEVA / EDITAR -->
            <div class="seccion-form <?php echo $modo_edit ? 'modo-edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Documento (ID: <?php echo $documento_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nuevo Documento Institucional
                    <?php endif; ?>
                </h2>
                <form method="POST" class="form-grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $documento_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Título <span style="color: var(--naranja);">*</span></label>
                        <input type="text" name="titulo" class="form-input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $documento_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Proyecto Educativo del Centro 24-25">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo Archivo <span style="color: var(--naranja);">*</span></label>
                        <select name="tipo_archivo" class="form-select" required>
                            <option value="pdf" <?php echo ($modo_edit && $documento_edit['tipo_archivo'] == 'pdf') || ($_POST['tipo_archivo'] ?? '' == 'pdf') ? 'selected' : ''; ?>>PDF</option>
                            <option value="docx" <?php echo ($modo_edit && $documento_edit['tipo_archivo'] == 'docx') || ($_POST['tipo_archivo'] ?? '' == 'docx') ? 'selected' : ''; ?>>DOCX</option>
                            <option value="doc" <?php echo ($modo_edit && $documento_edit['tipo_archivo'] == 'doc') || ($_POST['tipo_archivo'] ?? '' == 'doc') ? 'selected' : ''; ?>>DOC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha Publicación <span style="color: var(--naranja);">*</span></label>
                        <input type="date" name="fecha_publicacion" class="form-input" required 
                               value="<?php echo $modo_edit ? $documento_edit['fecha_publicacion'] : ($_POST['fecha_publicacion'] ?? date('Y-m-d')); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Orden</label>
                        <input type="number" name="orden" class="form-input" min="1" max="100"
                               value="<?php echo $modo_edit ? $documento_edit['orden'] : ($_POST['orden'] ?? ''); ?>" placeholder="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL <span style="color: var(--naranja);">*</span></label>
                        <input type="url" name="url" class="form-input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $documento_edit['url'] : ($_POST['url'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Descripción <span style="color: var(--naranja);">*</span></label>
                        <textarea name="descripcion" class="form-textarea" required><?php echo htmlspecialchars($modo_edit ? $documento_edit['descripcion'] : ($_POST['descripcion'] ?? '')); ?></textarea>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Documento
                        </button>
                        <a href="dashboard_doc_institucionales.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <!-- LISTA DE DOCUMENTOS -->
            <div class="seccion-form">
                <h2><i class="fas fa-list"></i> Documentos Publicados (<?php echo count($documentos); ?>)</h2>
                <?php if (!empty($documentos)): ?>
                    <div class="documentos-grid">
                        <?php foreach ($documentos as $documento): ?>
                            <div class="documento-card">
                                <div class="documento-tipo"><?php echo strtoupper(htmlspecialchars($documento['tipo_archivo'])); ?></div>
                                <div class="documento-orden">Orden: <?php echo $documento['orden']; ?></div>
                                <h3 class="documento-titulo"><?php echo htmlspecialchars($documento['titulo']); ?></h3>
                                <div class="documento-fecha">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($documento['fecha_publicacion'])); ?>
                                </div>
                                <div class="documento-descripcion"><?php echo htmlspecialchars(substr($documento['descripcion'], 0, 150)); ?>...</div>
                                <a href="<?php echo htmlspecialchars($documento['url']); ?>" class="documento-enlace" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Ver Documento
                                </a>
                                
                                <div class="acciones-botones">
                                    <a href="?editar=<?php echo $documento['id']; ?>" class="btn btn-small btn-editar" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este documento?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $documento['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-delete" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--gris);">
                        <i class="fas fa-file-pdf" style="font-size: 4rem; margin-bottom: 1rem; color: var(--naranja);"></i>
                        <h3>No hay documentos institucionales</h3>
                        <p>Añade el primer documento con el formulario de arriba</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="logout.php" style="text-align: center;">
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </form>
    </div>
</body>
</html>
