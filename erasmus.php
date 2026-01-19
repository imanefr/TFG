<?php
include("conexion.php");

// Consulta noticias Erasmus (últimas 8)
$sql = "SELECT titulo, contenido, fecha, enlace FROM erasmus_news ORDER BY fecha DESC LIMIT 8";
$resultado = $conexion->query($sql);
$noticias = [];
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila;
}
?>

       <?php include 'head.php'; ?>

<body>
    

    <!-- HEADER ERASMUS -->
    <section class="erasmus-contenido">
        <div class="contenedor-max">
            <div class="avisos-layout">
                <div class="avisos-logo">
                    <i class="fas fa-plane" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="avisos-texto">
                    <h2>ERASMUS+</h2>
                    <p>Proyectos de movilidad en Europa desde 2010</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ACREDITACIONES -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="seccion-contenido-h2">Nuestras Acreditaciones</h2>
            
            <div class="grid-acreditaciones">
                <a href="https://erasmus-plus.ec.europa.eu/document/higher-education-institutions-holding-an-eche-2021-2027" class="card-acreditacion" target="_blank">
                    <i class="fas fa-certificate"></i>
                    <h4>ERASMUS CHARTER FOR HIGHER EDUCATION</h4>
                    <p>ECHE 2021-2027</p>
                </a>
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/03/2020-1-CERTIFICADO-ES01-KA120-VET-095056.pdf" class="card-acreditacion" target="_blank">
                    <i class="fas fa-certificate"></i>
                    <h4>KA120-VET Acreditación FP</h4>
                    <p>Formación Profesional</p>
                </a>
            </div>

            <div class="documentos-erasmus">
                <div class="doc-erasmus">
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/04/CartaECHE2021_IES_LaArboleda_ES.pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> Carta ECHE (ES)
                    </a>
                </div>
                <div class="doc-erasmus">
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/04/ECHE_Letter2021_IES_LaArboleda_EN.pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> ECHE Charter (EN)
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- NOTICIAS ERASMUS (estilo avisos) -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="seccion-contenido-h2">Últimas Movilidades</h2>
            
            <?php if (!empty($noticias)): ?>
                <div class="lista-avisos">
                    <?php foreach ($noticias as $noticia): ?>
                        <div class="aviso-item">
                            <div class="aviso-contenido">
                                <p class="aviso-fecha"><?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></p>
                                <h3 class="aviso-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p class="aviso-texto"><?php echo substr(strip_tags($noticia['contenido']), 0, 200); ?>...</p>
                                <?php if ($noticia['enlace']): ?>
                                    <a href="<?php echo htmlspecialchars($noticia['enlace']); ?>" class="aviso-enlace" target="_blank">
                                        Leer completo →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sin-contenido">
                    <i class="fas fa-globe"></i>
                    <h3>No hay noticias recientes</h3>
                    <p>Próximamente nuevas movilidades Erasmus+</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

           <?php include 'footer.php'; ?>


    <script src="script.js"></script>
</body>
</html>
