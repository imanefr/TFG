<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');// PROCESAR ACCIONES

$submenus_nuestro_centro = [
    ['enlace' => 'relevante_ahora.php', 'titulo' => 'Relevante Ahora', 'icono' => 'fas fa-bookmark', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias marcadas como relevantes.'],
    ['enlace' => 'ultimas_noticias.php', 'titulo' => 'Noticias', 'icono' => 'fas fa-bolt', 'descripcion' => 'Lista, Crea, Actualiza o Elimina todas las noticias.'],
];

$colores = ['relevante_ahora' => '#10B981', 'ultimas_noticias' => '#F59E0B'];

// Título dinámico para el header global
$titulo_dashboard = "Dashboard Inicio";
?>
    <!-- HEADER GLOBAL -->
        <?php include 'dashboard_head.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_nuestro_centro_container">
        
    

        <?php if (!$is_admin): ?>
            <div class="dashboard_nuestro_centro_no_admin">
                <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
                <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
            </div>
        <?php else: ?>
            <!-- CUADROS DEL MENÚ - 3 ELEMENTOS -->
            <div class="dashboard_nuestro_centro_dashboard_grid">
                <?php foreach ($submenus_nuestro_centro as $item): 
                    $key = strtolower(str_replace([' ', 'á'], ['', 'a'], $item['titulo']));
                    $dashboard_page = 'dashboard_' . $key . '.php';
                ?>
                    <div class="dashboard_nuestro_centro_cuadro_menu" style="border-top-color: <?php echo $colores[$key] ?? '#10B981'; ?>;">
                        <div class="dashboard_nuestro_centro_cuadro_icono" style="background: linear-gradient(135deg, <?php echo $colores[$key] ?? '#10B981'; ?>, #059669);">
                            <i class="fas <?php echo $item['icono']; ?>"></i>
                        </div>
                        <h3 class="dashboard_nuestro_centro_cuadro_titulo"><?php echo $item['titulo']; ?></h3>
                        <p class="dashboard_nuestro_centro_cuadro_desc"><?php echo $item['descripcion']; ?></p>
                        
                        <div class="dashboard_nuestro_centro_botones_directos">
                            <a href="<?php echo $item['enlace']; ?>" class="dashboard_nuestro_centro_submenu_item" target="_blank">
                                <i class="fas fa-eye"></i> Ver Página
                            </a>
                            <a href="<?php echo $dashboard_page; ?>" class="dashboard_nuestro_centro_submenu_item">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- BOTÓN LOGOUT -->
        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"> </i>  Volver
            </button>
        </form>
    </div>
</body>
</html>