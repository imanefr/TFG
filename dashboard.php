<?php
session_start();
include 'conexion.php';

// Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// Estadísticas
$stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE activo = 1");
$stmt->execute();
$total_usuarios = $stmt->get_result()->fetch_row()[0];
$stmt->close();

$menu_principal = [
    'inicio' => ['icono' => 'fa-home', 'titulo' => 'Página Inicio', 'descripcion' => 'Página principal del IES La Arboleda', 'enlace' => 'index.php', 'dashboard' => 'dashboard_inicio.php'],
    'nuestro_centro' => ['icono' => 'fa-building', 'titulo' => 'Nuestro Centro', 'descripcion' => 'Organigrama, AMPA, Resultados Académicos', 'enlace' => '', 'dashboard' => 'dashboard_nuestro_centro.php'],
    'oferta_educativa' => ['icono' => 'fa-graduation-cap', 'titulo' => 'Oferta Educativa', 'descripcion' => 'ESO, Bachillerato, Formación Profesional', 'enlace' => '', 'dashboard' => 'dashboard_oferta_educativa.php'],
    'secretaria' => ['icono' => 'fa-file-alt', 'titulo' => 'Secretaría', 'descripcion' => 'Matriculaciones, convalidaciones, títulos', 'enlace' => '', 'dashboard' => 'dashboard_secretaria.php'],
    'erasmus' => ['icono' => 'fa-plane', 'titulo' => 'Erasmus+', 'descripcion' => 'Programa de movilidad europea', 'enlace' => 'erasmus.php', 'dashboard' => 'dashboard_erasmus.php'],
    'documentos' => ['icono' => 'fa-file-pdf', 'titulo' => 'Documentos Institucionales', 'descripcion' => 'Documentos oficiales del centro', 'enlace' => 'doc_institucionales.php', 'dashboard' => 'dashboard_doc_institucionales.php'],
    'departamentos' => ['icono' => 'fa-users', 'titulo' => 'Departamentos', 'descripcion' => 'Listado de departamentos académicos', 'enlace' => 'departamentos.php', 'dashboard' => ''],
    'familias' => ['icono' => 'fa-user-friends', 'titulo' => 'Información Familias', 'descripcion' => 'Comunicaciones para familias', 'enlace' => 'info_familias.php', 'dashboard' => ''],
    'orientacion' => ['icono' => 'fa-chalkboard-teacher', 'titulo' => 'Orientación', 'descripcion' => 'Departamento de orientación', 'enlace' => 'orientacion.php', 'dashboard' => '']
];

$colores = [
    'inicio' => '#8B5CF6', 'nuestro_centro' => '#10B981', 'oferta_educativa' => '#3B82F6',
    'secretaria' => '#F59E0B', 'departamentos' => '#EC4899', 'erasmus' => '#06B6D4',
    'familias' => '#10B981', 'documentos' => '#F59E0B', 'orientacion' => '#8B5CF6'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - IES La Arboleda</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_inicio_container">

        <!-- HEADER GLOBAL -->
        <?php include 'dashboard_head.php'; ?>

        <?php if (!$is_admin): ?>
            <div class="dashboard_inicio_no_admin">
                <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
                <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
            </div>
        <?php else: ?>

            <!-- ESTADÍSTICAS -->
            <div class="dashboard_inicio_stats_grid">
                <div class="dashboard_inicio_stat_card">
                    <div class="dashboard_inicio_stat_number"><?php echo $total_usuarios; ?></div>
                    <div class="dashboard_inicio_stat_label">Total Usuarios</div>
                    <a href="dashboard_usuarios.php" class="dashboard_inicio_boton_gestion">
                        <i class="fas fa-users-cog"></i> Gestión de Usuarios
                    </a>
                </div>
            </div>

            <!-- CUADROS DEL MENÚ -->
            <div class="dashboard_inicio_dashboard_grid">
                <?php foreach ($menu_principal as $key => $item): ?>
                    <div class="dashboard_inicio_cuadro_menu" style="border-top-color: <?php echo $colores[$key] ?? '#8B5CF6'; ?>;">
                        <div class="dashboard_inicio_cuadro_icono" style="background: linear-gradient(135deg, <?php echo $colores[$key] ?? '#8B5CF6'; ?>, <?php echo $colores[$key] ?? '#7C3AED'; ?>);">
                            <i class="fas <?php echo $item['icono']; ?>"></i>
                        </div>
                        <h3 class="dashboard_inicio_cuadro_titulo"><?php echo $item['titulo']; ?></h3>
                        <p class="dashboard_inicio_cuadro_desc"><?php echo $item['descripcion']; ?></p>

                        <div class="dashboard_inicio_botones_directos">
                            <?php if (!empty($item['enlace'])): ?>
                                <a href="<?php echo $item['enlace']; ?>" class="dashboard_inicio_submenu_item" target="_blank">
                                    <i class="fas fa-eye"></i> Ver Página
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($item['dashboard'])): ?>
                                <a href="<?php echo $item['dashboard']; ?>" class="dashboard_inicio_submenu_item">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php else: ?>
                                <a href="#" class="dashboard_inicio_submenu_item" onclick="alert('Editar <?php echo $item['titulo']; ?> en desarrollo')">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
