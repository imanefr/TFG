<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// ✅ MANEJAR ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $etapa = $_GET['etapa'] ?? '';
    
    $tabla_map = ['ESO' => 'matriculacion_eso', 'Bachillerato' => 'matriculacion_bachillerato', 'FP' => 'matriculacion_fp'];
    $tabla = $tabla_map[$etapa] ?? null;
    
    if ($tabla) {
        $sql = "DELETE FROM `$tabla` WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['mensaje_ok'] = 'Matrícula eliminada correctamente';
        } else {
            $_SESSION['mensaje_error'] = 'Error al eliminar';
        }
        $stmt->close();
    }
    header('Location: dashboard_matricula.php');
    exit;
}

// ✅ MANEJAR EDITAR - BUSCAR EN LAS 3 TABLAS
$matricula_editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    
    // Buscar en ESO
    $sql = "SELECT 'ESO' as etapa, id, titulo, descripcion, ruta_pdf, fecha FROM matriculacion_eso WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $matricula_editar = $row;
    } else {
        // Buscar en Bachillerato
        $sql = "SELECT 'Bachillerato' as etapa, id, titulo, descripcion, ruta_pdf, fecha FROM matriculacion_bachillerato WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $matricula_editar = $row;
        } else {
            // Buscar en FP
            $sql = "SELECT 'FP' as etapa, id, titulo, descripcion, ruta_pdf, fecha FROM matriculacion_fp WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $matricula_editar = $row;
            }
        }
    }
    $stmt->close();
}

// Mensajes
$mensaje_ok = $_SESSION['mensaje_ok'] ?? '';
$mensaje_error = $_SESSION['mensaje_error'] ?? '';
unset($_SESSION['mensaje_ok'], $_SESSION['mensaje_error']);

// Obtener matrículas para listar
function obtenerMatriculas($conexion) {
    $matriculas = [];
    
    $sql_eso = "SELECT 'ESO' AS etapa, id, titulo, descripcion, ruta_pdf, fecha FROM matriculacion_eso WHERE activo = 1 ORDER BY fecha DESC";
    $result_eso = $conexion->query($sql_eso);
    if ($result_eso) while ($row = $result_eso->fetch_assoc()) $matriculas[] = $row;
    
    $sql_bach = "SELECT 'Bachillerato' AS etapa, id, titulo, descripcion, ruta_pdf, fecha FROM matriculacion_bachillerato WHERE activo = 1 ORDER BY fecha DESC";
    $result_bach = $conexion->query($sql_bach);
    if ($result_bach) while ($row = $result_bach->fetch_assoc()) $matriculas[] = $row;
    
    $sql_fp = "SELECT 'FP' AS etapa, id, titulo, descripcion, ruta_pdf, fecha FROM matriculacion_fp WHERE activo = 1 ORDER BY fecha DESC";
    $result_fp = $conexion->query($sql_fp);
    if ($result_fp) while ($row = $result_fp->fetch_assoc()) $matriculas[] = $row;
    
    return $matriculas;
}

$matriculas = obtenerMatriculas($conexion);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Matriculación - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --morado: #8B5CF6; --morado-oscuro: #7C3AED; --morado-claro: #C4B5FD;
            --blanco: #FFFFFF; --gris: #6B7280; --gris-oscuro: #1F2937;
            --verde: #10B981; --rojo: #EF4444; --naranja: #F59E0B;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: linear-gradient(135deg, #F8FAFC, #EDE9FE); padding: 2rem; }
        .container { max-width: 1400px; margin: 0 auto; }
        .btn-volver { background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(139,92,246,0.3); margin-bottom: 2rem; display: block; }
        .btn-volver:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(139,92,246,0.4); }
        .header { background: var(--blanco); padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(139,92,246,0.1); margin-bottom: 2rem; text-align: center; border: 1px solid var(--morado-claro); }
        .titulo-principal { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; }
        
        .form-section { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); margin-bottom: 2rem; border-top: 5px solid var(--verde); }
        .form-section.editando { border-top-color: var(--morado); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        label { font-weight: 600; color: var(--gris-oscuro); }
        input, select, textarea { padding: 1rem; border: 2px solid #E5E7EB; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--morado); box-shadow: 0 0 0 3px rgba(139,92,246,0.1); }
        textarea { resize: vertical; min-height: 120px; }
        .btn-guardar { background: linear-gradient(135deg, var(--verde), #059669); color: white; border: none; padding: 1.2rem 3rem; border-radius: 15px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16,185,129,0.3); }
        .btn-cancelar { background: #6B7280; color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
        .btn-guardar:hover, .btn-cancelar:hover { transform: translateY(-2px); }
        
        .lista-section { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); }
        .lista-matriculas { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .matricula-card { background: #F8FAFC; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease; border-left: 4px solid var(--verde); }
        .matricula-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .etapa-tag { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: white; margin-bottom: 1rem; }
        .etapa-eso { background: var(--rojo); }
        .etapa-bachillerato { background: var(--naranja); }
        .etapa-fp { background: var(--verde); }
        .fecha { color: var(--gris); font-size: 0.9rem; margin-bottom: 0.5rem; }
        .titulo-matricula { font-size: 1.2rem; font-weight: 700; color: var(--gris-oscuro); margin-bottom: 0.8rem; }
        .descripcion { color: var(--gris); margin-bottom: 1rem; line-height: 1.5; }
        .acciones { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .btn-accion { color: white; padding: 0.6rem 1.2rem; border-radius: 8px; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.3rem; transition: all 0.3s ease; font-weight: 600; }
        .btn-editar { background: var(--morado); }
        .btn-eliminar { background: var(--rojo); }
        .btn-accion:hover { transform: translateY(-2px); }
        
        .mensaje { padding: 1rem 2rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600; }
        .mensaje-ok { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .mensaje-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        
        @media (max-width: 768px) { 
            .form-grid { grid-template-columns: 1fr; } 
            .lista-matriculas { grid-template-columns: 1fr; }
            .acciones { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard_secretaria.php" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver Secretaría
        </a>
        
        <div class="header">
            <h1 class="titulo-principal">
                <i class="fas fa-file-signature"></i> Gestión Matriculación
            </h1>
        </div>

        <?php if ($mensaje_ok): ?>
            <div class="mensaje mensaje-ok">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensaje_ok) ?>
            </div>
        <?php endif; ?>

        <?php if ($mensaje_error): ?>
            <div class="mensaje mensaje-error">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensaje_error) ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO -->
        <section class="form-section <?= $matricula_editar ? 'editando' : '' ?>">
            <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;">
                <i class="fas fa-<?= $matricula_editar ? 'edit' : 'plus-circle' ?>"></i> 
                <?= $matricula_editar ? 'Editando: ' . htmlspecialchars($matricula_editar['titulo']) : 'Nueva Matrícula' ?>
            </h2>
            <form action="procesar_matricula.php" method="POST">
                <input type="hidden" name="id" value="<?= $matricula_editar['id'] ?? '' ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Etapa Educativa *</label>
                        <select name="etapa" required>
                            <option value="">-- Selecciona etapa --</option>
                            <option value="eso" <?= ($matricula_editar && $matricula_editar['etapa']=='ESO') ? 'selected' : '' ?>>ESO</option>
                            <option value="bachillerato" <?= ($matricula_editar && $matricula_editar['etapa']=='Bachillerato') ? 'selected' : '' ?>>Bachillerato</option>
                            <option value="fp" <?= ($matricula_editar && $matricula_editar['etapa']=='FP') ? 'selected' : '' ?>>FP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Título *</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($matricula_editar['titulo'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha *</label>
                        <input type="date" name="fecha" value="<?= $matricula_editar['fecha'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Ruta PDF *</label>
                        <input type="text" name="ruta_pdf" value="<?= htmlspecialchars($matricula_editar['ruta_pdf'] ?? '') ?>" required>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Descripción *</label>
                        <textarea name="descripcion" required><?= htmlspecialchars($matricula_editar['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                    <button type="submit" name="guardar" class="btn-guardar">
                        <i class="fas fa-save"></i> <?= $matricula_editar ? 'Actualizar' : 'Añadir' ?> Matrícula
                    </button>
                    <?php if ($matricula_editar): ?>
                        <a href="dashboard_matricula.php" class="btn-cancelar">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- LISTA MATRÍCULAS -->
        <section class="lista-section">
            <h2 style="color: var(--gris-oscuro); margin-bottom: 1.5rem;">
                <i class="fas fa-list"></i> Matrículas Publicadas (<?= count($matriculas) ?>)
            </h2>
            <?php if (empty($matriculas)): ?>
                <p style="text-align: center; color: var(--gris); padding: 2rem;">No hay matrículas publicadas aún.</p>
            <?php else: ?>
                <div class="lista-matriculas">
                    <?php foreach ($matriculas as $matricula): ?>
                        <div class="matricula-card">
                            <span class="etapa-tag etapa-<?= strtolower(str_replace(' ', '-', $matricula['etapa'])) ?>">
                                <?= htmlspecialchars($matricula['etapa']) ?>
                            </span>
                            <div class="fecha">
                                <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($matricula['fecha'])) ?>
                            </div>
                            <h4 class="titulo-matricula"><?= htmlspecialchars($matricula['titulo']) ?></h4>
                            <p class="descripcion"><?= htmlspecialchars(substr($matricula['descripcion'], 0, 100)) ?>...</p>
                            <div class="acciones">
                                <a href="?editar=<?= $matricula['id'] ?>" class="btn-accion btn-editar" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?eliminar=<?= $matricula['id'] ?>&etapa=<?= urlencode($matricula['etapa']) ?>" 
                                   class="btn-accion btn-eliminar" 
                                   onclick="return confirm('¿Eliminar?\n<?= htmlspecialchars($matricula['titulo']) ?>')" 
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
