<?php
include("conexion.php");

// Consulta datos del curso
$sql = "SELECT titulo, contenido, fecha, enlace FROM curso_videojuegos ORDER BY fecha DESC LIMIT 8";
$resultado = $conexion->query($sql);
$info_curso = [];
while ($fila = $resultado->fetch_assoc()) {
    $info_curso[] = $fila;
}
?>
<?php include 'head.php'; ?>


        <style>
            /* HEADER VIDEOJUEGOS - SIN NINGÚN BORDE VERDE */
            .videojuegos-contenido {
                padding: 3rem 1rem;
            }

            .videojuegos-contenido .avisos-layout {
                background: var(--blanco) !important;
                /* ❌ SIN BORDE VERDE - LImpIO */
                box-shadow: var(--sombra-suave);
                border-radius: 12px;
                border: none !important;
            }

            /* BLOQUEO TOTAL DE BORDES */
            .videojuegos-contenido .avisos-layout,
            .videojuegos-contenido .avisos-layout * {
                border-left: none !important;
                border-right: none !important;
                border-top: none !important;
                border-bottom: none !important;
            }

            .videojuegos-contenido .avisos-logo i {
                font-size: 3.5rem;
                color: var(--verde-principal);
            }

            .videojuegos-contenido .avisos-texto h2 {
                color: var(--verde-principal);
                font-size: 1.6rem;
                font-weight: 700;
                margin-bottom: 0.3rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            /* GRID INFO CURSO - SOLO EN ARRIBA */
            .info-curso-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
                gap: 2rem;
                margin-bottom: 4rem;
            }

            .info-card {
                background: var(--blanco);
                border-radius: 20px;
                padding: 2.5rem 2rem;
                box-shadow: var(--sombra-suave);
                border-top: 5px solid var(--verde-principal);
                transition: all 0.3s ease;
            }

            .info-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--sombra-fuerte);
            }

            .info-icon i {
                font-size: 3.5rem;
                background: linear-gradient(135deg, var(--verde-principal), var(--verde-muy-claro));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                text-align: center;
                margin-bottom: 1.5rem;
                display: block;
            }

            .info-card h3 {
                font-size: 1.3rem;
                font-weight: 700;
                color: var(--gris-texto);
                margin-bottom: 1.5rem;
                text-align: center;
            }

            .info-card ul {
                list-style: none;
                padding: 0;
                margin: 0 0 2rem 0;
            }

            .info-card li {
                padding: 0.8rem 0 0.8rem 2rem;
                position: relative;
                color: var(--gris-texto);
                margin-bottom: 0.5rem;
                font-weight: 500;
            }

            .info-card li::before {
                content: "🎮";
                position: absolute;
                left: 0;
                top: 0.7rem;
                font-size: 1.1rem;
            }

            /* RESPONSIVE */
            @media (max-width: 768px) {
                .videojuegos-contenido .avisos-layout {
                    flex-direction: column;
                    text-align: center;
                    gap: 1rem;
                    padding: 1.2rem;
                }
                .info-curso-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
       

        <!-- HEADER VIDEOJUEGOS -->
        <section class="videojuegos-contenido">
            <div class="contenedor-max">
                <div class="avisos-layout">
                    <div class="avisos-logo">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="avisos-texto">
                        <h2>DESARROLLO DE VIDEOJUEGOS</h2>
                        <p>Curso Especialización GS · Realidad Virtual</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- INFO CURSO -->
        <section class="seccion-contenido">
            <div class="contenedor-max">
                <?php if (!empty($info_curso)): ?>
                    <div class="info-curso-grid">
                        <?php foreach ($info_curso as $item): ?>
                            <div class="info-card">
                                <div class="info-icon">
                                    <?php if (strpos($item['titulo'], 'Certificados') !== false): ?>
                                        <i class="fas fa-certificate"></i>
                                    <?php elseif (strpos($item['titulo'], 'Plan') !== false): ?>
                                        <i class="fas fa-list-check"></i>
                                    <?php elseif (strpos($item['titulo'], 'Requisitos') !== false): ?>
                                        <i class="fas fa-clipboard-check"></i>
                                    <?php else: ?>
                                        <i class="fas fa-gamepad"></i>
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                                <ul>
                                    <?php
                                    $texto = strip_tags($item['contenido']);
                                    $items = explode('✓', $texto);
                                    foreach ($items as $linea) {
                                        $linea = trim($linea);
                                        if (!empty($linea)) {
                                            echo "<li>$linea</li>";
                                        }
                                    }
                                    ?>
                                </ul>
                                <?php if ($item['enlace']): ?>
                                    <a href="<?php echo htmlspecialchars($item['enlace']); ?>" class="aviso-enlace" target="_blank">
                                        Leer completo <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <img src="img/logo-certificado.jpg" alt="Logo IES La Arboleda Videojuegos" class="certificado-logo">

                <?php else: ?>
                    <div class="sin-contenido">
                        <i class="fas fa-gamepad"></i>
                        <h3>Información próximamente</h3>
                        <p>Próximamente información completa del curso de desarrollo de videojuegos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

               <?php include 'footer.php'; ?>


        <script src="script.js"></script>
    </body>
</html>
