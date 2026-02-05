<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// CARGAR DATOS AMPA
$stmt = $conexion->prepare("SELECT * FROM ampa WHERE id = 1");
$stmt->execute();
$resultado = $stmt->get_result();
$ampa_data = $resultado->fetch_assoc();
$stmt->close();

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    $titulo = trim($_POST['titulo']);
    $texto = trim($_POST['texto']);
    $enlace_formulario = trim($_POST['enlace_formulario']);
    $enlace_video = trim($_POST['enlace_video']);
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
        $tipo_imagen = $_FILES['imagen']['type'];
        $stmt = $conexion->prepare("UPDATE ampa SET titulo=?, texto=?, imagen=?, tipo_imagen=?, enlace_formulario=?, enlace_video=? WHERE id=1");
        $stmt->bind_param("sssiss", $titulo, $texto, $imagen, $tipo_imagen, $enlace_formulario, $enlace_video);
    } else {
        $stmt = $conexion->prepare("UPDATE ampa SET titulo=?, texto=?, enlace_formulario=?, enlace_video=? WHERE id=1");
        $stmt->bind_param("ssss", $titulo, $texto, $enlace_formulario, $enlace_video);
    }
    
    if ($stmt->execute()) {
        $mensaje = 'AMPA actualizado correctamente';
        header("Refresh:0");
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión AMPA - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --morado: #8B5CF6; --morado-oscuro: #7C3AED; --morado-claro: #C4B5FD;
            --blanco: #FFFFFF; --gris: #6B7280; --gris-oscuro: #1F2937; --verde: #10B981;
            --naranja: #F59E0B;
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
        
        .seccion-form { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); margin-bottom: 2rem; border-top: 5px solid var(--naranja); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-label { font-weight: 600; color: var(--gris-oscuro); }
        .form-input, .form-textarea, .form-file { padding: 1rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; transition: all 0.3s; background: #f9fafb; }
        .form-input:focus, .form-textarea:focus, .form-file:focus { outline: none; border-color: var(--naranja); box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
        .btn-group { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn { padding: 1rem 2rem; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: linear-gradient(135deg, var(--naranja), #f97316); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(245,158,11,0.3); }
        .btn-secondary { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); color: white; }
        
        .preview-section { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); margin-bottom: 2rem; border-top: 5px solid var(--verde); }
        .preview-media { display: flex; gap: 2rem; padding: 2rem; background: #f8f9fa; border-radius: 10px; margin-bottom: 2rem; }
        .preview-media-img { flex: 0 0 300px; text-align: center; }
        .preview-media-img img { max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .preview-media-video iframe { width: 100%; height: 200px; border-radius: 10px; }
        .preview-text { flex: 1; }
        .preview-text h3 { color: #2c3e50; margin-bottom: 1rem; }
        
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; font-weight: 600; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .no-admin { text-align: center; padding: 4rem; background: var(--blanco); border-radius: 20px; color: var(--gris); margin: 2rem 0; border: 1px solid var(--morado-claro); }
        .btn-logout { background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; border: none; padding: 1.2rem 2.5rem; border-radius: 15px; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: block; margin: 3rem auto 0; transition: all 0.3s ease; }
        .btn-logout:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.4); }
        
        @media (max-width: 768px) { 
            .header-actions { left: 1rem; top: 1rem; }
            .form-grid { grid-template-columns: 1fr; }
            .preview-media { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER CON BOTÓN VOLVER -->
        <div class="header">
            <div class="header-actions">
                <a href="dashboard.php" class="btn-volver" title="Volver al Dashboard">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
            
            <h1 class="saludo">
                <i class="fas fa-users"></i>
                Gestión AMPA
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

            <!-- PREVIEW ACTUAL -->
            <div class="preview-section">
                <h2><i class="fas fa-eye"></i> Vista Previa Actual</h2>
                <?php if ($ampa_data): ?>
                    <?php $hay_media = !empty($ampa_data['imagen']) || !empty($ampa_data['enlace_video']); ?>
                    <div class="preview-media">
                        <?php if ($hay_media): ?>
                            <div class="preview-media-img">
                                <?php if (!empty($ampa_data['imagen'])): ?>
                                    <img src="data:<?php echo $ampa_data['tipo_imagen']; ?>;base64,<?php echo base64_encode($ampa_data['imagen']); ?>" alt="AMPA">
                                <?php else: ?>
                                    <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $ampa_data['enlace_video'])); ?>" frameborder="0" allowfullscreen></iframe>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="preview-text">
                            <h3><?php echo htmlspecialchars($ampa_data['titulo']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($ampa_data['texto'])); ?></p>
                            <?php if (!empty($ampa_data['enlace_formulario'])): ?>
                                <a href="<?php echo htmlspecialchars($ampa_data['enlace_formulario']); ?>" target="_blank" class="btn btn-primary" style="padding: 0.7rem 1.5rem; font-size: 0.9rem;">
                                    📋 Formulario de inscripción
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- FORMULARIO EDITAR AMPA -->
            <div class="seccion-form">
                <h2><i class="fas fa-edit"></i> Editar Contenido AMPA</h2>
                <form method="POST" enctype="multipart/form-data" class="form-grid">
                    <input type="hidden" name="accion" value="editar">
                    
                    <div class="form-group">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-input" required 
                               value="<?php echo htmlspecialchars($ampa_data['titulo'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nueva Imagen (opcional)</label>
                        <input type="file" name="imagen" class="form-file" accept="image/*">
                        <small>Dejar vacío para mantener la actual</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enlace Video YouTube (opcional)</label>
                        <input type="url" name="enlace_video" class="form-input" 
                               value="<?php echo htmlspecialchars($ampa_data['enlace_video'] ?? ''); ?>"
                               placeholder="https://www.youtube.com/watch?v=...">
                        <small>Solo uno: imagen O video</small>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Texto Principal</label>
                        <textarea name="texto" class="form-textarea" required rows="8"><?php echo htmlspecialchars($ampa_data['texto'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enlace Formulario (opcional)</label>
                        <input type="url" name="enlace_formulario" class="form-input" 
                               value="<?php echo htmlspecialchars($ampa_data['enlace_formulario'] ?? ''); ?>"
                               placeholder="https://docs.google.com/forms/...">
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar AMPA
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Volver al Dashboard
                        </a>
                    </div>
                </form>
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
