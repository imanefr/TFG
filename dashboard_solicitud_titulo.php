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
            $stmt = $conexion->prepare("DELETE FROM solicitudes_titulos WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) $mensaje = 'Solicitud de título eliminada correctamente';
            $stmt->close();
            break;
            
        case 'nueva':
            $tipo = trim($_POST['tipo']);
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_normativa = trim($_POST['enlace_normativa']);
            $enlace_autorizacion = trim($_POST['enlace_autorizacion']);
            $horario = trim($_POST['horario']);
            $stmt = $conexion->prepare("INSERT INTO solicitudes_titulos (tipo, titulo, texto, enlace_normativa, enlace_autorizacion, horario, activo) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssss", $tipo, $titulo, $texto, $enlace_normativa, $enlace_autorizacion, $horario);
            if ($stmt->execute()) $mensaje = 'Solicitud de título añadida correctamente';
            $stmt->close();
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $tipo = trim($_POST['tipo']);
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_normativa = trim($_POST['enlace_normativa']);
            $enlace_autorizacion = trim($_POST['enlace_autorizacion']);
            $horario = trim($_POST['horario']);
            $stmt = $conexion->prepare("UPDATE solicitudes_titulos SET tipo=?, titulo=?, texto=?, enlace_normativa=?, enlace_autorizacion=?, horario=? WHERE id=?");
            $stmt->bind_param("ssssssi", $tipo, $titulo, $texto, $enlace_normativa, $enlace_autorizacion, $horario, $id);
            if ($stmt->execute()) $mensaje = 'Solicitud de título actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR SOLICITUDES DE TÍTULOS
$stmt = $conexion->prepare("SELECT * FROM solicitudes_titulos WHERE activo = 1 ORDER BY fecha_creacion DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$solicitudes = [];
while ($fila = $resultado->fetch_assoc()) {
    $solicitudes[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$solicitud_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM solicitudes_titulos WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $solicitud_edit = $result->fetch_assoc();
    $modo_edit = $solicitud_edit !== null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Solicitudes Títulos - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --morado: #8B5CF6; --morado-oscuro: #7C3AED; --morado-claro: #C4B5FD;
            --blanco: #FFFFFF; --gris: #6B7280; --gris-oscuro: #1F2937; --verde: #10B981;
            --naranja: #F59E0B; --azul: #3B82F6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: linear-gradient(135deg, #F8FAFC, #EDE9FE); min-height: 100vh; padding: 2rem; }
        .container { max-width: 1400px; margin: 0 auto; }
        
        /* BOTÓN VOLVER HEADER */
        .header-actions { 
            position: absolute; 
            top: 2.5rem; 
            left: 2rem; 
            z-index: 1000;
            display: flex; 
            gap: 1rem; 
        }
        .btn-volver { 
            background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); 
            color: white; 
            border: none; 
            padding: 0.8rem 1.5rem; 
            border-radius: 10px; 
            font-weight: 600; 
            font-size: 0.95rem; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 0.5rem; 
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139,92,246,0.3);
        }
        .btn-volver:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(139,92,246,0.4); 
        }
        
        .header { 
            background: var(--blanco); 
            padding: 2.5rem; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(139,92,246,0.1); 
            margin-bottom: 2rem; 
            text-align: center; 
            border: 1px solid var(--morado-claro);
            position: relative;
        }
        .saludo { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap; }
        .info-usuario { background: var(--morado-claro); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 1.1rem; font-weight: 600; }
        
        .seccion-form { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); margin-bottom: 2rem; border-top: 5px solid var(--azul); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-label { font-weight: 600; color: var(--gris-oscuro); }
        .form-input, .form-textarea, .form-select { padding: 1rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; transition: all 0.3s; background: #f9fafb; }
        .form-input:focus, .form-textarea:focus, .form-select:focus { outline: none; border-color: var(--azul); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .btn-group { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn { padding: 1rem 2rem; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: linear-gradient(135deg, var(--azul), #1D4ED8); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(59,130,246,0.3); }
        .btn-secondary { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); color: white; }
        
        .noticias-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; }
        .noticia-card { background: var(--blanco); border-radius: 15px; padding: 2rem; box-shadow: 0 8px 25px rgba(139,92,246,0.08); border-left: 5px solid var(--azul); }
        .noticia-titulo { font-size: 1.3rem; font-weight: 700; color: var(--gris-oscuro); margin-bottom: 0.5rem; }
        .noticia-fecha { color: var(--gris); font-size: 0.9rem; margin-bottom: 1rem; }
        .noticia-contenido { color: var(--gris-oscuro); margin-bottom: 1rem; line-height: 1.6; }
        .noticia-enlace { color: var(--morado); text-decoration: none; font-weight: 600; margin-right: 1rem; }
        .noticia-enlace:hover { text-decoration: underline; }

        .acciones-botones {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn-small {
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-editar {
            background: linear-gradient(135deg, var(--naranja), #f97316);
            color: white;
        }
        .btn-editar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245,158,11,0.4);
        }
        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239,68,68,0.4);
        }

        .modo-edit { background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-top-color: var(--azul) !important; }
        .form-textarea { min-height: 120px; resize: vertical; font-family: inherit; }
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; font-weight: 600; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .no-admin { text-align: center; padding: 4rem; background: var(--blanco); border-radius: 20px; color: var(--gris); margin: 2rem 0; border: 1px solid var(--morado-claro); }
        .btn-logout { background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; border: none; padding: 1.2rem 2.5rem; border-radius: 15px; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: block; margin: 3rem auto 0; transition: all 0.3s ease; }
        .btn-logout:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.4); }
        
        @media (max-width: 768px) { 
            .header-actions { left: 1rem; top: 1rem; }
            .form-grid, .noticias-grid { grid-template-columns: 1fr; } 
            .btn-group, .acciones-botones { flex-direction: column; align-items: center; }
            .btn-small { width: 100%; max-width: 250px; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-actions">
                <a href="dashboard_secretaria.php" class="btn-volver" title="Volver a Secretaría">
                    <i class="fas fa-arrow-left"></i> Secretaría
                </a>
            </div>
            
            <h1 class="saludo">
                <i class="fas fa-certificate"></i>
                Gestión Solicitudes Títulos
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
                        <i class="fas fa-edit"></i> Editar Solicitud Título (ID: <?php echo $solicitud_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Solicitud Título
                    <?php endif; ?>
                </h2>
                <form method="POST" class="form-grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $solicitud_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Tipo *</label>
                        <select name="tipo" class="form-select" required>
                            <option value="ESO" <?php echo ($modo_edit && $solicitud_edit['tipo'] == 'ESO') ? 'selected' : ''; ?>>ESO</option>
                            <option value="BACHILLERATO" <?php echo ($modo_edit && $solicitud_edit['tipo'] == 'BACHILLERATO') ? 'selected' : ''; ?>>BACHILLERATO</option>
                            <option value="FP" <?php echo ($modo_edit && $solicitud_edit['tipo'] == 'FP') ? 'selected' : ''; ?>>FP</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Título *</label>
                        <input type="text" name="titulo" class="form-input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Solicitud Título ESO">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Texto *</label>
                        <textarea name="texto" class="form-textarea" required><?php echo htmlspecialchars($modo_edit ? $solicitud_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enlace Normativa</label>
                        <input type="url" name="enlace_normativa" class="form-input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['enlace_normativa'] : ($_POST['enlace_normativa'] ?? '')); ?>"
                               placeholder="https://sede.comunidad.madrid/...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enlace Autorización</label>
                        <input type="url" name="enlace_autorizacion" class="form-input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['enlace_autorizacion'] : ($_POST['enlace_autorizacion'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Horario</label>
                        <input type="text" name="horario" class="form-input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['horario'] : ($_POST['horario'] ?? '')); ?>"
                               placeholder="Ej: L-V 9:30-12:00">
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Solicitud
                        </button>
                        <a href="dashboard_solicitud_titulos.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> <?php echo $modo_edit ? 'Cancelar' : 'Volver'; ?>
                        </a>
                    </div>
                </form>
            </div>

            <!-- LISTA DE SOLICITUDES -->
            <div class="seccion-form">
                <h2><i class="fas fa-list"></i> Solicitudes Publicadas (<?php echo count($solicitudes); ?>)</h2>
                <?php if (!empty($solicitudes)): ?>
                    <div class="noticias-grid">
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <div class="noticia-card">
                                <h3 class="noticia-titulo"><?php echo htmlspecialchars($solicitud['titulo']); ?> (<?php echo strtoupper($solicitud['tipo']); ?>)</h3>
                                <div class="noticia-fecha">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_creacion'])); ?>
                                </div>
                                <?php if ($solicitud['horario']): ?>
                                    <div class="noticia-fecha" style="margin-bottom: 1rem;">
                                        <i class="fas fa-clock"></i> <?php echo htmlspecialchars($solicitud['horario']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="noticia-contenido"><?php echo htmlspecialchars(substr($solicitud['texto'], 0, 150)); ?>...</div>
                                
                                <div style="margin: 1rem 0;">
                                    <?php if ($solicitud['enlace_normativa']): ?>
                                        <a href="<?php echo htmlspecialchars($solicitud['enlace_normativa']); ?>" class="noticia-enlace" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Normativa
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($solicitud['enlace_autorizacion']): ?>
                                        <a href="<?php echo htmlspecialchars($solicitud['enlace_autorizacion']); ?>" class="noticia-enlace" target="_blank">
                                            <i class="fas fa-file-signature"></i> Autorización
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="acciones-botones">
                                    <a href="?editar=<?php echo $solicitud['id']; ?>" class="btn btn-small btn-editar" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta solicitud de título?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $solicitud['id']; ?>">
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
                        <i class="fas fa-certificate" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                        <h3>No hay solicitudes de títulos</h3>
                        <p>Añade la primera solicitud con el formulario de arriba</p>
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
