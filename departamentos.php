<?php
include("conexion.php"); // Conexión a la base de datos (por si en el futuro se añade contenido dinámico)

// Array con los departamentos del centro y su información de ruta e icono.
// Cada elemento contiene: nombre visible, archivo de destino y clase del icono (Font Awesome)
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

<?php include 'head.php'; ?> <!-- Cabecera común (metaetiquetas, estilos y scripts) -->

<main class="departamentos-pagina">
    <!-- Sección de cabecera del bloque de departamentos -->
    <section class="seccion-hero-universal departamentos-hero">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <!-- Icono general para la página de departamentos -->
                    <i class="fas fa-users" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Departamentos</h1>
                    <p class="hero-subtitulo-universal">Asociación de Madres y Padres del Alumnado</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección principal que muestra el grid de departamentos (4 columnas adaptables) -->
    <section class="seccion-contenido departamentos-contenido">
        <div class="contenedor-max">
            <div class="grid-departamentos">
                <!-- Bucle que genera automáticamente cada tarjeta de departamento -->
                <?php foreach ($departamentos as $dep): ?>
                    <a href="<?php echo htmlspecialchars($dep['pagina']); ?>" class="card-departamento">
                        <i class="<?php echo $dep['icono']; ?> icono-departamento"></i>
                        <h3><?php echo htmlspecialchars($dep['nombre']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?> <!-- Pie de página común -->

<!-- Script adicional general (interactividad o animaciones) -->
<script src="script.js"></script>
