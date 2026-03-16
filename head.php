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
        <!-- En head.php
<div id="idiomas-global" style="position:fixed;top:20px;right:20px;z-index:9999">
<select onchange="window.location='?lang='+this.value" style="padding:10px;border-radius:20px;border:2px solid #667eea;background:white;font-weight:600">
    <option value="es" <?=($_SESSION['idioma']??'es')=='es'?'selected':''?>>🇪🇸 ES</option>
    <option value="en" <?=($_SESSION['idioma']??'es')=='en'?'selected':''?>>🇺🇸 EN</option>
    <option value="fr" <?=($_SESSION['idioma']??'es')=='fr'?'selected':''?>>🇫🇷 FR</option>
</select>
</div> -->

        <!-- HEADER PRINCIPAL -->
        <header class="header_pagina_header_top">
            <div class="header_pagina_header_contenido">
                <img src="img/logo.jpg" alt="Logo IES La Arboleda" class="header_pagina_header_logo">
                <div class="header_pagina_header_texto">
                    <h1>Instituto de Educación Secundaria La Arboleda</h1>
                    <p>(Alcorcón) · Centro cofinanciado por el FSE</p>
                </div>
            </div>
        </header>

        <!-- NAVEGACIÓN PRINCIPAL -->
        <nav class="header_pagina_barra_menu">
            <div class="header_pagina_menu_contenedor">
                <a href="index.php" class="header_pagina_item_menu <?php echo isActivePage('index') ? 'activo' : ''; ?>">Inicio</a>

                <div class="header_pagina_item_menu header_pagina_desplegable <?php echo isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos') ? 'activo' : ''; ?>">
                    Nuestro centro
                    <span class="header_pagina_icono_desplegable">▾</span>
                    <div class="header_pagina_submenu">
                        <a href="organigrama.php" class="header_pagina_submenu_titulo <?php echo isActivePage('organigrama') ? 'activo' : ''; ?>">Organigrama</a>
                        <a href="ampa.php" class="header_pagina_submenu_titulo <?php echo isActivePage('ampa') ? 'activo' : ''; ?>">AMPA</a>
                        <a href="resultados_academicos.php" class="header_pagina_submenu_titulo <?php echo isActivePage('resultados_academicos') ? 'activo' : ''; ?>">Resultados Académicos</a>
                        <a href="bolsa_empleo.php" class="header_pagina_submenu_titulo <?php echo isActivePage('bolsa_empleo') ? 'activo' : ''; ?>">Bolsa de empleo</a>
                        <a href="teatro.php" class="header_pagina_submenu_titulo <?php echo isActivePage('teatro') ? 'activo' : ''; ?>">Teatro</a>
                        <a href="plan_igualdad.php" class="header_pagina_submenu_titulo <?php echo isActivePage('plan_igualdad') ? 'activo' : ''; ?>">Plan de igualdad</a>



                    </div>
                </div>

                <!-- OFERTA EDUCATIVA  -->
                <div class="header_pagina_item_menu header_pagina_desplegable <?php echo isActivePage('eso_info') || isActivePage('fp_info') || isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">
                    Oferta educativa
                    <span class="header_pagina_icono_desplegable">▾</span>
                    <div class="header_pagina_submenu">
                        <a href="info_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('eso_info') ? 'activo' : ''; ?>">ESO</a>
                        <a href="info_bachillerato.php" class="header_pagina_submenu_titulo">Bachillerato</a>
                        <a href="info_fp.php" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable <?php echo isActivePage('fp_info') ? 'activo' : ''; ?>">Formación Profesional▾</a>
                        <div class="header_pagina_submenu_anidado">
                            <a href="https://www.comunidad.madrid/sites/default/files/ifces02_desarrollo_de_videojuegos_y_realidad_virtual.pdf" class="header_pagina_submenu_titulo <?php echo isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">Curso Desarrollo de Videojuegos</a>
                        </div>
                    </div>
                </div>

                <div class="header_pagina_item_menu header_pagina_desplegable <?php
                echo (isActivePage('avisos') ||
                isActiveSubmenu('matriculacion') ||
                isActiveSubmenu('convalidacion') ||
                isActiveSubmenu('solicitud_titulo') ||
                isActivePage('otros_tramites') ||
                isActivePage('contacto')) ? 'activo' : '';
                ?>">
                    Secretaría
                    <span class="header_pagina_icono_desplegable">▾</span>
                    <div class="header_pagina_submenu">
                        <a href="avisos.php" class="header_pagina_submenu_titulo <?php echo isActivePage('avisos') ? 'activo' : ''; ?>">Avisos</a>
                        <a href="#" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable">Matriculación ▾</a>
                        <div class="header_pagina_submenu_anidado">
                            <a href="matriculacion_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('matriculacion_eso') ? 'activo' : ''; ?>">Matriculación ESO</a>
                            <a href="matriculacion_bach.php" class="header_pagina_submenu_titulo <?php echo isActivePage('matriculacion_bach') ? 'activo' : ''; ?>">Matriculación Bachillerato</a>
                            <a href="matriculacion_fp.php" class="header_pagina_submenu_titulo <?php echo isActivePage('matriculacion_fp') ? 'activo' : ''; ?>">Matriculación FP</a>
                        </div> 
                        <a href="#" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable">Convalidación ▾</a>
                        <div class="header_pagina_submenu_anidado">
                            <a href="convalidacion_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('convalidacion_eso') ? 'activo' : ''; ?>">Convalidación ESO</a>
                            <a href="convalidacion_bach.php" class="header_pagina_submenu_titulo <?php echo isActivePage('convalidacion_bach') ? 'activo' : ''; ?>">Convalidación Bachillerato</a>
                            <a href="convalidacion_fp.php" class="header_pagina_submenu_titulo <?php echo isActivePage('convalidacion_fp') ? 'activo' : ''; ?>">Convalidación FP</a>
                        </div> 
                        <a href="#" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable">Solicitud títulos ▾</a>
                        <div class="header_pagina_submenu_anidado">
                            <a href="solicitud_titulo_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('solicitud_titulo_eso') ? 'activo' : ''; ?>">Título ESO</a>
                            <a href="solicitud_titulo_bach.php" class="header_pagina_submenu_titulo <?php echo isActivePage('solicitud_titulo_bach') ? 'activo' : ''; ?>">Título Bachillerato</a>
                            <a href="solicitud_titulo_fp.php" class="header_pagina_submenu_titulo <?php echo isActivePage('solicitud_titulo_fp') ? 'activo' : ''; ?>">Título FP</a>
                        </div> 
                        <a href="otros_tramites.php" class="header_pagina_submenu_titulo <?php echo isActivePage('otros_tramites') ? 'activo' : ''; ?>">Otros trámites</a>
                        <a href="contacto_secretaria.php" class="header_pagina_submenu_titulo <?php echo isActivePage('contacto') ? 'activo' : ''; ?>">Contacto</a>
                    </div>
                </div>

                <a href="departamentos.php" class="header_pagina_item_menu <?php echo isActivePage('departamentos') ? 'activo' : ''; ?>">Departamentos</a>
                <a href="erasmus.php" class="header_pagina_item_menu <?php echo isActivePage('erasmus') ? 'activo' : ''; ?>">Erasmus+</a>
                <a href="info_familias.php" class="header_pagina_item_menu <?php echo isActivePage('info_familias') ? 'activo' : ''; ?>">Información familias</a>
                <a href="doc_institucionales.php" class="header_pagina_item_menu <?php echo isActivePage('doc_institucionales') ? 'activo' : ''; ?>">Documentos institucionales</a>
                <a href="orientacion.php" class="header_pagina_item_menu <?php echo isActivePage('orientacion') ? 'activo' : ''; ?>">Orientación</a>
                <button class="header_pagina_nav_toggle" aria-label="Abrir menú">☰</button>
            </div>
        </nav>

        <!-- BREADCRUMB -->
<section class="header_pagina_seccion_breadcrumb">
    <div class="header_pagina_contenedor_max">
        <nav class="header_pagina_breadcrumb_nav" aria-label="Ruta de navegación">
            <a href="index.php" class="header_pagina_breadcrumb_link">
                <i class="fas fa-home"></i> Inicio
            </a>
            <span class="header_pagina_breadcrumb_separador">/</span>

            <?php if (isActivePage('index')): ?>
                <span class="header_pagina_breadcrumb_link activo">Inicio</span>

            <!-- NUESTRO CENTRO -->
            <?php elseif (isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos') || isActivePage('plan_igualdad') || isActivePage('bolsa_empleo') || isActivePage('teatro')): ?>
                <a href="#" class="header_pagina_breadcrumb_link">Nuestro centro</a>
                <span class="header_pagina_breadcrumb_separador">/</span>
                <?php if (isActivePage('organigrama')): ?>
                    <span class="header_pagina_breadcrumb_link activo">Organigrama</span>
                <?php elseif (isActivePage('ampa')): ?>
                    <span class="header_pagina_breadcrumb_link activo">AMPA</span>
                <?php elseif (isActivePage('resultados_academicos')): ?>
                    <span class="header_pagina_breadcrumb_link activo">Resultados Académicos</span>
                <?php elseif (isActivePage('bolsa_empleo')): ?>
                    <span class="header_pagina_breadcrumb_link activo">Bolsa de Empleo</span>
                <?php elseif (isActivePage('teatro')): ?>
                    <span class="header_pagina_breadcrumb_link activo">Teatro</span>
                <?php elseif (isActivePage('plan_igualdad')): ?>
                    <span class="header_pagina_breadcrumb_link activo">Plan de Igualdad</span>
                <?php endif; ?>

            <!-- OFERTA EDUCATIVA -->
            <?php elseif (isActivePage('eso_info') || isActivePage('fp_info') || isActivePage('desarrollo_videojuegos')): ?>
                <a href="#" class="header_pagina_breadcrumb_link">Oferta educativa</a>
                <span class="header_pagina_breadcrumb_separador">/</span>
                <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>

            <!-- SECRETARÍA -->
            <?php elseif (isActiveSubmenu('matriculacion') || isActiveSubmenu('convalidacion') || isActiveSubmenu('solicitud_titulo') || isActivePage('avisos') || isActivePage('otros_tramites') || isActivePage('contacto')): ?>
                <a href="#" class="header_pagina_breadcrumb_link">Secretaría</a>
                <span class="header_pagina_breadcrumb_separador">/</span>

                <?php if (isActiveSubmenu('matriculacion')): ?>
                    <a href="#" class="header_pagina_breadcrumb_link">Matriculación</a>
                    <span class="header_pagina_breadcrumb_separador">/</span>
                    <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace(['matriculacion_', '-'], ' ', $current_page)); ?></span>

                <?php elseif (isActiveSubmenu('convalidacion')): ?>
                    <a href="#" class="header_pagina_breadcrumb_link">Convalidación</a>
                    <span class="header_pagina_breadcrumb_separador">/</span>
                    <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace(['convalidacion_', '-'], ' ', $current_page)); ?></span>

                <?php elseif (isActiveSubmenu('solicitud_titulo')): ?>
                    <a href="#" class="header_pagina_breadcrumb_link">Solicitud títulos</a>
                    <span class="header_pagina_breadcrumb_separador">/</span>
                    <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace(['solicitud_titulo_', '-'], ' ', $current_page)); ?></span>

                <?php else: ?>
                    <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                <?php endif; ?>

            <!-- DEPARTAMENTOS -->
            <?php elseif (isActivePage('departamentos')): ?>
                <span class="header_pagina_breadcrumb_link activo">Departamentos</span>

            <!-- OTROS -->
            <?php else: ?>
                <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
            <?php endif; ?>
        </nav>
        
    </div>
</section>
        
  


