<?php
// DETECTAR PÁGINA ACTUAL
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
    <header class="cabecera-top">
        <div class="cabecera-contenido">
            <img src="img/logo.jpg" alt="Logo IES La Arboleda" class="cabecera-logo">
            <div class="cabecera-texto">
                <h1>Instituto de Educación Secundaria La Arboleda</h1>
                <p>(Alcorcón) · Centro cofinanciado por el FSE</p>
            </div>
        </div>
    </header>

    <nav class="barra-menu">
        <div class="menu-contenedor">
            <a href="index.php" class="item-menu <?php echo isActivePage('index') ? 'activo' : ''; ?>">Inicio</a>

            <div class="item-menu desplegable <?php echo isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos') ? 'activo' : ''; ?>">
                Nuestro centro
                <span class="icono-desplegable">▾</span>
                <div class="submenu">
                    <a href="organigrama.php" class="submenu-titulo <?php echo isActivePage('organigrama') ? 'activo' : ''; ?>">Organigrama</a>
                    <a href="ampa.php" class="submenu-titulo <?php echo isActivePage('ampa') ? 'activo' : ''; ?>">AMPA</a>
                    <!-- NUEVO APARTADO RESULTADOS ACADÉMICOS -->
                    <a href="resultados_academicos.php" class="submenu-titulo <?php echo isActivePage('resultados_academicos') ? 'activo' : ''; ?>">Resultados Académicos</a>
                </div>
            </div>

            <div class="item-menu desplegable <?php echo isActivePage('eso_info') || isActivePage('fp_info') || isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">
                Oferta educativa
                <span class="icono-desplegable">▾</span>
                <div class="submenu">
                    <a href="eso_info.php" class="submenu-titulo <?php echo isActivePage('eso_info') ? 'activo' : ''; ?>">ESO</a>
                    <a href="" class="submenu-titulo">Bachillerato</a>
                    <a href="fp_info.php" class="submenu-titulo <?php echo isActivePage('fp_info') ? 'activo' : ''; ?>">Formación Profesional▾</a>
                    <div class="submenu-anidado">
                        <a href="#">Desarrollo de Aplicaciones Web</a>
                        <a href="#">Administración de Sistemas</a>
                        <a href="desarrollo_videojuegos.php" class="<?php echo isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">Curso Desarrollo de Videojuegos</a>
                    </div>
                </div>
            </div>

            <div class="item-menu desplegable <?php 
                echo (isActivePage('avisos') || 
                      isActiveSubmenu('matriculacion') || 
                      isActiveSubmenu('convalidacion') || 
                      isActiveSubmenu('solicitud_titulo') || 
                      isActivePage('otros_tramites') || 
                      isActivePage('contacto')) ? 'activo' : ''; 
            ?>">
                Secretaría
                <span class="icono-desplegable">▾</span>
                <div class="submenu">
                    <a href="avisos.php" class="submenu-titulo <?php echo isActivePage('avisos') ? 'activo' : ''; ?>">Avisos</a>
                    <a href="#" class="submenu-titulo">Matriculación ▾</a>
                    <div class="submenu-anidado">
                        <a href="matriculacion_eso.php" class="<?php echo isActivePage('matriculacion_eso') ? 'activo' : ''; ?>">Matriculación ESO</a>
                        <a href="matriculacion_bach.php" class="<?php echo isActivePage('matriculacion_bach') ? 'activo' : ''; ?>">Matriculación Bachillerato</a>
                        <a href="matriculacion_fp.php" class="<?php echo isActivePage('matriculacion_fp') ? 'activo' : ''; ?>">Matriculación FP</a>
                    </div> 
                    <a href="#" class="submenu-titulo">Convalidación ▾</a>
                    <div class="submenu-anidado">
                        <a href="convalidacion_eso.php" class="<?php echo isActivePage('convalidacion_eso') ? 'activo' : ''; ?>">Convalidación ESO</a>
                        <a href="convalidacion_bach.php" class="<?php echo isActivePage('convalidacion_bach') ? 'activo' : ''; ?>">Convalidación Bachillerato</a>
                        <a href="convalidacion_fp.php" class="<?php echo isActivePage('convalidacion_fp') ? 'activo' : ''; ?>">Convalidación FP</a>
                    </div> 
                    <a href="#" class="submenu-titulo">Solicitud títulos ▾</a>
                    <div class="submenu-anidado">
                        <a href="solicitud_titulo_eso.php" class="submenu-titulo <?php echo isActivePage('solicitud_titulo_eso') ? 'activo' : ''; ?>">Título ESO</a>
                        <a href="solicitud_titulo_bach.php" class="submenu-titulo <?php echo isActivePage('solicitud_titulo_bach') ? 'activo' : ''; ?>">Título Bachillerato</a>
                        <a href="solicitud_titulo_fp.php" class="submenu-titulo <?php echo isActivePage('solicitud_titulo_fp') ? 'activo' : ''; ?>">Título FP</a>
                    </div> 
                    <a href="otros_tramites.php" class="submenu-titulo <?php echo isActivePage('otros_tramites') ? 'activo' : ''; ?>">Otros trámites</a>
                    <a href="contacto_secretaria.php" class="submenu-titulo <?php echo isActivePage('contacto') ? 'activo' : ''; ?>">Contacto</a>
                </div>
            </div>

            <!-- APARTADO DEPARTAMENTOS -->
            <a href="departamentos.php" class="item-menu <?php echo isActivePage('departamentos') ? 'activo' : ''; ?>">Departamentos</a>

            <a href="erasmus.php" class="item-menu <?php echo isActivePage('erasmus') ? 'activo' : ''; ?>">Erasmus+</a>
            <a href="info_familias.php" class="item-menu <?php echo isActivePage('info_familias') ? 'activo' : ''; ?>">Información familias</a>
            <a href="doc_institucionales.php" class="item-menu <?php echo isActivePage('doc_institucionales') ? 'activo' : ''; ?>">Documentos institucionales</a>
            <a href="orientacion.php" class="item-menu <?php echo isActivePage('orientacion') ? 'activo' : ''; ?>">Orientación</a>

            <button class="nav-toggle" aria-label="Abrir menú">☰</button>
        </div>
    </nav>

    <section class="seccion-breadcrumb">
        <div class="contenedor-max">
            <nav class="breadcrumb-nav" aria-label="Ruta de navegación">
                <a href="index.php" class="breadcrumb-link">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <span class="breadcrumb-separador">/</span>
                
                <?php if (isActivePage('index')): ?>
                    <span class="breadcrumb-link activo">Inicio</span>
                    
                <?php elseif (isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos')): ?>
                    <a href="#" class="breadcrumb-link">Nuestro centro</a>
                    <span class="breadcrumb-separador">/</span>
                    <span class="breadcrumb-link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                    
                <?php elseif (isActivePage('eso_info') || isActivePage('fp_info') || isActivePage('desarrollo_videojuegos')): ?>
                    <a href="#" class="breadcrumb-link">Oferta educativa</a>
                    <span class="breadcrumb-separador">/</span>
                    <span class="breadcrumb-link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                    
                <?php elseif (isActiveSubmenu('matriculacion') || isActiveSubmenu('convalidacion') || isActiveSubmenu('solicitud_titulo') || isActivePage('avisos') || isActivePage('otros_tramites') || isActivePage('contacto')): ?>
                    <a href="#" class="breadcrumb-link">Secretaría</a>
                    <span class="breadcrumb-separador">/</span>
                    
                    <?php if (isActiveSubmenu('matriculacion')): ?>
                        <a href="#" class="breadcrumb-link">Matriculación</a>
                        <span class="breadcrumb-separador">/</span>
                        <span class="breadcrumb-link activo"><?php echo ucwords(str_replace(['matriculacion_', '-'], ' ', $current_page)); ?></span>
                        
                    <?php elseif (isActiveSubmenu('convalidacion')): ?>
                        <a href="#" class="breadcrumb-link">Convalidación</a>
                        <span class="breadcrumb-separador">/</span>
                        <span class="breadcrumb-link activo"><?php echo ucwords(str_replace(['convalidacion_', '-'], ' ', $current_page)); ?></span>
                        
                    <?php elseif (isActiveSubmenu('solicitud_titulo')): ?>
                        <a href="#" class="breadcrumb-link">Solicitud títulos</a>
                        <span class="breadcrumb-separador">/</span>
                        <span class="breadcrumb-link activo"><?php echo ucwords(str_replace(['solicitud_titulo_', '-'], ' ', $current_page)); ?></span>
                        
                    <?php else: ?>
                        <span class="breadcrumb-link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                    <?php endif; ?>
                    
                <?php elseif (isActivePage('departamentos')): ?>
                    <span class="breadcrumb-link activo">Departamentos</span>
                    
                <?php else: ?>
                    <span class="breadcrumb-link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                <?php endif; ?>
            </nav>
        </div>
    </section>
