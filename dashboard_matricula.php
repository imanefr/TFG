<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_dashboard = "Gestión de Matriculación";
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

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
    $sql = "SELECT 'ESO' as etapa, id, titulo, descripcion, ruta_pdf, fecha_creacion FROM matriculacion_eso WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $matricula_editar = $row;
    } else {
        // Buscar en Bachillerato
        $sql = "SELECT 'Bachillerato' as etapa, id, titulo, descripcion, ruta_pdf, fecha_creacion FROM matriculacion_bachillerato WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $matricula_editar = $row;
        } else {
            // Buscar en FP
            $sql = "SELECT 'FP' as etapa, id, titulo, descripcion, ruta_pdf, fecha_creacion FROM matriculacion_fp WHERE id = ?";
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
    
    $sql_eso = "SELECT 'ESO' AS etapa, id, titulo, descripcion, ruta_pdf, fecha_creacion FROM matriculacion_eso WHERE activo = 1 ORDER BY fecha_creacion DESC";
    $result_eso = $conexion->query($sql_eso);
    if ($result_eso) while ($row = $result_eso->fetch_assoc()) $matriculas[] = $row;
    
    $sql_bach = "SELECT 'Bachillerato' AS etapa, id, titulo, descripcion, ruta_pdf, fecha_creacion FROM matriculacion_bachillerato WHERE activo = 1 ORDER BY fecha_creacion DESC";
    $result_bach = $conexion->query($sql_bach);
    if ($result_bach) while ($row = $result_bach->fetch_assoc()) $matriculas[] = $row;
    
    $sql_fp = "SELECT 'FP' AS etapa, id, titulo, descripcion, ruta_pdf, fecha_creacion FROM matriculacion_fp WHERE activo = 1 ORDER BY fecha_creacion DESC";
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
    <title>Gestión Matriculación - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_matricula_container">
        <!-- HEADER -->
        <?php include 'dashboard_head.php'; ?>

        <?php if (!$is_admin): ?>
            <div class="dashboard_matricula_no_admin">
                <i class="fas fa-lock dashboard_inicio_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar la matriculación</h2>
            </div>
        <?php else: ?>

            <?php if ($mensaje_ok): ?>
                <div class="dashboard_avisos_alert dashboard_avisos_alert_success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensaje_ok) ?>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_error): ?>
                <div class="dashboard_avisos_alert dashboard_avisos_alert_error">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensaje_error) ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <div class="dashboard_matricula_seccion_form <?= $matricula_editar ? 'dashboard_matricula_modo_edit' : '' ?>">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;">
                    <i class="fas fa-<?= $matricula_editar ? 'edit' : 'plus-circle' ?>"></i> 
                    <?= $matricula_editar ? 'Editar Matrícula: ' . htmlspecialchars($matricula_editar['titulo']) : 'Añadir Nueva Matrícula' ?>
                </h2>
                <form action="procesar_matricula.php" method="POST" class="dashboard_matricula_form_grid">
                    <input type="hidden" name="id" value="<?= $matricula_editar['id'] ?? '' ?>">
                    <input type="hidden" name="accion" value="<?= $matricula_editar ? 'editar' : 'nueva' ?>">
                    
                    <div class="dashboard_matricula_form_group">
                        <label class="dashboard_matricula_form_label">Etapa Educativa *</label>
                        <select name="etapa" class="dashboard_matricula_form_select" required>
                            <option value="">-- Selecciona etapa --</option>
                            <option value="eso" <?= ($matricula_editar && $matricula_editar['etapa']=='ESO') ? 'selected' : '' ?>>ESO</option>
                            <option value="bachillerato" <?= ($matricula_editar && $matricula_editar['etapa']=='Bachillerato') ? 'selected' : '' ?>>Bachillerato</option>
                            <option value="fp" <?= ($matricula_editar && $matricula_editar['etapa']=='FP') ? 'selected' : '' ?>>FP</option>
                        </select>
                    </div>
                    <div class="dashboard_matricula_form_group">
                        <label class="dashboard_matricula_form_label">Título de la Matrícula *</label>
                        <input type="text" name="titulo" class="dashboard_matricula_form_input" 
                               value="<?= htmlspecialchars($matricula_editar['titulo'] ?? '') ?>" 
                               placeholder="Ej: Instrucciones Matrícula ESO 2025" required>
                    </div>
                    <div class="dashboard_matricula_form_group">
                        <label class="dashboard_matricula_form_label">Fecha de Publicación *</label>
                        <input type="date" name="fecha" class="dashboard_matricula_form_input" 
                               value="<?= $matricula_editar['fecha_creacion'] ?? date('Y-m-d') ?>" required>
                    </div>
                    <div class="dashboard_matricula_form_group">
                        <label class="dashboard_matricula_form_label">Ruta al PDF (URL/Carpeta) *</label>
                        <input type="text" name="ruta_pdf" class="dashboard_matricula_form_input" 
                               value="<?= htmlspecialchars($matricula_editar['ruta_pdf'] ?? '') ?>" 
                               placeholder="pdfs/matricula_eso.pdf" required>
                    </div>
                    <div class="dashboard_matricula_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_matricula_form_label">Descripción / Observaciones *</label>
                        <textarea name="descripcion" class="dashboard_matricula_form_textarea" 
                                  placeholder="Escribe aquí los detalles del proceso..." required><?= htmlspecialchars($matricula_editar['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="dashboard_matricula_btn_group">
                        <button type="submit" name="guardar" class="dashboard_matricula_btn dashboard_matricula_btn_primary">
                            <i class="fas fa-save"></i> <?= $matricula_editar ? 'Actualizar Cambios' : 'Publicar Matrícula' ?>
                        </button>
                        <?php if ($matricula_editar): ?>
                            <a href="dashboard_matricula.php" class="dashboard_matricula_btn dashboard_matricula_btn_secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- LISTA MATRÍCULAS -->
            <div class="dashboard_matricula_seccion_lista">
                <h2>
                    <i class="fas fa-list"></i> Matrículas Publicadas (<?= count($matriculas) ?>)
                </h2>
                <?php if (empty($matriculas)): ?>
                    <div class="dashboard_erasmus_vacio">
                        <i class="fas fa-file-signature"></i>
                        <h3>No hay matrículas publicadas aún.</h3>
                        <p>Utiliza el formulario de arriba para añadir el primer trámite.</p>
                    </div>
                <?php else: ?>
                    <div class="dashboard_matricula_grid">
                        <?php foreach ($matriculas as $matricula): ?>
                            <div class="dashboard_matricula_card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="dashboard_matricula_tag tag_<?= strtolower(str_replace(' ', '-', $matricula['etapa'])) ?>">
                                        <?= htmlspecialchars($matricula['etapa']) ?>
                                    </span>
                                    <div class="dashboard_matricula_card_fecha">
                                        <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($matricula['fecha_creacion'])) ?>
                                    </div>
                                </div>
                                <h4 class="dashboard_matricula_card_titulo"><?= htmlspecialchars($matricula['titulo']) ?></h4>
                                <p class="dashboard_matricula_card_desc"><?= htmlspecialchars(substr($matricula['descripcion'], 0, 120)) ?>...</p>
                                
                                <div class="dashboard_matricula_acciones">
                                    <a href="?editar=<?= $matricula['id'] ?>" class="dashboard_matricula_btn_small btn_edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="?eliminar=<?= $matricula['id'] ?>&etapa=<?= urlencode($matricula['etapa']) ?>" 
                                       class="dashboard_matricula_btn_small btn_delete" 
                                       onclick="return confirm('¿Seguro que quieres eliminar esta publicación?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dashboard_secretaria.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver a Secretaría
            </button>
        </form>
    </div>
</body>
</html>
>
</body>
</html>
