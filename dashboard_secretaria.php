<?php
// DASHBOARD SECRETARÍA
session_start(); // Iniciar sesión para autenticación + permisos
include 'conexion.php'; // Conexión MySQLi (aunque no se usa aquí)

// SEGURIDAD CRÍTICA: Verificar sesión activa antes de renderizar
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir si sesión expiró/inválida
    exit; // Detener ejecución inmediatamente
}

// VARIABLE GLOBAL: Título para dashboard_head.php reutilizable
$titulo_dashboard = "Dashboard Secretaría";

// CONTROL ACCESO: Solo administradores gestionan secretaría
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// MENÚ SECRETARÍA - 6 subsecciones con enlaces directos a dashboards
$submenus_secretaria = [
    // AVISOS - Publicaciones urgentes
    ['enlace' => 'avisos.php', 'titulo' => 'Avisos', 'icono' => 'fa-bullhorn', 
     'descripcion' => 'Publicación y gestión de avisos importantes', 
     'dashboard' => 'dashboard_avisos.php'],
    
    // MATRÍCULA - Procesos anuales
    ['enlace' => 'matriculacion.php', 'titulo' => 'Matriculación', 'icono' => 'fa-file-signature', 
     'descripcion' => 'Gestión de procesos de matrícula de estudiantes', 
     'dashboard' => 'dashboard_matricula.php'],
    
    // CONVALIDACIÓN - Reconocimiento créditos
    ['enlace' => 'convalidacion.php', 'titulo' => 'Convalidación', 'icono' => 'fa-balance-scale', 
     'descripcion' => 'Tramitación y revisión de convalidaciones', 
     'dashboard' => 'dashboard_convalidacion.php'],
    
    // TÍTULOS - Expedición oficial
    ['enlace' => 'solicitud_titulos.php', 'titulo' => 'Solicitud de títulos', 'icono' => 'fa-certificate', 
     'descripcion' => 'Solicitud y gestión de títulos académicos', 
     'dashboard' => 'dashboard_solicitud_titulos.php'],
    
    // OTROS - Trámites varios (EN DESARROLLO)
    ['enlace' => 'otros_tramites.php', 'titulo' => 'Otros trámites', 'icono' => 'fa-folder-open', 
     'descripcion' => 'Gestión de otros procedimientos administrativos', 
     'dashboard' => 'dashboard_otros_tramites.php'],
    
    // CONTACTO - Datos secretaría
    ['enlace' => 'contacto_secretaria.php', 'titulo' => 'Contacto', 'icono' => 'fa-envelope-open-text', 
     'descripcion' => 'Información de contacto de la secretaría', 
     'dashboard' => 'dashboard_contacto.php']
];

// IDENTIDAD VISUAL - Colores únicos por subsección secretaria
$colores = [
    'avisos' => '#EF4444',           // Rojo - Urgencia
    'matriculacion' => '#10B981',    // Verde - Confirmación
    'convalidacion' => '#F59E0B',    // Naranja - Revisión
    'solicitud_de_titulos' => '#3B82F6', // Azul - Oficial
    'otros_tramites' => '#8B5CF6',   // Violeta - Misceláneo
    'contacto' => '#EC4899'          // Rosa - Comunicación
];
?>

<!DOCTYPE html> <!-- HTML5 estándar -->
<html lang="es"> <!-- Accesibilidad + SEO local -->
<head>
    <!-- CONFIGURACIÓN TÉCNICA -->
    <meta charset="UTF-8"> <!-- Soporte caracteres españoles -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive mobile -->
    <title>Secretaría - Dashboard Admin</title> <!-- SEO + UX pestaña -->
    
    <!-- RECURSOS EXTERNOS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Iconos -->
    <link rel="stylesheet" href="style_dashboard.css"> <!-- Estilos personalizados -->
</head>

<body>
    <!-- CONTENEDOR PRINCIPAL SECRETARÍA -->
    <div class="dashboard_secretaria_container">
        
        <!-- HEADER GLOBAL - Título + usuario + logout -->
        <?php include 'dashboard_head.php'; ?>

        <!-- BLOQUEO NO-ADMIN - Vista restringida -->
        <?php if (!$is_admin): ?>
            <div class="dashboard_secretaria_no_admin">
                <!-- Icono candado grande + mensaje claro -->
                <i class="fas fa-lock dashboard_secretaria_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>
            <!-- GRID DASHBOARD - 6 cuadros secretaria (responsive) -->
            <div class="dashboard_secretaria_dashboard_grid">
                <?php foreach ($submenus_secretaria as $item):
                    // NORMALIZAR CLAVE CSS - "Solicitud de títulos" → "solicitud_de_titulos"
                    $key = strtolower(str_replace(['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'],
                                        ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
                                        str_replace(' ', '_', $item['titulo'])));
                    
                    // COLORES DINÁMICOS - Primario + secundario gradiente
                    $color_primario = $colores[$key] ?? '#10B981'; // Fallback verde
                    $color_secundario = $color_primario == '#10B981' ? '#059669' : '#0f766e';
                ?>
                    <!-- CUADRO INDIVIDUAL SUBSECCIÓN -->
                    <div class="dashboard_secretaria_cuadro_menu dashboard_secretaria_cuadro_color_<?php echo $key; ?>">
                        
                        <!-- ICONO DESTACADO - Gradiente personal por sección -->
                        <div class="dashboard_secretaria_cuadro_icono dashboard_secretaria_cuadro_icono_color_<?php echo $key; ?>">
                            <i class="fas <?php echo $item['icono']; ?>"></i>
                        </div>
                        
                        <!-- CONTENIDO TEXTO -->
                        <h3 class="dashboard_secretaria_cuadro_titulo">
                            <?php echo htmlspecialchars($item['titulo']); ?>
                        </h3>
                        <p class="dashboard_secretaria_cuadro_desc">
                            <?php echo htmlspecialchars($item['descripcion']); ?>
                        </p>

                        <!-- BOTONES DUALES - Ver + Editar -->
                        <div class="dashboard_secretaria_botones_directos">
                            <!-- VER PÚBLICA - Target blank -->
                            <a href="<?php echo htmlspecialchars($item['enlace']); ?>" 
                               class="dashboard_secretaria_submenu_item" 
                               target="_blank">
                                <i class="fas fa-eye"></i> Ver Página
                            </a>
                            
                            <?php if ($item['dashboard'] && $item['dashboard'] !== '#'): ?>
                                <!-- DASHBOARD EXISTENTE - Enlace directo -->
                                <a href="<?php echo htmlspecialchars($item['dashboard']); ?>" 
                                   class="dashboard_secretaria_submenu_item">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php else: ?>
                                <!-- EN DESARROLLO - Alerta UX -->
                                <a href="#" class="dashboard_secretaria_submenu_item dashboard_secretaria_submenu_en_desarrollo" 
                                   onclick="alert('Dashboard <?= htmlspecialchars($item['titulo']); ?> en desarrollo')">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> <!-- Fin Grid 6 cuadros -->
        <?php endif; ?>

        <!-- NAVEGACIÓN: Volver Dashboard Principal -->
        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </form>
    </div> <!-- Fin contenedor -->
</body>
</html>
