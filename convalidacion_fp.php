<?php include 'head.php'; ?>

    <style>
        :root {
            --verde-principal: #138b3c;
            --verde-oscuro: #0b5c28;
            --verde-muy-claro: #22c55e;
            --gris-texto: #222222;
            --gris-suave: #e5e7eb;
            --gris-medio: #6b7280;
            --blanco: #ffffff;
            --sombra-suave: 0 8px 18px rgba(0,0,0,0.06);
            --sombra-fuerte: 0 12px 24px rgba(0,0,0,0.12);
            --fuente-base: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: var(--fuente-base);
            background: #fafafa; 
            color: var(--gris-texto);
            line-height: 1.6;
        }

        /* HEADER */
        .cabecera-top {
            background: var(--blanco);
            border-bottom: 1px solid var(--gris-suave);
        }
        .cabecera-contenido {
            max-width: 1150px; margin: 0 auto;
            padding: 0.8rem 1.5rem; display: flex;
            align-items: center; gap: 1rem;
        }
        .cabecera-logo { height: 70px; width: auto; }
        .cabecera-texto h1 { 
            font-size: 1.25rem; font-weight: 600; 
            margin-bottom: 0.2rem;
        }
        .cabecera-texto p { 
            font-size: 0.9rem; color: var(--gris-medio); 
        }

        /* NAVEGACIÓN */
        .barra-menu {
            background: var(--verde-principal);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            position: sticky; top: 0; z-index: 100;
        }
        .menu-contenedor {
            max-width: 1150px; margin: 0 auto;
            padding: 0 1.5rem; display: flex;
            align-items: center; gap: 1.5rem;
        }
        .item-menu {
            color: white; text-decoration: none;
            padding: 1rem 0; font-weight: 500;
            position: relative;
        }
        .item-menu:hover, .item-menu.activo { 
            color: #fefce8; 
        }
        .item-menu.activo::after {
            content: ""; position: absolute;
            bottom: 0; left: 0; width: 70%;
            height: 2px; background: white;
        }
        .desplegable { position: relative; }
        .icono-desplegable { 
            font-size: 0.8rem; margin-left: 0.3rem; 
        }
        .submenu {
            position: absolute; top: 100%; left: 0;
            background: var(--verde-oscuro); min-width: 200px;
            border-radius: 0 0 10px 10px; display: none;
            box-shadow: var(--sombra-fuerte);
        }
        .desplegable:hover .submenu { display: block; }
        .submenu-titulo {
            display: block; padding: 0.8rem 1.2rem;
            color: #e5fbe9; text-decoration: none;
            font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .submenu-titulo:hover { 
            background: rgba(255,255,255,0.15); 
        }
        .submenu-titulo.activo { 
            background: rgba(255,255,255,0.2); 
        }
        .nav-toggle {
            margin-left: auto; background: none;
            border: 1px solid #bbf7d0; color: #dcfce7;
            padding: 0.5rem; border-radius: 5px; cursor: pointer;
            display: none;
        }

        /* BREADCRUMB */
        .seccion-breadcrumb {
            background: var(--blanco);
            padding: 1rem 0; box-shadow: var(--sombra-suave);
        }
        .contenedor-max {
            max-width: 1150px; margin: 0 auto; padding: 0 1.5rem;
        }
        .breadcrumb-nav {
            display: flex; align-items: center;
            font-size: 0.9rem; color: var(--gris-medio);
        }
        .breadcrumb-link { 
            color: var(--gris-medio); text-decoration: none;
            margin-right: 0.5rem;
        }
        .breadcrumb-link:hover { color: var(--verde-principal); }
        .breadcrumb-link.activo { 
            color: var(--verde-principal); font-weight: 600; 
        }
        .breadcrumb-separador { margin: 0 0.5rem; color: var(--gris-suave); }

        /* CONTENIDO PRINCIPAL */
        .seccion-contenido {
            background: var(--blanco); border-radius: 12px;
            box-shadow: var(--sombra-suave); padding: 2rem;
            margin: 2rem auto; max-width: 1150px;
        }

        /* CONVALIDACIÓN */
        .convalidacion-contenido { padding: 2rem 0; }
        .convalidacion-layout {
            display: flex; gap: 2rem; align-items: flex-start;
            background: var(--blanco); padding: 2.5rem;
            border-radius: 12px; box-shadow: var(--sombra-suave);
            margin: 2rem 0; border-left: 5px solid var(--verde-principal);
        }
        .convalidacion-logo {
            flex-shrink: 0; text-align: center; padding: 1rem;
            min-width: 300px;
        }
        .convalidacion-logo img, 
        .convalidacion-logo iframe {
            width: 100%; max-width: 300px; height: 220px;
            border-radius: 12px; box-shadow: var(--sombra-fuerte);
            object-fit: cover;
        }
        .convalidacion-texto { flex: 1; }
        .convalidacion-texto h1 {
            color: var(--verde-principal); font-size: 2.2rem;
            margin-bottom: 1rem; text-transform: uppercase;
            letter-spacing: 0.03em; font-weight: 700;
        }
        .texto-principal {
            color: var(--gris-texto); font-size: 1.1rem;
            line-height: 1.7; margin-bottom: 2rem; text-align: justify;
        }
        .convalidacion-acciones {
            display: flex; gap: 1rem; flex-wrap: wrap;
        }
        .btn-normativa, .btn-formulario {
            display: inline-flex; align-items: center; gap: 0.6rem;
            padding: 1rem 2rem; border-radius: 25px; font-weight: 700;
            text-decoration: none; transition: all 0.3s ease;
            font-size: 0.95rem; text-transform: uppercase;
        }
        .btn-normativa {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white !important;
        }
        .btn-normativa:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,123,255,0.3);
        }
        .btn-formulario {
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            color: white !important;
        }
        .btn-formulario:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(19,139,60,0.4);
        }
        .convalidacion-sin-media {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 3rem 2.5rem; border-radius: 16px;
            margin: 2rem 0; text-align: center; box-shadow: var(--sombra-suave);
        }
        .convalidacion-icono {
            font-size: 4.5rem; color: var(--verde-principal);
            margin-bottom: 1.5rem; display: block;
        }
        .aviso-importante {
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
            border-left: 5px solid #ef4444; padding: 1.5rem;
            border-radius: 8px; margin: 2rem 0;
            box-shadow: 0 4px 15px rgba(239,68,68,0.15);
        }
        .info-convalidacion {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem; margin-top: 3rem; padding-top: 2rem;
            border-top: 2px solid var(--gris-suave);
        }
        .info-card-convalidacion {
            background: var(--blanco); padding: 2rem;
            border-radius: 12px; text-align: center;
            box-shadow: var(--sombra-suave); 
            border-top: 4px solid var(--verde-muy-claro);
            transition: all 0.3s ease;
        }
        .info-card-convalidacion:hover {
            transform: translateY(-5px); box-shadow: var(--sombra-fuerte);
        }
        .info-card-convalidacion i {
            font-size: 3rem; color: var(--verde-principal);
            margin-bottom: 1rem; display: block;
        }
        .sin-convalidacion {
            text-align: center; padding: 4rem 2rem;
            color: var(--gris-medio);
        }
        .sin-convalidacion i {
            font-size: 5rem; color: var(--gris-suave);
            margin-bottom: 2rem; display: block;
        }

        /* FOOTER */
        .pie-pagina {
            background: #1a1a1a; color: #e5e7eb;
            padding: 3rem 0; margin-top: 4rem;
        }
        .pie-contenedor {
            max-width: 1150px; margin: 0 auto;
            padding: 0 1.5rem; display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem; text-align: center;
        }
        .pie-columna h3 {
            color: white; font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        .pie-logo { height: 60px; margin-bottom: 1rem; }
        .pie-descripcion { 
            color: #9ca3af; margin-bottom: 1rem; 
        }
        .pie-lista { list-style: none; padding: 0; }
        .pie-lista li { margin-bottom: 0.5rem; }
        .pie-lista i { margin-right: 0.5rem; color: var(--verde-muy-claro); }
        .pie-inferior {
            background: #111; padding: 1rem 0;
            text-align: center; color: #6b7280;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .menu-contenedor { gap: 0.5rem; }
            .nav-toggle { display: block; }
            .convalidacion-layout { 
                flex-direction: column; text-align: center; 
                padding: 1.5rem;
            }
            .convalidacion-acciones { 
                flex-direction: column; align-items: center; 
            }
            .btn-normativa, .btn-formulario {
                width: 100%; max-width: 300px; justify-content: center;
            }
            .info-convalidacion { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    

    <?php include("conexion.php"); ?>

    <main>
        <section class="seccion-contenido">
            <div class="convalidacion-contenido">
                <?php
                $sql_convalidacion = "SELECT titulo, texto, imagen, tipo_imagen, enlace_video, enlace_normativa, enlace_formulario 
                                    FROM convalidaciones WHERE tipo = 'FP' AND activo = 1 LIMIT 1";
                $resultado_convalidacion = $conexion->query($sql_convalidacion);

                if ($resultado_convalidacion && $fila_convalidacion = $resultado_convalidacion->fetch_assoc()) {
                    $hay_media = !empty($fila_convalidacion['imagen']) || !empty($fila_convalidacion['enlace_video']);
                ?>

                <?php if ($hay_media): ?>
                    <div class="convalidacion-layout">
                        <div class="convalidacion-logo">
                            <?php if (!empty($fila_convalidacion['imagen'])): ?>
                                <img src="data:<?php echo $fila_convalidacion['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila_convalidacion['imagen']); ?>" alt="Convalidación FP">
                            <?php elseif (!empty($fila_convalidacion['enlace_video'])): ?>
                                <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila_convalidacion['enlace_video'])); ?>" frameborder="0" allowfullscreen></iframe>
                            <?php endif; ?>
                        </div>
                        <div class="convalidacion-texto">
                            <h1><?php echo htmlspecialchars($fila_convalidacion['titulo']); ?></h1>
                            <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_convalidacion['texto'])); ?></div>
                            <div class="convalidacion-acciones">
                                <?php if (!empty($fila_convalidacion['enlace_normativa'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                        <i class="fas fa-file-pdf"></i> Normativa oficial
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($fila_convalidacion['enlace_formulario'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_formulario']); ?>" target="_blank" class="btn-formulario">
                                        <i class="fas fa-edit"></i> Formulario solicitud
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="convalidacion-sin-media">
                        <i class="fas fa-file-signature convalidacion-icono"></i>
                        <h1><?php echo htmlspecialchars($fila_convalidacion['titulo']); ?></h1>
                        <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_convalidacion['texto'])); ?></div>
                        <div class="convalidacion-acciones">
                            <?php if (!empty($fila_convalidacion['enlace_normativa'])): ?>
                                <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                    <i class="fas fa-file-pdf"></i> Normativa oficial
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($fila_convalidacion['enlace_formulario'])): ?>
                                <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_formulario']); ?>" target="_blank" class="btn-formulario">
                                    <i class="fas fa-edit"></i> Formulario solicitud
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- AVISO IMPORTANTE -->
                <div class="aviso-importante">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>IMPORTANTE:</strong> NO SE ADMITIRÁN FORMULARIOS ESCRITOS A MANO. Descargue, rellene digitalmente y entréguelo en secretaría o por registro electrónico.
                </div>

                <!-- INFO CARDS -->
                <div class="info-convalidacion">
                    <div class="info-card-convalidacion">
                        <i class="fas fa-file-contract"></i>
                        <h4>Normativa oficial</h4>
                        <p>Consulta la normativa autonómica y estatal sobre convalidaciones en Formación Profesional.</p>
                    </div>
                    <div class="info-card-convalidacion">
                        <i class="fas fa-download"></i>
                        <h4>Formulario digital</h4>
                        <p>Descargue el formulario oficial en PDF, cumpliméntelo digitalmente y preséntelo firmado.</p>
                    </div>
                    <div class="info-card-convalidacion">
                        <i class="fas fa-clock"></i>
                        <h4>Plazos de presentación</h4>
                        <p>Consulte los plazos establecidos por la Comunidad de Madrid o el centro educativo.</p>
                    </div>
                </div>

                <?php } else { ?>
                    <div class="sin-convalidacion">
                        <i class="fas fa-file-search"></i>
                        <h1>Convalidación FP</h1>
                        <p>No hay información disponible en este momento.</p>
                        <p><strong>Secretaría:</strong> 916 43 99 91</p>
                    </div>
                <?php } 
                $conexion->close();
                ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
