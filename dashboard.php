<?php
// INICIA SESIÓN - Necesaria para acceder a $_SESSION y mantener login
session_start();

// INCLUYE CONEXIÓN BD - Carga archivo conexion.php con $conexion PDO/MySQLi
include 'conexion.php';

// VERIFICA SESIÓN ACTIVA - Redirige a login si no hay usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');  // Redirige a página de login
    exit;                           // Termina ejecución inmediatamente
}

// DETERMINA SI ES ADMIN - Verifica rol del usuario logueado
$is_admin = ($_SESSION['usuario_rol'] === 'admin');  // true/false

// CONSULTA USUARIOS ACTIVOS - Cuenta usuarios con campo 'activo'=1
$stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE activo = 1");  // Prepara consulta preparada
$stmt->execute();                                                              // Ejecuta consulta
$total_usuarios = $stmt->get_result()->fetch_row()[0];                         // Obtiene número (fila[0])
$stmt->close();                                                                // Libera statement

// ARRAY MENÚ PRINCIPAL - Define 9 secciones del dashboard con iconos, títulos, enlaces
$menu_principal = [
    'inicio' => ['icono' => 'fa-home', 'titulo' => 'Página Inicio', 'descripcion' => 'Página principal del IES La Arboleda', 'enlace' => 'index.php', 'dashboard' => ''],
    'nuestro_centro' => ['icono' => 'fa-building', 'titulo' => 'Nuestro Centro', 'descripcion' => 'Organigrama, AMPA, Resultados Académicos', 'enlace' => '', 'dashboard' => 'dashboard_nuestro_centro.php'],
    'oferta_educativa' => ['icono' => 'fa-graduation-cap', 'titulo' => 'Oferta Educativa', 'descripcion' => 'ESO, Bachillerato, Formación Profesional', 'enlace' => '', 'dashboard' => 'dashboard_oferta_educativa.php'],
    'secretaria' => ['icono' => 'fa-file-alt', 'titulo' => 'Secretaría', 'descripcion' => 'Matriculaciones, convalidaciones, títulos', 'enlace' => '', 'dashboard' => 'dashboard_secretaria.php'],
    'erasmus' => ['icono' => 'fa-plane', 'titulo' => 'Erasmus+', 'descripcion' => 'Programa de movilidad europea', 'enlace' => 'erasmus.php', 'dashboard' => 'dashboard_erasmus.php'],
    'documentos' => ['icono' => 'fa-file-pdf', 'titulo' => 'Documentos Institucionales', 'descripcion' => 'Documentos oficiales del centro', 'enlace' => 'doc_institucionales.php', 'dashboard' => 'dashboard_doc_institucionales.php'],
    'departamentos' => ['icono' => 'fa-users', 'titulo' => 'Departamentos', 'descripcion' => 'Listado de departamentos académicos', 'enlace' => 'departamentos.php', 'dashboard' => ''],
    'familias' => ['icono' => 'fa-user-friends', 'titulo' => 'Información Familias', 'descripcion' => 'Comunicaciones para familias', 'enlace' => 'info_familias.php', 'dashboard' => ''],
    'orientacion' => ['icono' => 'fa-chalkboard-teacher', 'titulo' => 'Orientación', 'descripcion' => 'Departamento de orientación', 'enlace' => 'orientacion.php', 'dashboard' => '']
];


?>

<!DOCTYPE html>  <!-- DOCTYPE HTML5 -->
<html lang="es"> <!-- Página en español -->
<head>
    <!-- CHARSET UTF-8 - Soporte caracteres especiales (ñ, acentos) -->
    <meta charset="UTF-8">
    <!-- VIEWPORT RESPONSIVE - Escala móvil correctamente -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- TÍTULO PÁGINA - Muestra en pestaña navegador -->
    <title>Dashboard Admin - IES La Arboleda</title>
    <!-- FONT AWESOME 6 - Iconos gratuitos (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- CSS PROPIO - Estilos específicos del dashboard -->
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <!-- CONTENEDOR PRINCIPAL - Wrapper de todo el dashboard -->
    <div class="dashboard_inicio_container">

        <!-- HEADER GLOBAL - Incluye navbar superior con usuario/logout -->
        <?php include 'dashboard_head.php'; ?>

        <!-- BLOQUEO NO-ADMIN - Muestra mensaje si usuario NO es admin -->
        <?php if (!$is_admin): ?>
            <div class="dashboard_inicio_no_admin">
                <!-- ÍCONEO CANDADO - Visual indica restricción -->
                <i class="fas fa-lock dashboard_inicio_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
                <!-- MUESTRA ROL ACTUAL - Ej: "Profesor", "Secretario" -->
                <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
            </div>
        <?php else: ?>  <!-- SI ES ADMIN: muestra dashboard completo -->

            <!-- Tarjeta con contador usuarios activos -->
            <div class="dashboard_inicio_stats_grid">
                <div class="dashboard_inicio_stat_card">
                    <!-- NÚMERO USUARIOS - Variable PHP desde BD -->
                    <div class="dashboard_inicio_stat_number"><?php echo $total_usuarios; ?></div>
                    <div class="dashboard_inicio_stat_label">Total Usuarios</div>
                    <!-- BOTÓN GESTIÓN - Enlace directo a gestión usuarios -->
                    <a href="dashboard_usuarios.php" class="dashboard_inicio_boton_gestion">
                        <i class="fas fa-users-cog"></i> Gestión de Usuarios
                    </a>
                </div>
            </div>

            <!-- GRID MENÚ PRINCIPAL - 9 cuadros navegables -->
            <div class="dashboard_inicio_dashboard_grid">
                <!-- LOOP FOREACH - Genera 9 cuadros dinámicamente desde $menu_principal -->
                <?php foreach ($menu_principal as $key => $item): ?>
                    <!-- CUADRO INDIVIDUAL - Cada sección del menú -->
                    <div class="dashboard_inicio_cuadro_menu dashboard_inicio_cuadro_menu_<?php echo $key; ?>">
                        <!-- ÍCONEO COLOR GRADIENTE - Fondo con gradiente del color de sección -->
                        <div class="dashboard_inicio_cuadro_icono dashboard_inicio_cuadro_icono_<?php echo $key; ?>">
                            <i class="fas <?php echo $item['icono']; ?>"></i>  <!-- Icono FontAwesome -->
                        </div>
                        <!-- TÍTULO SECCIÓN - Nombre principal -->
                        <h3 class="dashboard_inicio_cuadro_titulo"><?php echo $item['titulo']; ?></h3>
                        <!-- DESCRIPCIÓN - Texto explicativo breve -->
                        <p class="dashboard_inicio_cuadro_desc"><?php echo $item['descripcion']; ?></p>

                        <!-- BOTONES ACCIÓN - 1 o 2 botones por sección -->
                        <div class="dashboard_inicio_botones_directos">
                            <!-- BOTÓN "VER PÁGINA" - Si existe enlace público -->
                            <?php if (!empty($item['enlace'])): ?>
                                <a href="<?php echo $item['enlace']; ?>" class="dashboard_inicio_submenu_item dashboard_inicio_boton_ver_pagina" target="_blank">
                                    <i class="fas fa-eye"></i> Ver Página
                                </a>
                            <?php endif; ?>

                            <!-- BOTÓN "EDITAR" - Dashboard específico o placeholder -->
                            <?php if (!empty($item['dashboard'])): ?>
                                <!-- DASHBOARD EXISTE - Enlace real -->
                                <a href="<?php echo $item['dashboard']; ?>" class="dashboard_inicio_submenu_item dashboard_inicio_boton_editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php else: ?>
                                <!-- SIN DASHBOARD - Muestra alerta "en desarrollo" -->
                                <a href="#" class="dashboard_inicio_submenu_item dashboard_inicio_boton_editar_placeholder" onclick="alert('Editar <?php echo $item['titulo']; ?> en desarrollo')">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>  <!-- Fin loop de 9 cuadros -->
            </div>
        <?php endif; ?>  <!-- Fin condición admin -->
    </div>  <!-- Fin contenedor principal -->
</body>
</html>
