<?php
// INICIA SESIÓN SI NO ESTÁ ACTIVA - Evita warnings si ya existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Inicia sesión PHP solo si no está activa
}

// OBTIENE NOMBRE ARCHIVO ACTUAL - Para resaltar menú activo (sin extensión .php)
$current_page = basename($_SERVER['PHP_SELF'], '.php');  // Ej: "index.php" → "index"

// FUNCIÓN: Verifica si página actual coincide exactamente con parámetro
function isActivePage($page) {
    global $current_page;           // Accede a variable global
    return $current_page === $page; // Devuelve true/false (comparación estricta)
}

// FUNCIÓN: Verifica si página actual contiene subcadena (para submenús)
function isActiveSubmenu($submenu) {
    global $current_page;                // Accede a variable global
    return strpos($current_page, $submenu) !== false;  // true si contiene texto
}
?>

<!DOCTYPE html>  <!-- DOCTYPE HTML5 estándar -->
<html lang="es"> <!-- Idioma español para SEO/accesibilidad -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IES La Arboleda</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style_bot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>  
   
    <!-- HEADER PRINCIPAL -->
    <header class="header_pagina_header_top">
        <div class="header_pagina_header_contenido">
            <!-- LOGO INSTITUTO - Imagen fija del IES La Arboleda -->
            <img src="img/logo.jpg" alt="Logo IES La Arboleda" class="header_pagina_header_logo">
            <!-- TEXTO INSTITUTO - Nombre + ubicación + FSE -->
            <div class="header_pagina_header_texto">
                <h1>Instituto de Educación Secundaria La Arboleda</h1>
                <p>(Alcorcón) · Centro cofinanciado por el FSE</p>
            </div>
        </div>
    </header>

    <!-- NAVEGACIÓN PRINCIPAL - Menú hamburguesa responsive con submenús -->
    <nav class="header_pagina_barra_menu">
        <div class="header_pagina_menu_contenedor">
            <!-- INICIO - Enlace directo, resalta si página actual = index -->
            <a href="index.php" class="header_pagina_item_menu <?php echo isActivePage('index') ? 'activo' : ''; ?>">Inicio</a>

            <!-- DESPLEGABLE "NUESTRO CENTRO" - 6 subpáginas -->
            <div class="header_pagina_item_menu header_pagina_desplegable <?php echo isActivePage('organigrama') || isActivePage('ampa') || isActivePage('resultados_academicos') ? 'activo' : ''; ?>">
                Nuestro centro  <!-- Título principal desplegable -->
                <span class="header_pagina_icono_desplegable">▾</span>  <!-- Flecha dropdown CSS/JS -->
                <div class="header_pagina_submenu">  <!-- CONTENIDO SUBMENÚ -->
                    <a href="organigrama.php" class="header_pagina_submenu_titulo <?php echo isActivePage('organigrama') ? 'activo' : ''; ?>">Organigrama</a>
                    <a href="ampa.php" class="header_pagina_submenu_titulo <?php echo isActivePage('ampa') ? 'activo' : ''; ?>">AMPA</a>
                    <a href="resultados_academicos.php" class="header_pagina_submenu_titulo <?php echo isActivePage('resultados_academicos') ? 'activo' : ''; ?>">Resultados Académicos</a>
                    <a href="bolsa_empleo.php" class="header_pagina_submenu_titulo <?php echo isActivePage('bolsa_empleo') ? 'activo' : ''; ?>">Bolsa de empleo</a>
                    <a href="teatro.php" class="header_pagina_submenu_titulo <?php echo isActivePage('teatro') ? 'activo' : ''; ?>">Teatro</a>
                    <a href="plan_igualdad.php" class="header_pagina_submenu_titulo <?php echo isActivePage('plan_igualdad') ? 'activo' : ''; ?>">Plan de igualdad</a>
                </div>
            </div>

            <!-- DESPLEGABLE "OFERTA EDUCATIVA" - ESO, Bachillerato, FP con sub-submenú -->
            <div class="header_pagina_item_menu header_pagina_desplegable <?php echo isActivePage('eso_info') || isActivePage('fp_info') || isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">
                Oferta educativa
                <span class="header_pagina_icono_desplegable">▾</span>
                <div class="header_pagina_submenu">
                    <a href="info_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('eso_info') ? 'activo' : ''; ?>">ESO</a>
                    <a href="info_bachillerato.php" class="header_pagina_submenu_titulo">Bachillerato</a>
                    <!-- FP CON SUBMENÚ ANIDADO - Desarrollo Videojuegos PDF externo -->
                    <a href="info_fp.php" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable <?php echo isActivePage('fp_info') ? 'activo' : ''; ?>">Formación Profesional▾</a>
                    <div class="header_pagina_submenu_anidado">  <!-- Sub-submenú nivel 3 -->
                        <a href="https://www.comunidad.madrid/sites/default/files/ifces02_desarrollo_de_videojuegos_y_realidad_virtual.pdf" class="header_pagina_submenu_titulo <?php echo isActivePage('desarrollo_videojuegos') ? 'activo' : ''; ?>">Curso Desarrollo de Videojuegos</a>
                    </div>
                </div>
            </div>

            <!-- DESPLEGABLE "SECRETARÍA" - Complejo con 3 niveles de submenús -->
            <div class="header_pagina_item_menu header_pagina_desplegable <?php
            // CONDICIÓN COMPLEJA - Activa si cualquier página de secretaría está activa
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
                    
                    <!-- MATRICULACIÓN - Submenú anidado ESO/Bach/FP -->
                    <a href="#" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable">Matriculación ▾</a>
                    <div class="header_pagina_submenu_anidado">
                        <a href="matriculacion_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('matriculacion_eso') ? 'activo' : ''; ?>">Matriculación ESO</a>
                        <a href="matriculacion_bach.php" class="header_pagina_submenu_titulo <?php echo isActivePage('matriculacion_bach') ? 'activo' : ''; ?>">Matriculación Bachillerato</a>
                        <a href="matriculacion_fp.php" class="header_pagina_submenu_titulo <?php echo isActivePage('matriculacion_fp') ? 'activo' : ''; ?>">Matriculación FP</a>
                    </div> 
                    
                    <!-- CONVALIDACIÓN - Submenú anidado ESO/Bach/FP -->
                    <a href="#" class="header_pagina_submenu_titulo header_pagina_titulo_desplegable">Convalidación ▾</a>
                    <div class="header_pagina_submenu_anidado">
                        <a href="convalidacion_eso.php" class="header_pagina_submenu_titulo <?php echo isActivePage('convalidacion_eso') ? 'activo' : ''; ?>">Convalidación ESO</a>
                        <a href="convalidacion_bach.php" class="header_pagina_submenu_titulo <?php echo isActivePage('convalidacion_bach') ? 'activo' : ''; ?>">Convalidación Bachillerato</a>
                        <a href="convalidacion_fp.php" class="header_pagina_submenu_titulo <?php echo isActivePage('convalidacion_fp') ? 'activo' : ''; ?>">Convalidación FP</a>
                    </div> 
                    
                    <!-- SOLICITUD TÍTULOS - Submenú anidado ESO/Bach/FP -->
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

            <!-- ENLACES SIMPLES - Sin submenús, resaltan si página activa -->
            <a href="departamentos.php" class="header_pagina_item_menu <?php echo isActivePage('departamentos') ? 'activo' : ''; ?>">Departamentos</a>
            <a href="erasmus.php" class="header_pagina_item_menu <?php echo isActivePage('erasmus') ? 'activo' : ''; ?>">Erasmus+</a>
            <a href="info_familias.php" class="header_pagina_item_menu <?php echo isActivePage('info_familias') ? 'activo' : ''; ?>">Información familias</a>
            <a href="doc_institucionales.php" class="header_pagina_item_menu <?php echo isActivePage('doc_institucionales') ? 'activo' : ''; ?>">Documentos institucionales</a>
            <a href="orientacion.php" class="header_pagina_item_menu <?php echo isActivePage('orientacion') ? 'activo' : ''; ?>">Orientación</a>
            <!-- HAMBURGUESA MÓVIL - Botón ☰ para menú responsive en móvil -->
            <button class="header_pagina_nav_toggle" aria-label="Abrir menú">☰</button>
        </div>
    </nav>

    <!-- BREADCRUMB - Ruta de navegación dinámica (Inicio > Sección > Subsección) -->
    <section class="header_pagina_seccion_breadcrumb">
        <div class="header_pagina_contenedor_max">
            <nav class="header_pagina_breadcrumb_nav" aria-label="Ruta de navegación">
                <!-- SIEMPRE INICIO - Enlace fijo a home -->
                <a href="index.php" class="header_pagina_breadcrumb_link">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <span class="header_pagina_breadcrumb_separador">/</span>  <!-- Separador visual "/ " -->

                <!-- LÓGICA BREADCRUMB - Condicionales PHP por sección -->
                <?php if (isActivePage('index')): ?>  <!-- PÁGINA INICIO -->
                    <span class="header_pagina_breadcrumb_link activo">Inicio</span>

                <!-- NUESTRO CENTRO - 6 páginas específicas -->
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

                <!-- OFERTA EDUCATIVA - ESO/FP simplificado -->
                <?php elseif (isActivePage('eso_info') || isActivePage('fp_info') || isActivePage('desarrollo_videojuegos')): ?>
                    <a href="#" class="header_pagina_breadcrumb_link">Oferta educativa</a>
                    <span class="header_pagina_breadcrumb_separador">/</span>
                    <!-- FORMATEA NOMBRE ARCHIVO - "eso_info" → "Eso Info" -->
                    <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>

                <!-- SECRETARÍA - Lógica compleja con sub-submenús -->
                <?php elseif (isActiveSubmenu('matriculacion') || isActiveSubmenu('convalidacion') || isActiveSubmenu('solicitud_titulo') || isActivePage('avisos') || isActivePage('otros_tramites') || isActivePage('contacto')): ?>
                    <a href="#" class="header_pagina_breadcrumb_link">Secretaría</a>
                    <span class="header_pagina_breadcrumb_separador">/</span>

                    <?php if (isActiveSubmenu('matriculacion')): ?>  <!-- MATRICULACIÓN 3 niveles -->
                        <a href="#" class="header_pagina_breadcrumb_link">Matriculación</a>
                        <span class="header_pagina_breadcrumb_separador">/</span>
                        <!-- FORMATEA: "matriculacion_eso" → "Matriculacion Eso" -->
                        <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace(['matriculacion_', '-'], ' ', $current_page)); ?></span>

                    <?php elseif (isActiveSubmenu('convalidacion')): ?>  <!-- CONVALIDACIÓN 3 niveles -->
                        <a href="#" class="header_pagina_breadcrumb_link">Convalidación</a>
                        <span class="header_pagina_breadcrumb_separador">/</span>
                        <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace(['convalidacion_', '-'], ' ', $current_page)); ?></span>

                    <?php elseif (isActiveSubmenu('solicitud_titulo')): ?>  <!-- TÍTULOS 3 niveles -->
                        <a href="#" class="header_pagina_breadcrumb_link">Solicitud títulos</a>
                        <span class="header_pagina_breadcrumb_separador">/</span>
                        <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace(['solicitud_titulo_', '-'], ' ', $current_page)); ?></span>

                    <?php else: ?>  <!-- AVISOS/OTROS/CONTACTO - 2 niveles -->
                        <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                    <?php endif; ?>

                <!-- DEPARTAMENTOS - Página simple -->
                <?php elseif (isActivePage('departamentos')): ?>
                    <span class="header_pagina_breadcrumb_link activo">Departamentos</span>

                <!-- DEFAULT - Cualquier otra página -->
                <?php else: ?>
                    <span class="header_pagina_breadcrumb_link activo"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></span>
                <?php endif; ?>
            </nav>
        </div>
    </section>

    <!-- ARBOLEDA BOT - Chat IA con WhatsApp notifications (JS + HTML) -->
    <script src="arboleda_bot.js"></script>  <!-- Script JS del chatbot -->

    <!-- CONTENEDOR NOTIFICACIONES - Popups esquina superior derecha -->
    <div id="arboleda_bot_notifications"></div>
    
    <!-- BOTÓN TOGGLE - Abre/cierra chat (emoji 🤖 flotante) -->
    <button id="arboleda_bot_toggle" title="Abrir ArboledaBot" class="arboleda_bot_toggle">🤖</button>
    
    <!-- CONTENEDOR CHAT - Ventana completa con header/mensajes/input -->
    <div id="arboleda_bot_container" class="arboleda_bot_container">
        <!-- HEADER CHAT - Título + subtítulo -->
        <div class="arboleda_bot_header">
            <div class="arboleda_bot_header_titulo">🤖 ArboledaBot</div>
            <div class="arboleda_bot_header_subtitulo">Guía del IES La Arboleda</div>
        </div>

        <!-- ÁREA MENSAJES - Scrollable con mensajes user/bot -->
        <div class="arboleda_bot_messages" id="arboleda_bot_messages">
            <!-- MENSAJE BIENVENIDA BOT - Mensaje inicial fijo -->
            <div class="arboleda_bot_message arboleda_bot_bot">
                <div class="arboleda_bot_content">
                    ¡Hola! Te ayudo a encontrar todo:<br>
                    • Matriculación → Secretaría<br>
                    • Aula Virtual → A UN CLIC<br>
                    • DAW → Oferta educativa<br><br>
                    ¡Pregúntame!
                </div>
            </div>
        </div>

        <!-- INPUT ENVÍO - Campo texto + botón enviar -->
        <div class="arboleda_bot_input">
            <!-- CAMPO TEXTO - Placeholder con ejemplo + autocomplete off -->
            <input type="text" id="arboleda_bot_input" placeholder="Ej: ¿matriculación?" autocomplete="off">
            <!-- BOTÓN ENVIAR - Emoji flecha ➤ -->
            <button class="arboleda_bot_send" title="Enviar">➤</button>
        </div>
    </div>  <!-- FIN ARBOLEDA BOT -->
