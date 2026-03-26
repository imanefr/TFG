<?php
    include("head.php");
    include("conexion.php");

    
?>

<section class="seccion-hero-universal departamentos_hero">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fa-solid fa-user-doctor" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Orientación</h1>
                <p class="hero-subtitulo-universal">Información y recursos del departamento de orientación</p>
            </div>
        </div>
    </div>
</section>

<main class="orientacion_pagina">
    <div class="contenedor-max">
        <h1 class="orientacion_titulo">Departamento de Orientación</h1>
        <p class="orientacion_parrafo">
            La Educación Secundaria Obligatoria (ESO) son
            los estudios mínimos obligatorios en España. 
            Al finalizar esta etapa, existen diferentes opciones
            tanto si has obtenido la titulación como si no la has
            conseguido.
        </p>
        <div class="orientacion-info">
            <div class="orientacion_info_seccion">
                <h3 class="orientacion_seccion_titulo">¿Qué puedo hacer después de la ESO?</h3>
                <p class="orientacion_parrafo">Si has aprobado la ESO, hay varias opciones que puedes escoger:</p>
                <ul class="orientacion_lista">
                <?php
                    $sql = "SELECT * FROM orientacion ORDER BY orden";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                        ?>
                            <li>
                                <h4 class="orientacion_seccion_titulo_lista"><?php echo $row['titulo']; ?></h4>
                                <p class="orientacion_parrafo_lista">
                                <?php echo $row['texto']; ?>
                                <a class="orientacion_link" href="<?php echo $row['link']; ?>"><?php echo $row['texto_enlace']; ?></a>
                                </p>
                            </li>
                        <?php
                        }   
                    }
                ?>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php
    include("footer.php");
?>