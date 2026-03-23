<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$titulo_dashboard = "Dashboard Secretaría";

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

$submenus_secretaria = [
    ['enlace' => 'avisos.php', 'titulo' => 'Avisos', 'icono' => 'fa-bullhorn', 'descripcion' => 'Publicación y gestión de avisos importantes', 'dashboard' => 'dashboard_avisos.php'],
    ['enlace' => 'matriculacion.php', 'titulo' => 'Matriculación', 'icono' => 'fa-file-signature', 'descripcion' => 'Gestión de procesos de matrícula de estudiantes', 'dashboard' => 'dashboard_matricula.php'],
    ['enlace' => 'convalidacion.php', 'titulo' => 'Convalidación', 'icono' => 'fa-balance-scale', 'descripcion' => 'Tramitación y revisión de convalidaciones', 'dashboard' => 'dashboard_convalidacion.php'],
    ['enlace' => 'solicitud_titulos.php', 'titulo' => 'Solicitud de títulos', 'icono' => 'fa-certificate', 'descripcion' => 'Solicitud y gestión de títulos académicos', 'dashboard' => 'dashboard_solicitud_titulos.php'],
    ['enlace' => 'otros_tramites.php', 'titulo' => 'Otros trámites', 'icono' => 'fa-folder-open', 'descripcion' => 'Gestión de otros procedimientos administrativos', 'dashboard' => '#'],
    ['enlace' => 'contacto_secretaria.php', 'titulo' => 'Contacto', 'icono' => 'fa-envelope-open-text', 'descripcion' => 'Información de contacto de la secretaría', 'dashboard' => 'dashboard_contacto.php']
];

$colores = [
    'avisos' => '#EF4444',
    'matriculacion' => '#10B981',
    'convalidacion' => '#F59E0B',
    'solicitud_de_titulos' => '#3B82F6',
    'otros_tramites' => '#8B5CF6',
    'contacto' => '#EC4899'
];
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Secretaría - Dashboard Admin</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">
    </head>
    <body>
        <div class="dashboard_secretaria_container">
            <?php include 'dashboard_head.php'; ?>


            <?php if (!$is_admin): ?>
                <div class="dashboard_secretaria_no_admin">
                    <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                    <h2>Solo administradores pueden gestionar el contenido</h2>
                </div>
            <?php else: ?>
                <div class="dashboard_secretaria_dashboard_grid">
                    <?php
                    foreach ($submenus_secretaria as $item):
                        $key = strtolower(str_replace([' ', 'á'], ['_', 'a'], $item['titulo']));
                        $color_primario = $colores[$key] ?? '#10B981';
                        $color_secundario = $color_primario == '#10B981' ? '#059669' : '#0f766e';
                        ?>
                        <div class="dashboard_secretaria_cuadro_menu" style="border-top-color: <?php echo $color_primario; ?>;">
                            <div class="dashboard_secretaria_cuadro_icono" style="background: linear-gradient(135deg, <?php echo $color_primario; ?>, <?php echo $color_secundario; ?>);">
                                <i class="fas <?php echo $item['icono']; ?>"></i>
                            </div>
                            <h3 class="dashboard_secretaria_cuadro_titulo"><?php echo $item['titulo']; ?></h3>
                            <p class="dashboard_secretaria_cuadro_desc"><?php echo $item['descripcion']; ?></p>

                            <div class="dashboard_secretaria_botones_directos">
                                <a href="<?php echo $item['enlace']; ?>" class="dashboard_secretaria_submenu_item" target="_blank">
                                    <i class="fas fa-eye"></i> Ver Página
                                </a>
                                <?php if ($item['dashboard'] && $item['dashboard'] !== '#'): ?>
                                    <a href="<?php echo $item['dashboard']; ?>" class="dashboard_secretaria_submenu_item">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="dashboard_secretaria_submenu_item" onclick="alert('Dashboard <?= $item['titulo']; ?> en desarrollo')">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"> </i>  Volver
            </button>
        </form>
        </div>
    </body>
</html>
