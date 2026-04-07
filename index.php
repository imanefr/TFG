<?php
// INICIA SESIÓN SI NO ESTÁ ACTIVA - Verifica estado y evita warnings múltiples
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Inicia sesión PHP solo si no existe
}
?>

<!-- INCLUYE HEADER COMPLETO - Navbar, breadcrumb, ArboledaBot (head.php) -->
<?php include_once 'head.php'; ?>

<?php
// CARGAR NOTIFICACIONES PUSH - Lee tabla BD para popups WhatsApp
include("conexion.php");                                    // Conexión MySQLi
$sql_notif = "SELECT * FROM notificaciones_push WHERE activo=1 ORDER BY fecha DESC LIMIT 10";  // Últimas 10 activas
$result_notif = $conexion->query($sql_notif);               // Ejecuta consulta
$notificaciones = [];                                       // Array vacío inicial

// PROCESA RESULTADO - Convierte filas BD → array PHP
if ($result_notif && $result_notif->num_rows > 0) {
    while ($row = $result_notif->fetch_assoc()) {           // Itera cada fila
        $notificaciones[] = $row;                           // Añade a array
    }
}
$conexion->close();                                         // Cierra conexión BD
?>

<!-- HERO CARRUSEL - Imagen principal + título overlay (3 slides) -->
<section id="inicio" class="indice_pagina_hero_carrusel">
    <div class="indice_pagina_contenedor_carrusel">          <!-- Wrapper carrusel JS/CSS -->
        <!-- SLIDE 1 - Imagen de fondo activa por defecto -->
        <div class="indice_pagina_imagen_carrusel indice_pagina_activa" style="background-image: url('img/instituto_back_1.jpg');"></div>
        <!-- SLIDE 2 - Segunda imagen -->
        <div class="indice_pagina_imagen_carrusel" style="background-image: url('img/instituto_back_2.jpg');"></div>
        <!-- SLIDE 3 - Tercera imagen -->
        <div class="indice_pagina_imagen_carrusel" style="background-image: url('img/instituto_back_3.jpg');"></div>
    </div>
    <!-- TEXTO OVERLAY - Título hero sobre carrusel -->
    <div class="indice_pagina_contenido_hero">
        <h2 class="indice_pagina_titulo_hero">
            <span class="indice_pagina_texto_hero_superior">BIENVENIDOS A NUESTRO CENTRO</span>  <!-- Línea superior -->
            <span class="indice_pagina_texto_hero_principal">IES LA ARBOLEDA</span>              <!-- Título principal -->
            <span class="indice_pagina_texto_hero_inferior">Alcorcón - Tu instituto de referencia</span> <!-- Subtítulo -->
        </h2>
    </div>
</section>

<!-- ATAJOS "A UN CLIC" - 3 enlaces directos externos -->
<section class="indice_pagina_seccion_atajo">
    <div class="indice_pagina_contenedor_principal">
        <h3 class="indice_pagina_titulo_atajo">A UN CLIC</h3>     <!-- Título sección -->
        <div class="indice_pagina_grid_atajos">                  <!-- Grid CSS 3 columnas -->
            <!-- AULA VIRTUAL - Enlace EducaMadrid -->
            <a href="https://aulavirtual33.educa.madrid.org/ies.laarboleda.alcorcon/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-graduation-cap"></i></div>  <!-- Icono FontAwesome -->
                <h4 class="indice_pagina_titulo_atajo_card">Aula Virtual</h4>
            </a>
            <!-- CORREO EDUCAMADRID - Email institucional -->
            <a href="https://correoweb.educa.madrid.org/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-envelope"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Correo educamadrid</h4>
            </a>
            <!-- ROBLE/RAÍCES - Plataforma Comunidad Madrid -->
            <a href="https://raices.madrid.org/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-tree"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Roble/Raíces</h4>
            </a>
        </div>
    </div>
</section>

<!-- MAIN CONTENIDO - Secciones dinámicas -->
<main>
    <?php include("conexion.php"); ?>                          <!-- Reconexión BD para noticias -->
    
    <section class="indice_pagina_contenedor_principal">
        <!-- TÍTULO RELEVANTE -->
         <a href="noticias_relevantes.php" class="indice_relevante_ahora_link">
            <h2 class="indice_pagina_titulo_atajo">RELEVANTE AHORA</h2>
         </a>
        <div class="indice_pagina_grid_noticias"> <!-- Grid noticias fijas -->
            <?php
                $sql = "SELECT * FROM noticias WHERE destacada = 1 ORDER BY fecha DESC LIMIT 3";
                $resultado = $conexion->query($sql);
                if ($resultado && $resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
            ?>
                <a href="noticias_relevantes.php?id=<?php echo $fila['id']; ?>" class="indice_pagina_tarjeta_noticia card-un-clic">
                    <img src="<?php echo $fila['imagen']; ?>" alt="<?php echo $fila['titulo']; ?>">
                    <p class="indice_pagina_fecha_noticia"><?php echo date("d/m/Y", strtotime($fila["fecha"])); ?></p>
                    <h4 class="indice_pagina_titulo_noticia"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                </a>
            <?php
                }  // Fin while noticias
            }  // Fin if noticias
            ?>
        </div>

        <!-- ÚLTIMAS NOTICIAS - Dinámicas desde BD -->
         <a href="ultimas_noticias.php" class="indice_relevante_ahora_link">
            <h2 class="indice_pagina_titulo_atajo">ÚLTIMAS NOTICIAS</h2>
         </a>
        <div class="indice_pagina_grid_noticias">
            <?php
            $sql = "SELECT * FROM noticias ORDER BY fecha DESC LIMIT 6";  // Últimas 5 noticias
            $resultado = $conexion->query($sql);                         // Ejecuta consulta
            if ($resultado && $resultado->num_rows > 0) {                // Si hay resultados
                while ($fila = $resultado->fetch_assoc()) {              // Itera cada noticia
            ?>
                <!-- TARJETA NOTICIA DINÁMICA -->
                <div class="indice_pagina_tarjeta_noticia noticia-item">
                    <?php if (!empty($fila["imagen"])): ?>                   <!-- IMAGEN OPCIONAL -->
                        <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Noticia">
                    <?php endif; ?>
                    <!-- FECHA FORMATEADA - "2026-03-17" → "17/03/2026" -->
                    <p class="indice_pagina_fecha_noticia"><?php echo date("d/m/Y", strtotime($fila["fecha"])); ?></p>
                    <!-- TÍTULO - Escapa HTML para seguridad -->
                    <h4 class="indice_pagina_titulo_noticia"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                    <!-- CONTENIDO - Primer párrafo escapa HTML -->
                    <p class="indice_pagina_contenido_noticia"><?php echo htmlspecialchars($fila["contenido"]); ?></p>
                    <!-- ENLACE DETALLE - noticia.php?id=123 -->
                    <a href="noticia.php?id=<?php echo $fila['id']; ?>" class="indice_pagina_boton_leer_mas">Leer más</a>
                </div>
            <?php
                }  // Fin while noticias
            } else {  // SIN NOTICIAS
                echo '<p>No hay noticias disponibles por el momento.</p>';
            }
            $conexion->close();  // Cierra conexión BD
            ?>
        </div>
    </section>
</main>

<!-- CONTENEDOR NOTIFICACIONES -->
<div id="notificaciones_whatsapp_container"></div>

<!-- JS NOTIFICACIONES -->
<script>
const notificaciones = <?php echo json_encode($notificaciones); ?>;  // Convierte PHP array → JS JSON

// FUNCIÓN: Crea popup individual
function mostrarNotificacionWhatsApp(notif) {
    const container = document.getElementById('notificaciones_whatsapp_container');  // Busca contenedor
    const notifDiv = document.createElement('div');                                  // Crea div notificación
    notifDiv.className = 'notificaciones_whatsapp_item';                             // Clase CSS
    
    // CLICK ENLACE - Abre URL si existe
    if (notif.enlace) {
        notifDiv.onclick = () => window.open(notif.enlace, '_blank');
    }
    
    // HTML TEMPLATE - Estructura -like
    
    notifDiv.innerHTML = `
        <div class="notificaciones_whatsapp_header">                           <!-- Header: avatar + título -->
            <div class="notificaciones_whatsapp_avatar">${notif.titulo.charAt(0)}</div>  <!-- 1ª letra avatar -->
            <div class="notificaciones_whatsapp_content">
                <div class="notificaciones_whatsapp_titulo">${notif.titulo}</div>         <!-- Nombre remitente -->
                <div class="notificaciones_whatsapp_time">${new Date(notif.fecha).toLocaleTimeString()}</div>  <!-- Hora local -->
            </div>
        </div>
        <div class="notificaciones_whatsapp_descripcion">${notif.descripcion}</div>  <!-- Mensaje -->
    `;
    
    // INSERTA ARRIBA - Primera posición (más reciente arriba)
    container.insertBefore(notifDiv, container.firstChild);
    // AUTO-ELIMINA - 10 segundos después
    setTimeout(() => notifDiv.remove(), 10000);
}

// MUESTRA 2 PRIMERAS - Con delay progresivo (0s, 2s)
if (notificaciones.length > 0) {
    notificaciones.slice(0, 2).forEach((notif, i) => {         // Primeras 2 notificaciones
        setTimeout(() => mostrarNotificacionWhatsApp(notif), i * 2000);  // Delay 2s entre cada una
    });
}
</script>

<!-- FOOTER - Incluye copyright, redes, contacto -->
<?php include 'footer.php'; ?>
