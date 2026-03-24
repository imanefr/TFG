<?php include 'conexion.php'; ?>              <!-- Conecta a MySQLi -->

<?php
// Consulta BD: obtiene datos contacto secretaría (solo primer registro)
$sql = "SELECT telefono, fax, horario, correo, aviso FROM contacto_secretaria LIMIT 1";  // SQL selecciona 5 campos
$resultado = $conexion->query($sql);         // Ejecuta consulta y guarda resultado
$datos_contacto = $resultado->fetch_assoc(); // Convierte primer resultado en array asociativo
?>

<?php include 'head.php'; ?>                 <!-- Incluye el head.php -->

<!-- HEADER CONTACTO SECRETARIS -->
<section class="seccion-hero-universal">     
    <div class="contenedor-max">             
        <div class="hero-layout-universal">  
            <div class="hero-icono-universal">
                <i class="fas fa-phone icono_universal"></i>  
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Contacto secretaría</h1> <!-- Título H1 página -->
                <p class="hero-subtitulo-universal">Información y atención al público</p> <!-- Subtítulo descriptivo -->
            </div>
        </div>
    </div>
</section>

<!-- Contenido principal contacto -->
<main class="info_contacto_pagina">          
    <section class="seccion-contenido">      
        <div class="contenedor-max">         
            <h2 class="info_contacto_titulo">Para contactar con secretaría</h2> <!-- Título sección -->

            <!-- Lista completa datos contacto -->
            <div class="info_contacto_lista"> <!-- Contenedor lista -->
                <!-- ÍTEM TELÉFONO -->
                <div class="info_contacto_item"> 
                    <i class="fas fa-phone"></i> <!-- Icono teléfono -->
                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($datos_contacto['telefono']); ?> <!-- Muestra teléfono escapado -->
                </div>

                <!-- ÍTEM FAX -->
                <div class="info_contacto_item"> 
                    <i class="fas fa-fax"></i>     <!-- Icono fax -->
                    <strong>Fax:</strong> <?php echo htmlspecialchars($datos_contacto['fax']); ?> <!-- Muestra fax escapado -->
                </div>

                <!-- ÍTEM HORARIO -->
                <div class="info_contacto_item"> 
                    <i class="fas fa-clock"></i>   <!-- Icono reloj -->
                    <strong>Horario:</strong> <?php echo htmlspecialchars($datos_contacto['horario']); ?> <!-- Muestra horario escapado -->
                </div>

                <!-- ÍTEM CORREO (clicable) -->
                <div class="info_contacto_item"> 
                    <i class="fas fa-envelope"></i> <!-- Icono sobre -->
                    <strong>Correo:</strong> 
                    <a href="mailto:<?php echo htmlspecialchars($datos_contacto['correo']); ?>" class="info_contacto_email"> <!-- Enlace mailto -->
                        <?php echo htmlspecialchars($datos_contacto['correo']); ?> <!-- Email escapado 2 veces -->
                    </a>
                </div>
            </div>

            <!-- Aviso destacado (importante) -->
            <div class="info_contacto_aviso">    
                <i class="fas fa-exclamation-triangle"></i> <!-- Icono advertencia -->
                <strong>AVISO IMPORTANTE:</strong> <?php echo htmlspecialchars($datos_contacto['aviso']); ?> <!-- Texto aviso -->
            </div>
        </div>
    </section>
</main>

<?php $conexion->close(); ?>                 <!-- Cierra conexión base datos -->

<?php include 'footer.php'; ?>               <!-- Carga footer  -->
