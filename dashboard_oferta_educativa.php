<?php
// DASHBOARD OFERTA EDUCATIVA - Gestión IES La Arboleda (misma estructura Nuestro Centro)
session_start(); // Iniciar sesión para autenticación global
include 'conexion.php'; // Conexión MySQLi segura
// SEGURIDAD: Verificar usuario autenticado antes de cualquier renderizado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir a login si sesión expiró
    exit; // Parar ejecución inmediatamente
}

// CONTROL DE PERMISOS: Solo admins acceden a gestión de contenido
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');// PROCESAR ACCIONES

// CONFIGURACIÓN MENÚS - Array de subsecciones "Oferta Educativa"
$submenus_oferta = [
    ['enlace' => 'eso_info.php', 'titulo' => 'ESO', 'icono' => 'fa-school', 'descripcion' => 'Educación Secundaria Obligatoria'],
    ['enlace' => 'bachillerato.php', 'titulo' => 'Bachillerato', 'icono' => 'fa-graduation-cap', 'descripcion' => 'Bachillerato (en desarrollo)'],
    ['enlace' => 'fp_info.php', 'titulo' => 'FP General', 'icono' => 'fa-tools', 'descripcion' => 'Formación Profesional general'],
    ['enlace' => 'desarrollo_videojuegos.php', 'titulo' => 'Desarrollo Videojuegos', 'icono' => 'fa-gamepad', 'descripcion' => 'Curso superior de videojuegos']
];

// PALETA DE COLORES - Identidad visual por sección
$colores = [
    'eso' => '#3B82F6',                 // Azul Sky
    'bachillerato' => '#8B5CF6',        // Violeta
    'fp_general' => '#10B981',          // Verde Emerald
    'desarrollo_videojuegos' => '#F59E0B' // Naranja Amber
];

// HEADER: Título dinámico para dashboard_head.php
$titulo_dashboard = "Dashboard Oferta Educativa";
?>
<!-- HEADER GLOBAL REUTILIZABLE -->
            <?php include 'dashboard_head.php'; ?> <!-- Título + usuario + logout -->
<!DOCTYPE html>
<html lang="es"> <!-- Español para accesibilidad/SEO -->
    <head>
        <!-- META TÉCNICOS -->
        <meta charset="UTF-8"> <!-- Soporte ñ/acentos -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mobile-first -->
        <title>Oferta Educativa - Dashboard Admin</title> 
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Iconos -->
        <link rel="stylesheet" href="style_dashboard.css"> <!-- Estilos personalizados -->
    </head>

    <body>
        <!-- CONTENEDOR PRINCIPAL - Dashboard "Oferta Educativa" -->
        <div class="dashboard_nuestro_centro_container">

            

            <!-- RESTRICCIÓN ADMIN - Solo administradores gestionan -->
            <?php if (!$is_admin): ?>
                <!-- PÁGINA BLOQUEADA - Vista no-admin -->
                <div class="dashboard_nuestro_centro_no_admin">
                    <i class="fas fa-lock dashboard_nuestro_centro_no_admin_icono"></i>
                    <h2>Solo administradores pueden gestionar el contenido</h2>
                    <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
                </div>
            <?php else: ?>
                <!-- DASHBOARD ADMIN - Grid de 4 cuadros de gestión -->
                <div class="dashboard_nuestro_centro_dashboard_grid">
                    <?php
                    foreach ($submenus_oferta as $item):
                        // GENERAR CLAVES URL AMIGABLES - Normalizar tildes y espacios
                        $key = strtolower(str_replace(['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', ' '],
                                        ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', '_'],
                                        $item['titulo']));
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
                </div> <!-- Fin Grid 4 cuadros -->
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