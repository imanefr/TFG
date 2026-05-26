<?php
// 1. INICIO DE SESIÓN
// Arranca la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. CONEXIÓN Y CABECERA
include("conexion.php"); // Conecta con la base de datos
include_once 'head.php'; // Carga la cabecera e iconos de la web

// 3. OBTENER NOTIFICACIONES DESTACADAS
// Busca hasta 5 noticias destacadas para los popups estilo WhatsApp
$sql_notif = "SELECT id, titulo, contenido AS descripcion, fecha, enlace FROM noticias ORDER BY fecha DESC LIMIT 2";
$result_notif = $conexion->query($sql_notif);
$notificaciones = [];

if ($result_notif && $result_notif->num_rows > 0) {
    while ($row = $result_notif->fetch_assoc()) {
        $notificaciones[] = $row; // Guarda cada noticia destacada en la lista
    }
}
?>

<section id="inicio" class="indice_pagina_hero_carrusel">
    <div class="indice_pagina_contenedor_carrusel">
        <div class="indice_pagina_imagen_carrusel indice_pagina_activa" style="background-image: url('img/instituto_back_1.jpg');"></div>
        <div class="indice_pagina_imagen_carrusel" style="background-image: url('img/instituto_back_2.jpg');"></div>
        <div class="indice_pagina_imagen_carrusel" style="background-image: url('img/instituto_back_3.jpg');"></div>
    </div>
    <div class="indice_pagina_contenido_hero">
        <h2 class="indice_pagina_titulo_hero">
            <span class="indice_pagina_texto_hero_superior">BIENVENIDOS A NUESTRO CENTRO</span>
            <span class="indice_pagina_texto_hero_principal">IES LA ARBOLEDA</span>
            <span class="indice_pagina_texto_hero_inferior">Alcorcón - Tu instituto de referencia</span>
        </h2>
    </div>
</section>

<section class="franja_imagenes_ancho">
    <div class="franja_contenedor_flex">
        <div class="franja_item_imagen"><img src="img/union_eu.jpeg" alt="Union Europea"></div>
        <div class="franja_item_imagen"><img src="img/consejeria.png" alt="Comunidad de Madrid"></div>
        <div class="franja_item_imagen"><img src="img/gobierno.png" alt="Gobierno de España"></div>
        <div class="franja_item_imagen"><img src="img/eu2.jpg" alt="UE"></div>
        <div class="franja_item_imagen"><img src="img/circulo.png" alt="Competencia Informática"></div>
    </div>
</section>

<section class="indice_pagina_seccion_atajo">
    <div class="indice_pagina_contenedor_principal">
        <h3 class="indice_pagina_titulo_atajo">A UN CLIC</h3>
        <div class="indice_pagina_grid_atajos">
            <a href="https://aulavirtual33.educa.madrid.org/ies.laarboleda.alcorcon/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-graduation-cap"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Aula Virtual</h4>
            </a>
            <a href="https://correoweb.educa.madrid.org/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-envelope"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Correo educamadrid</h4>
            </a>
            <a href="https://raices.madrid.org/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-tree"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Roble/Raíces</h4>
            </a>
        </div>
    </div>
</section>

<main>
    <section class="indice_pagina_contenedor_principal">

        <a href="relevante_ahora.php" class="indice_relevante_ahora_link">
            <h2 class="indice_pagina_titulo_atajo">RELEVANTE AHORA</h2>
        </a>
        <div class="indice_pagina_grid_noticias">
            <?php
            // Busca las últimas 3 noticias destacadas
            $sql_destacadas = "SELECT * FROM noticias WHERE destacada = 1 ORDER BY fecha DESC LIMIT 3";
            $res_destacadas = $conexion->query($sql_destacadas);
            if ($res_destacadas && $res_destacadas->num_rows > 0) {
                while ($fila = $res_destacadas->fetch_assoc()) {
                    ?>
                    <a href="noticia.php?id=<?php echo $fila['id']; ?>" class="indice_pagina_tarjeta_noticia card-un-clic">
                        <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Noticia">
                        <p class="indice_pagina_fecha_noticia"><?php echo date('d/m/Y', strtotime($fila["fecha"])); ?></p>
                        <h4 class="indice_pagina_titulo_noticia"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                    </a>
                    <?php
                }
            }
            ?>
        </div>

        <a href="ultimas_noticias.php" class="indice_relevante_ahora_link">
            <h2 class="indice_pagina_titulo_atajo">ÚLTIMAS NOTICIAS</h2>
        </a>
        <div class="indice_pagina_grid_noticias">
            <?php
            // Busca las últimas 6 noticias publicadas
            $sql_ultimas = "SELECT * FROM noticias ORDER BY fecha DESC LIMIT 6";
            $res_ultimas = $conexion->query($sql_ultimas);
            if ($res_ultimas && $res_ultimas->num_rows > 0) {
                while ($fila = $res_ultimas->fetch_assoc()) {
                    ?>
                    <div class="indice_pagina_tarjeta_noticia noticia-item">
                        <?php if (!empty($fila["imagen"])): ?>
                            <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Noticia">
                        <?php endif; ?>
                        <p class="indice_pagina_fecha_noticia"><?php echo date('d/m/Y', strtotime($fila["fecha"])); ?></p>
                        <h4 class="indice_pagina_titulo_noticia"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                        <p class="indice_pagina_contenido_noticia">
                            <?php echo htmlspecialchars(substr(strip_tags($fila["contenido"]), 0, 100)) . '...'; ?>
                        </p>
                        <a href="noticia.php?id=<?php echo $fila['id']; ?>" class="indice_pagina_boton_leer_mas">Leer más</a>
                    </div>
                    <?php
                }
            }
            // Cierra la conexión a la base de datos
            $conexion->close();
            ?>
        </div>
    </section>
</main>

<div id="notificaciones_whatsapp_container"></div>

<script>
    // Pasa las noticias destacadas de PHP a JavaScript
    const notificaciones = <?php echo json_encode($notificaciones); ?>;

    // Crea el popup estilo WhatsApp en la esquina de la pantalla
    function mostrarNotificacionWhatsApp(notif) {
        const container = document.getElementById('notificaciones_whatsapp_container');
        const notifDiv = document.createElement('div');
        notifDiv.className = 'notificaciones_whatsapp_item';

        // Abre la noticia al hacer clic en la alerta
        notifDiv.onclick = () => {
            window.location.href = 'noticia.php?id=' + notif.id;
        };

        // Estructura HTML interna del popup flotante
        notifDiv.innerHTML = `
        <div class="notificaciones_whatsapp_header">
            <div class="notificaciones_whatsapp_avatar">${notif.titulo.charAt(0)}</div>
            <div class="notificaciones_whatsapp_content">
                <div class="notificaciones_whatsapp_titulo">${notif.titulo}</div>
                <div class="notificaciones_whatsapp_time">${new Date(notif.fecha).toLocaleDateString()}</div>
            </div>
        </div>
        <div class="notificaciones_whatsapp_descripcion">${notif.descripcion.substring(0, 80)}...</div>
    `;

        container.insertBefore(notifDiv, container.firstChild);

        // Oculta y borra el popup después de 8 segundos
        setTimeout(() => {
            notifDiv.style.opacity = '0';
            notifDiv.style.transform = 'translateX(100px)';
            notifDiv.style.transition = 'all 0.5s ease';
            setTimeout(() => notifDiv.remove(), 500);
        }, 8000);
    }

    // Lanza las alertas de una en una cada 3 segundos
    if (notificaciones.length > 0) {
        notificaciones.forEach((notif, i) => {
            setTimeout(() => mostrarNotificacionWhatsApp(notif), i * 3000);
        });
    }

    // CONTROL DEL CARRUSEL DE IMÁGENES
    let indiceActual = 0;
    const imagenes = document.querySelectorAll('.indice_pagina_imagen_carrusel');

    // Cambia la imagen de fondo visible
    function cambiarImagen() {
        imagenes[indiceActual].classList.remove('indice_pagina_activa'); // Oculta la actual
        indiceActual = (indiceActual + 1) % imagenes.length;            // Pasa a la siguiente
        imagenes[indiceActual].classList.add('indice_pagina_activa');    // Muestra la nueva
    }

    // Ejecuta el cambio automático cada 4 segundos
    if (imagenes.length > 0) {
        setInterval(cambiarImagen, 4000);
    }
</script>

<?php 
// Carga el pie de página global
include 'footer.php'; 
?>