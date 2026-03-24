<?php
include("conexion.php");  // Conexión base de datos

$sql = "SELECT pdf_url, titulo, descripcion FROM libros_eso WHERE activo = 1 LIMIT 1";
$resultado = $conexion->query($sql);

if (!$resultado) {
    $libro = ['pdf_url' => '', 'titulo' => '', 'descripcion' => 'Error al cargar los libros.'];
} else {
    $libro = $resultado->fetch_assoc() ?: ['pdf_url' => '', 'titulo' => '', 'descripcion' => ''];
}

$conexion->close();  // Cierra BD

// Base URL para PDFs (ajusta según tu estructura)
$baseUrl = 'https://tudominio.tld/'; // cambia por tu dominio/carpeta
$pdfUrl = $libro['pdf_url'];

// Si la URL es relativa, conviértela a absoluta
if (!empty($pdfUrl) && !preg_match('#^https?://#i', $pdfUrl)) {
    $pdfUrl = $baseUrl . ltrim($pdfUrl, '/');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'head.php'; ?> <!-- Meta + CSS base -->

</head>

<body>

<!-- Hero con icono libro -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-book icono_universal"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">ESO</h1>
                <p class="hero-subtitulo-universal"><?php echo htmlspecialchars($libro['titulo']); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Contenido principal -->
<main class="info_eso_pagina">
    <section class="seccion-contenido">  
        <div class="contenedor-max">     
            
            <!-- TÍTULO PRINCIPAL -->
            <h2 class="info_eso_titulo">Libros de Texto ESO</h2>

            <!-- DESCRIPCIÓN DESDE BD - Muestra si existe en variable $libro['descripcion'] -->
            <?php if (!empty($libro['descripcion'])): ?>
                <p class="info_eso_descripcion"><?php echo htmlspecialchars($libro['descripcion']); ?></p>  <!-- Escapa HTML XSS -->
            <?php endif; ?>

            <!-- BOTÓN DESCARGA PDF - Si existe $pdfUrl desde código PHP previo -->
            <?php if (!empty($pdfUrl)): ?>
                <div class="info_eso_acciones"> 
                    <!-- ENLACE PDF OFICIAL - Target blank + seguridad -->
                    <a href="<?php echo htmlspecialchars($pdfUrl); ?>" class="info_eso_btn_pdf"
                       title="Descargar PDF oficial" target="_blank" rel="noopener">  <!-- rel=noopener seguridad popup -->
                        <i class="fas fa-download"></i> Descargar PDF oficial  <!-- Icono + texto -->
                    </a>
                </div>
            <?php endif; ?>

            <!-- VISTA PREVIA IFRAME - Visor PDF embebido -->
            <?php if (!empty($pdfUrl)): ?>
                <div class="info_eso_vista">    
                    <!-- IFRAME PDF - Muestra PDF directamente en navegador -->
                    <iframe src="<?php echo htmlspecialchars($pdfUrl); ?>"
                            class="info_eso_visor"
                            title="Visor de libros ESO">  <!-- Title accesibilidad -->
                    </iframe>
                </div>
            <?php endif; ?>

            <!-- ESTADO SIN DATOS - Fallback si BD vacío -->
            <?php if (empty($pdfUrl)): ?>
                <div class="info_eso_sin_datos"> 
                    <i class="fas fa-book info_eso_icono_vacio"></i>  <!-- Icono libro vacío -->
                    <h3>No hay libros disponibles</h3>                 <!-- Título error -->
                    <p>Consulta con secretaría</p>                    <!-- Instrucción contacto -->
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- FOOTER GLOBAL -->
<?php include 'footer.php'; ?>
</body>
</html>
