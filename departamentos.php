<?php
// PÁGINA DEPARTAMENTOS 
// Listado completo 20 departamentos didácticos con enlaces directos
include("conexion.php"); // Conexión preparada para contenido dinámico futuro

// ARRAY COMPLETO - 20 departamentos organizados por áreas académicas
$departamentos = [
    ['nombre' => 'Actividades Extraescolares', 'pagina' => 'actividades_extraescolares.php', 'icono' => 'fas fa-star'],
    ['nombre' => 'Biblioteca', 'pagina' => 'biblioteca.php', 'icono' => 'fas fa-book'],
    ['nombre' => 'Biología y Geología', 'pagina' => 'biologia.php', 'icono' => 'fas fa-leaf'],
    ['nombre' => 'Dibujo', 'pagina' => 'dibujo.php', 'icono' => 'fas fa-pencil-alt'],
    
    ['nombre' => 'Economía', 'pagina' => 'economia.php', 'icono' => 'fas fa-chart-line'],
    ['nombre' => 'Educación Física', 'pagina' => 'educacion_fisica.php', 'icono' => 'fas fa-dumbbell'],
    ['nombre' => 'Filosofía', 'pagina' => 'filosofia.php', 'icono' => 'fas fa-brain'],
    ['nombre' => 'Física y Química', 'pagina' => 'fisica_quimica.php', 'icono' => 'fas fa-flask'],
    
    ['nombre' => 'Francés', 'pagina' => 'frances.php', 'icono' => 'fas fa-flag'],
    ['nombre' => 'FOL', 'pagina' => 'fol.php', 'icono' => 'fas fa-briefcase'],
    ['nombre' => 'Geografía e Historia', 'pagina' => 'geografia_historia.php', 'icono' => 'fas fa-globe'],
    ['nombre' => 'Imagen Personal', 'pagina' => 'imagen_personal.php', 'icono' => 'fas fa-cut'],
    
    ['nombre' => 'Imagen y Sonido', 'pagina' => 'imagen_sonido.php', 'icono' => 'fas fa-video'],
    ['nombre' => 'Informática', 'pagina' => 'informatica.php', 'icono' => 'fas fa-laptop'],
    ['nombre' => 'Inglés', 'pagina' => 'ingles.php', 'icono' => 'fas fa-language'],
    ['nombre' => 'Lengua Castellana y Literatura', 'pagina' => 'lengua.php', 'icono' => 'fas fa-font'],
    
    ['nombre' => 'Matemáticas', 'pagina' => 'matematicas.php', 'icono' => 'fas fa-calculator'],
    ['nombre' => 'Música', 'pagina' => 'musica.php', 'icono' => 'fas fa-music'],
    ['nombre' => 'Orientación', 'pagina' => 'orientacion.php', 'icono' => 'fas fa-compass'],
    ['nombre' => 'Religión', 'pagina' => 'religion.php', 'icono' => 'fas fa-pray'],
    
    ['nombre' => 'Tecnología', 'pagina' => 'tecnologia.php', 'icono' => 'fas fa-cogs']
];
?>

<!-- HEADER GLOBAL PÚBLICO -->
<?php include 'head.php'; ?>

<!-- HERO SECTION - Presentación departamentos -->
<section class="seccion-hero-universal departamentos_hero">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users icono_universal"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Departamentos</h1>
                <p class="hero-subtitulo-universal">Departamentos didácticos del centro</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL - Grid responsive 20 tarjetas -->
<main class="departamentos_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="departamentos_grid">
                <?php foreach ($departamentos as $dep): ?>
                    <!-- TARJETA INDIVIDUAL - Enlace directo departamento -->
                    <a href="<?php echo htmlspecialchars($dep['pagina']); ?>" class="departamentos_card">
                        <i class="<?php echo htmlspecialchars($dep['icono']); ?> departamentos_icono"></i>
                        <h3><?php echo htmlspecialchars($dep['nombre']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<!-- FOOTER GLOBAL -->
<?php include 'footer.php'; ?>
