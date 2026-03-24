<?php
// PÁGINA DEPARTAMENTOS 
// Listado completo 20 departamentos didácticos con enlaces directos
include("conexion.php"); // Conexión preparada para contenido dinámico futuro

// ARRAY COMPLETO - 20 departamentos organizados por áreas académicas
$departamentos = [
    ['nombre' => 'Actividades Extraescolares', 'pagina' => 'info_departamento.php?id=1', 'icono' => 'fas fa-star'],
    ['nombre' => 'Biblioteca', 'pagina' => 'info_departamento.php?id=2', 'icono' => 'fas fa-book'],
    ['nombre' => 'Biología y Geología', 'pagina' => 'info_departamento.php?id=3', 'icono' => 'fas fa-leaf'],
    ['nombre' => 'Dibujo', 'pagina' => 'info_departamento.php?id=4', 'icono' => 'fas fa-pencil-alt'],
    
    ['nombre' => 'Economía', 'pagina' => 'info_departamento.php?id=5', 'icono' => 'fas fa-chart-line'],
    ['nombre' => 'Educación Física', 'pagina' => 'info_departamento.php?id=6', 'icono' => 'fas fa-dumbbell'],
    ['nombre' => 'Filosofía', 'pagina' => 'info_departamento.php?id=7', 'icono' => 'fas fa-brain'],
    ['nombre' => 'Física y Química', 'pagina' => 'info_departamento.php?id=8', 'icono' => 'fas fa-flask'],
    
    ['nombre' => 'Francés', 'pagina' => 'info_departamento.php?id=9', 'icono' => 'fas fa-flag'],
    ['nombre' => 'FOL', 'pagina' => 'info_departamento.php?id=10', 'icono' => 'fas fa-briefcase'],
    ['nombre' => 'Geografía e Historia', 'pagina' => 'info_departamento.php?id=11', 'icono' => 'fas fa-globe'],
    ['nombre' => 'Imagen Personal', 'pagina' => 'info_departamento.php?id=12', 'icono' => 'fas fa-cut'],
    
    ['nombre' => 'Imagen y Sonido', 'pagina' => 'info_departamento.php?id=13', 'icono' => 'fas fa-video'],
    ['nombre' => 'Informática', 'pagina' => 'info_departamento.php?id=14', 'icono' => 'fas fa-laptop'],
    ['nombre' => 'Inglés', 'pagina' => 'info_departamento.php?id=15', 'icono' => 'fas fa-language'],
    ['nombre' => 'Lengua Castellana y Literatura', 'pagina' => 'info_departamento.php?id=16', 'icono' => 'fas fa-font'],
    
    ['nombre' => 'Matemáticas', 'pagina' => 'info_departamento.php?id=17', 'icono' => 'fas fa-calculator'],
    ['nombre' => 'Música', 'pagina' => 'info_departamento.php?id=18', 'icono' => 'fas fa-music'],
    ['nombre' => 'Orientación', 'pagina' => 'info_departamento.php?id=19', 'icono' => 'fas fa-compass'],
    ['nombre' => 'Religión', 'pagina' => 'info_departamento.php?id=20', 'icono' => 'fas fa-pray'],
    
    ['nombre' => 'Tecnología', 'pagina' => 'info_departamento.php?id=21', 'icono' => 'fas fa-cogs']
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
