<?php
include("conexion.php");

// Datos de departamentos CON PÁGINAS INTERNAS
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
<?php include 'head.php'; ?>


    <style>
        /* HEADER DEPARTAMENTOS - SIN BORDE VERDE */
        .departamentos-contenido {
            padding: 3rem 1rem;
        }

        .departamentos-contenido .avisos-layout {
            background: var(--blanco) !important;
            box-shadow: var(--sombra-suave);
            border-radius: 12px;
            border: none !important;
        }

        /* GRID DEPARTAMENTOS - 4 POR FILA */
        .grid-departamentos {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin: 3rem 0;
        }

        .card-departamento {
            background: var(--blanco);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: var(--sombra-suave);
            transition: all 0.3s ease;
            border-top: 4px solid var(--verde-muy-claro);
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }

        .card-departamento::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--verde-principal), var(--verde-muy-claro));
        }

        .card-departamento:hover {
            transform: translateY(-8px);
            box-shadow: var(--sombra-fuerte);
            border-top-color: var(--verde-principal);
        }

        .icono-departamento {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-muy-claro));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            display: block;
        }

        .card-departamento h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gris-texto);
            margin: 0;
            line-height: 1.3;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .grid-departamentos {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid-departamentos {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .grid-departamentos {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <!-- HEADER DEPARTAMENTOS -->
    <section class="departamentos-contenido">
        <div class="contenedor-max">
            <div class="avisos-layout">
                <div class="avisos-logo">
                    <i class="fas fa-users"></i>
                </div>
                <div class="avisos-texto">
                    <h2>DEPARTAMENTOS</h2>
                    <p>Conoce nuestros departamentos docentes</p>
                </div>
            </div>
        </div>
    </section>

    <!-- GRID 4xN - REDIRECCIONA A PÁGINAS INTERNAS -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="grid-departamentos">
                <?php foreach ($departamentos as $dep): ?>
                    <a href="<?php echo htmlspecialchars($dep['pagina']); ?>" class="card-departamento">
                        <i class="<?php echo $dep['icono']; ?> icono-departamento"></i>
                        <h3><?php echo htmlspecialchars($dep['nombre']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

       <?php include 'footer.php'; ?>


    <script src="script.js"></script>
</body>
</html>
