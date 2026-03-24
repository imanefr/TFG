<?php 
include 'head.php';
include 'conexion.php';
$deptID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Switch para obtener el nombre del departamento y su icono
switch ($deptID) {
    case 1:
        $deptNombre = "Actividades Extraescolares";
        $deptIcono = "fas fa-star";
        break;
    case 2:
        $deptNombre = "Biblioteca";
        $deptIcono = "fas fa-book";
        break;
    case 3:
        $deptNombre = "Biología y Geología";
        $deptIcono = "fas fa-leaf";
        break;
    case 4:
        $deptNombre = "Dibujo";
        $deptIcono = "fas fa-pencil-alt";
        break;
    case 5:
        $deptNombre = "Economía";
        $deptIcono = "fas fa-chart-line";
        break;
    case 6:
        $deptNombre = "Educación Física";
        $deptIcono = "fas fa-dumbbell";
        break;
    case 7:
        $deptNombre = "Filosofía";
        $deptIcono = "fas fa-brain";
        break;
    case 8:
        $deptNombre = "Física y Química";
        $deptIcono = "fas fa-flask";
        break;
    case 9:
        $deptNombre = "Francés";
        $deptIcono = "fas fa-flag";
        break;
    case 10:
        $deptNombre = "FOL";
        $deptIcono = "fas fa-briefcase";
        break;
    case 11:
        $deptNombre = "Geografía e Historia";
        $deptIcono = "fas fa-globe";
        break;
    case 12:
        $deptNombre = "Imagen Personal";
        $deptIcono = "fas fa-cut";
        break;
    case 13:
        $deptNombre = "Imagen y Sonido";
        $deptIcono = "fas fa-video";
        break;
    case 14:
        $deptNombre = "Informática";
        $deptIcono = "fas fa-laptop";
        break;
    case 15:
        $deptNombre = "Inglés";
        $deptIcono = "fas fa-language";
        break;
    case 16:
        $deptNombre = "Lengua Castellana y Literatura";
        $deptIcono = "fas fa-font";
        break;
    case 17:
        $deptNombre = "Matemáticas";
        $deptIcono = "fas fa-calculator";
        break;
    case 18:
        $deptNombre = "Música";
        $deptIcono = "fas fa-music";
        break;
    case 19:
        $deptNombre = "Orientación";
        $deptIcono = "fas fa-compass";
        break;
    case 20:
        $deptNombre = "Religión";
        $deptIcono = "fas fa-pray";
        break;
    case 21:
        $deptNombre = "Tecnología";
        $deptIcono = "fas fa-cogs";
        break;
    default:
        $deptNombre = "Departamento no encontrado";
        $deptIcono = "fas fa-users";
        break;
}

?>

<!-- HERO -->
<section class="seccion-hero-universal departamentos_hero">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="<?php echo $deptIcono; ?>" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal"><?php echo $deptNombre; ?></h1>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="departamentos_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="departamentos_titulo"><?php echo $deptNombre; ?></h2>
            <?php 
            
            $sql = "SELECT * FROM departamentos WHERE id = $deptID";
            $resultado = mysqli_query($conexion, $sql);
            $fila = mysqli_fetch_assoc($resultado);

            if($fila != "")
            {
                foreach($fila as $seccion)
                {
                    ?>
                    <div class="departamentos_seccion">
                        <h3 class="departamentos_seccion_titulo"><?php echo $seccion['titulo_seccion']; ?></h3>
                        <p class="departamentos_seccion_contenido"><?php echo $seccion['texto_seccion']; ?></p>
                    </div>
                <?php
                }
            }
            else
            {
                ?>
                <div class="erasmus_sin_contenido">
                    <i class="<?php echo $deptIcono; ?>"></i>
                    <h3>No hay noticias recientes</h3>
                    <p>No te preocupes. ¡Habrán novedades pronto!</p>
                </div>
                <?php
            }
                ?>
            <div class="departamentos_link_volver_container">
                <a href="departamentos.php" class="departamentos_link_volver">Volver a departamentos</a>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>