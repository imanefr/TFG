<?php
include 'head.php';
include 'conexion.php'
?>

<section class="seccion-hero-universal departamentos_hero">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fa-solid fa-file-arrow-down" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Documentos Institucionales</h1>
                <p class="hero-subtitulo-universal">Documentos institucionales del centro</p>
            </div>
        </div>
    </div>
</section>

<main class="documentos_institucionales_pagina">
    <div class="contenedor-max">
        <h2 class="documentos_institucionales_titulo">Documentos Institucionales</h2>
        
        <?php
        $sql = "SELECT * FROM documentos_institucionales";
        $result = $conexion->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['titulo'] != null && $row['url'] != null){
                    if($row['tipo_archivo'] == 'pdf'){
                ?>
                <div class="documentos_institucionales_contenedor_pdf">
                    <h3 class="documentos_institucionales_seccion_titulo"><?php echo $row['titulo']; ?></h3>
                    <iframe src="<?php echo $row['url']; ?>" class="documentos_institucionales_pdf_iframe"></iframe>
                    <a href="<?php echo $row['url']; ?>" class="documentos_institucionales_btn_descarga" download>
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </a>
                </div>
                <?php
                    }
                }
            }
        }
        ?>
    </div>
</main>

<?php
include 'footer.php';
?>