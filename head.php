<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF'], '.php');

function isActivePage($page) {
    global $current_page;
    return $current_page === $page;
}

function isActiveSubmenu($submenu) {
    global $current_page;
    return strpos($current_page, $submenu) !== false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IES La Arboleda</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- HEADER ORIGINAL -->
    <header class="header-principal">
        <div class="header-contenido">
            <img src="img/logo.jpg" alt="Logo IES La Arboleda" class="logo-header">
            <div>
                <h1 class="titulo-header">Instituto de Educación Secundaria La Arboleda</h1>
                <p class="subtitulo-header">(Alcorcón) · Centro cofinanciado por el FSE</p>
            </div>
        </div>
    </header>

    <!-- NAVEGACIÓN COMPLETA ORIGINAL -->
    <nav class="navegacion-principal">
        <div class="contenedor-navegacion">
            <a href="index.php" class="enlace-navegacion <?php echo isActivePage('index') ? 'activo' : ''; ?>">Inicio</a>

            <div class="contenedor-desplegable <?php echo isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos') ? 'activo' : ''; ?>">
                <a href="#" class="enlace-navegacion">Nuestro centro<span class="icono-desplegable">▾</span></a>
                <div class="menu-desplegable">
                    <a href="organigrama.php" class="enlace-desplegable <?php echo isActivePage('organigrama') ? 'activo' : ''; ?>">Organigrama</a>
                    <a href="ampa.php" class="enlace-desplegable <?php echo isActivePage('ampa') ? 'activo' : ''; ?>">AMPA</a>
                    <a href="resultados_academicos.php" class="enlace-desplegable <?php echo isActivePage('resultados_academicos') ? 'activo' : ''; ?>">Resultados Académicos</a>
                </div>
            </div>

            <div class="contenedor-desplegable <?php echo isActivePage('info_eso') || isActivePage('info_fp') || isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">
                <a href="#" class="enlace-navegacion">Oferta educativa<span class="icono-desplegable">▾</span></a>
                <div class="menu-desplegable">
                    <a href="info_eso.php" class="enlace-desplegable <?php echo isActivePage('info_eso') ? 'activo' : ''; ?>">ESO</a>
                    <a href="#" class="enlace-desplegable titulo-desplegable">Bachillerato▾</a>
                    <a href="info_fp.php" class="enlace-desplegable <?php echo isActivePage('info_fp') ? 'activo' : ''; ?>">Formación Profesional▾</a>
                    <div class="submenu-anidado">
                        <a href="#" class="enlace-desplegable">Desarrollo de Aplicaciones Web</a>
                        <a href="#" class="enlace-desplegable">Administración de Sistemas</a>
                        <a href="desarrollo_videojuegos.php" class="enlace-desplegable <?php echo isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">Curso Desarrollo de Videojuegos</a>
                    </div>
                </div>
            </div>

            <div class="contenedor-desplegable <?php
            echo (isActivePage('avisos') ||
            isActiveSubmenu('matriculacion') ||
            isActiveSubmenu('convalidacion') ||
            isActiveSubmenu('solicitud_titulo') ||
            isActivePage('otros_tramites') ||
            isActivePage('contacto')) ? 'activo' : '';
            ?>">
                <a href="#" class="enlace-navegacion">Secretaría<span class="icono-desplegable">▾</span></a>
                <div class="menu-desplegable">
                    <a href="avisos.php" class="enlace-desplegable <?php echo isActivePage('avisos') ? 'activo' : ''; ?>">Avisos</a>
                    <a href="#" class="enlace-desplegable titulo-desplegable">Matriculación ▾</a>
                    <div class="submenu-anidado">
                        <a href="matriculacion_eso.php" class="enlace-desplegable <?php echo isActivePage('matriculacion_eso') ? 'activo' : ''; ?>">Matriculación ESO</a>
                        <a href="matriculacion_bach.php" class="enlace-desplegable <?php echo isActivePage('matriculacion_bach') ? 'activo' : ''; ?>">Matriculación Bachillerato</a>
                        <a href="matriculacion_fp.php" class="enlace-desplegable <?php echo isActivePage('matriculacion_fp') ? 'activo' : ''; ?>">Matriculación FP</a>
                    </div>
                    <a href="#" class="enlace-desplegable titulo-desplegable">Convalidación ▾</a>
                    <div class="submenu-anidado">
                        <a href="convalidacion_eso.php" class="enlace-desplegable <?php echo isActivePage('convalidacion_eso') ? 'activo' : ''; ?>">Convalidación ESO</a>
                        <a href="convalidacion_bach.php" class="enlace-desplegable <?php echo isActivePage('convalidacion_bach') ? 'activo' : ''; ?>">Convalidación Bachillerato</a>
                        <a href="convalidacion_fp.php" class="enlace-desplegable <?php echo isActivePage('convalidacion_fp') ? 'activo' : ''; ?>">Convalidación FP</a>
                    </div>
                    <a href="#" class="enlace-desplegable titulo-desplegable">Solicitud títulos ▾</a>
                    <div class="submenu-anidado">
                        <a href="solicitud_titulo_eso.php" class="enlace-desplegable <?php echo isActivePage('solicitud_titulo_eso') ? 'activo' : ''; ?>">Título ESO</a>
                        <a href="solicitud_titulo_bach.php" class="enlace-desplegable <?php echo isActivePage('solicitud_titulo_bach') ? 'activo' : ''; ?>">Título Bachillerato</a>
                        <a href="solicitud_titulo_fp.php" class="enlace-desplegable <?php echo isActivePage('solicitud_titulo_fp') ? 'activo' : ''; ?>">Título FP</a>
                    </div>
                    <a href="otros_tramites.php" class="enlace-desplegable <?php echo isActivePage('otros_tramites') ? 'activo' : ''; ?>">Otros trámites</a>
                    <a href="contacto_secretaria.php" class="enlace-desplegable <?php echo isActivePage('contacto') ? 'activo' : ''; ?>">Contacto</a>
                </div>
            </div>

            <!-- APARTADO DEPARTAMENTOS -->
            <a href="departamentos.php" class="enlace-navegacion <?php echo isActivePage('departamentos') ? 'activo' : ''; ?>">Departamentos</a>

            <a href="erasmus.php" class="enlace-navegacion <?php echo isActivePage('erasmus') ? 'activo' : ''; ?>">Erasmus+</a>
            <a href="info_familias.php" class="enlace-navegacion <?php echo isActivePage('info_familias') ? 'activo' : ''; ?>">Información familias</a>
            <a href="doc_institucionales.php" class="enlace-navegacion <?php echo isActivePage('doc_institucionales') ? 'activo' : ''; ?>">Documentos institucionales</a>
            <a href="orientacion.php" class="enlace-navegacion <?php echo isActivePage('orientacion') ? 'activo' : ''; ?>">Orientación</a>

            <button class="boton-menu-movil" aria-label="Abrir menú">☰</button>
        </div>
    </nav>

    <!-- BREADCRUMB ORIGINAL COMPLETO -->
    <nav class="breadcrumb" aria-label="Ruta de navegación">
        <div class="breadcrumb-contenido">
            <a href="index.php" class="enlace-breadcrumb">
                <i class="fas fa-home"></i> Inicio
            </a>
            <span class="separador-breadcrumb">/</span>

            <?php if (isActivePage('index')): ?>
                <span class="enlace-breadcrumb activo">Inicio</span>

            <?php elseif (isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos')): ?>
                <a href="#" class="enlace-breadcrumb">Nuestro centro</a>
                <span class="separador-breadcrumb">/</span>
                <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>

            <?php elseif (isActivePage('info_eso') || isActivePage('info_fp') || isActivePage('desarrollo_videojuegos')): ?>
                <a href="#" class="enlace-breadcrumb">Oferta educativa</a>
                <span class="separador-breadcrumb">/</span>
                <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>

            <?php elseif (isActiveSubmenu('matriculacion') || isActiveSubmenu('convalidacion') || isActiveSubmenu('solicitud_titulo') || isActivePage('avisos') || isActivePage('otros_tramites') || isActivePage('contacto')): ?>
                <a href="#" class="enlace-breadcrumb">Secretaría</a>
                <span class="separador-breadcrumb">/</span>

                <?php if (isActiveSubmenu('matriculacion')): ?>
                    <a href="#" class="enlace-breadcrumb">Matriculación</a>
                    <span class="separador-breadcrumb">/</span>
                    <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace(['matriculacion_', '-'], ' ', $current_page)); ?></span>

                <?php elseif (isActiveSubmenu('convalidacion')): ?>
                    <a href="#" class="enlace-breadcrumb">Convalidación</a>
                    <span class="separador-breadcrumb">/</span>
                    <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace(['convalidacion_', '-'], ' ', $current_page)); ?></span>

                <?php elseif (isActiveSubmenu('solicitud_titulo')): ?>
                    <a href="#" class="enlace-breadcrumb">Solicitud títulos</a>
                    <span class="separador-breadcrumb">/</span>
                    <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace(['solicitud_titulo_', '-'], ' ', $current_page)); ?></span>

                <?php else: ?>
                    <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                <?php endif; ?>

            <?php elseif (isActivePage('departamentos')): ?>
                <span class="enlace-breadcrumb activo">Departamentos</span>

            <?php else: ?>
                <span class="enlace-breadcrumb activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
            <?php endif; ?>
        </div>
    </nav>
