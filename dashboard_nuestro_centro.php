<?php
// INICIO DASHBOARD - Gestión "Nuestro Centro" IES La Arboleda
session_start(); // Iniciar sesión para autenticación global
include 'conexion.php'; // Conexión MySQLi segura
// SEGURIDAD: Verificar usuario autenticado antes de cualquier renderizado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir a login si sesión expiró
    exit; // Parar ejecución inmediatamente
}

// CONTROL DE PERMISOS: Solo admins acceden a gestión de contenido
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// CONFIGURACIÓN MENÚS - Array de subsecciones "Nuestro Centro"
$submenus_nuestro_centro = [
    ['enlace' => 'organigrama.php', 'titulo' => 'Organigrama', 'icono' => 'fa-sitemap', 'descripcion' => 'Estructura organizativa del centro'],
    ['enlace' => 'ampa.php', 'titulo' => 'AMPA', 'icono' => 'fa-users', 'descripcion' => 'Asociación de padres y madres'],
    ['enlace' => 'resultados_academicos.php', 'titulo' => 'Resultados Académicos', 'icono' => 'fa-chart-bar', 'descripcion' => 'Resultados académicos del centro']
];

// PALETA DE COLORES - Identidad visual por sección
$colores = [
    'organigrama' => '#10B981', // Verde Emerald (estructura)
    'ampa' => '#F59E0B', // Naranja Amber (comunidad)  
    'resultados_academicos' => '#3B82F6' // Azul Sky (académico)
];

// HEADER: Título dinámico para dashboard_head.php
$titulo_dashboard = "Dashboard Nuestro Centro";
?>

<!DOCTYPE html>
<html lang="es"> <!-- Español para accesibilidad/SEO -->
    <head>
        <!-- META TÉCNICOS -->
        <meta charset="UTF-8"> <!-- Soporte ñ/acentos -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mobile-first -->
        <title>Nuestro Centro - Dashboard Admin</title> 
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Iconos -->
        <link rel="stylesheet" href="style_dashboard.css"> <!-- Estilos personalizados -->
    </head>

    <body>
        <!-- CONTENEDOR PRINCIPAL - Dashboard "Nuestro Centro" -->
        <div class="dashboard_nuestro_centro_container">

            <!-- HEADER GLOBAL REUTILIZABLE -->
            <?php include 'dashboard_head.php'; ?> <!-- Título + usuario + logout -->

            <!-- RESTRICCIÓN ADMIN - Solo administradores gestionan -->
            <?php if (!$is_admin): ?>
                <!-- PÁGINA BLOQUEADA - Vista no-admin -->
                <div class="dashboard_nuestro_centro_no_admin">
                    <i class="fas fa-lock dashboard_nuestro_centro_no_admin_icono"></i>
                    <h2>Solo administradores pueden gestionar el contenido</h2>
                    <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
                </div>
            <?php else: ?>
                <!-- DASHBOARD ADMIN - Grid de 3 cuadros de gestión -->
                <div class="dashboard_nuestro_centro_dashboard_grid">
                    <?php
                    foreach ($submenus_nuestro_centro as $item):
                        // GENERAR CLAVES URL AMIGABLES - organigrama.php → dashboard_organigrama.php
                        $key = strtolower(str_replace(['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'],
                                        ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
                                        str_replace(' ', '_', $item['titulo'])));
                        $dashboard_page = 'dashboard_' . $key . '.php'; // Página de edición
                        ?>
                        <!-- CUADRO INDIVIDUAL - Cada subsección -->
                        <div class="dashboard_nuestro_centro_cuadro_menu dashboard_nuestro_centro_cuadro_color_<?php echo $key; ?>">

                            <!-- ICONO DESTACADO - Gradiente por color de sección -->
                            <div class="dashboard_nuestro_centro_cuadro_icono dashboard_nuestro_centro_cuadro_icono_color_<?php echo $key; ?>">
                                <i class="fas <?php echo $item['icono']; ?>"></i> <!-- Icono dinámico -->
                            </div>

                            <!-- CONTENIDO TEXTO -->
                            <h3 class="dashboard_nuestro_centro_cuadro_titulo"><?php echo $item['titulo']; ?></h3>
                            <p class="dashboard_nuestro_centro_cuadro_desc"><?php echo $item['descripcion']; ?></p>

                            <!-- BOTONES DUALES - Ver página pública + Editar dashboard -->
                            <div class="dashboard_nuestro_centro_botones_directos">
                                <!-- VER PÁGINA PÚBLICA (target _blank) -->
                                <a href="<?php echo $item['enlace']; ?>" 
                                   class="dashboard_nuestro_centro_submenu_item" 
                                   target="_blank">
                                    <i class="fas fa-eye"></i> Ver Página
                                </a>
                                <!-- EDITAR EN DASHBOARD ADMIN -->
                                <a href="<?php echo $dashboard_page; ?>" 
                                   class="dashboard_nuestro_centro_submenu_item">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                <?php endforeach; ?>
                </div> <!-- Fin Grid 3 cuadros -->
<?php endif; ?>

            <!-- NAVEGACIÓN: Volver a dashboard principal -->
            <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
                <button type="submit" class="dashboard_universal_btn_volver">
                    <i class="fas fa-arrow-left"></i> Volver <!-- Icono + texto -->
                </button>
            </form>
        </div> <!-- Fin contenedor principal -->
    </body>
</html>
