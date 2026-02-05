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

        /* SOLICITUD TÍTULOS */
        .solicitud-contenido { padding: 2rem 0; }
        .solicitud-layout {
            display: flex; gap: 2rem; align-items: flex-start;
            background: var(--blanco); padding: 2.5rem;
            border-radius: 12px; box-shadow: var(--sombra-suave);
            margin: 2rem 0; border-left: 5px solid var(--verde-principal);
        }
        .solicitud-icono {
            flex-shrink: 0; text-align: center; padding: 1rem;
            min-width: 300px;
        }
        .solicitud-icono img, .solicitud-icono i {
            font-size: 6rem; color: var(--verde-principal);
            width: 100%; max-width: 300px; height: 220px;
            border-radius: 12px; box-shadow: var(--sombra-fuerte);
            object-fit: cover;
        }
        .solicitud-texto { flex: 1; }
        .solicitud-texto h1 {
            color: var(--verde-principal); font-size: 2.2rem;
            margin-bottom: 1rem; text-transform: uppercase;
            letter-spacing: 0.03em; font-weight: 700;
        }
        .texto-principal {
            color: var(--gris-texto); font-size: 1.1rem;
            line-height: 1.7; margin-bottom: 2rem; text-align: justify;
        }
        .solicitud-horario {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            padding: 1.5rem; border-radius: 10px;
            border-left: 4px solid var(--verde-principal);
            margin: 2rem 0;
        }
        .solicitud-horario i {
            color: var(--verde-principal); font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .solicitud-tasa {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            padding: 1.5rem; border-radius: 10px;
            border-left: 4px solid #3b82f6;
            margin: 2rem 0;
        }
        .solicitud-tasa i {
            color: #3b82f6; font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .solicitud-acciones {
            display: flex; gap: 1rem; flex-wrap: wrap;
        }
        .btn-normativa, .btn-tasa, .btn-autorizacion {
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
        .btn-tasa {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white !important;
        }
        .btn-tasa:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16,185,129,0.4);
        }
        .btn-autorizacion {
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            color: white !important;
        }
        .btn-autorizacion:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(19,139,60,0.4);
        }
        .aviso-importante {
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
            border-left: 5px solid #ef4444; padding: 1.5rem;
            border-radius: 8px; margin: 2rem 0;
            box-shadow: 0 4px 15px rgba(239,68,68,0.15);
        }
        .info-solicitud {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem; margin-top: 3rem; padding-top: 2rem;
            border-top: 2px solid var(--gris-suave);
        }
        .info-card-solicitud {
            background: var(--blanco); padding: 2rem;
            border-radius: 12px; text-align: center;
            box-shadow: var(--sombra-suave); 
            border-top: 4px solid var(--verde-muy-claro);
            transition: all 0.3s ease;
        }
        .info-card-solicitud:hover {
            transform: translateY(-5px); box-shadow: var(--sombra-fuerte);
        }
        .info-card-solicitud i {
            font-size: 3rem; color: var(--verde-principal);
            margin-bottom: 1rem; display: block;
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
            .solicitud-layout { 
                flex-direction: column; text-align: center; 
                padding: 1.5rem;
            }
            .solicitud-acciones { 
                flex-direction: column; align-items: center; 
            }
            .btn-normativa, .btn-tasa, .btn-autorizacion {
                width: 100%; max-width: 300px; justify-content: center;
            }
            .info-solicitud { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

     

    <?php include("conexion.php"); ?>

    <main>
        <section class="seccion-contenido">
            <div class="solicitud-contenido">
                <?php
                $sql_solicitud = "SELECT * FROM solicitudes_titulos WHERE tipo = 'BACHILLERATO' AND activo = 1 LIMIT 1";
                $resultado_solicitud = $conexion->query($sql_solicitud);

                if ($resultado_solicitud && $fila_solicitud = $resultado_solicitud->fetch_assoc()) {
                    $hay_media = !empty($fila_solicitud['imagen']);
                ?>

                <?php if ($hay_media): ?>
                    <div class="solicitud-layout">
                        <div class="solicitud-icono">
                            <img src="data:<?php echo $fila_solicitud['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila_solicitud['imagen']); ?>" alt="Título Bachillerato">
                        </div>
                        <div class="solicitud-texto">
                            <h1><?php echo htmlspecialchars($fila_solicitud['titulo']); ?></h1>
                            <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_solicitud['texto'])); ?></div>
                            <div class="solicitud-acciones">
                                <?php if (!empty($fila_solicitud['enlace_normativa'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                        <i class="fas fa-file-pdf"></i> Normativa oficial
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($fila_solicitud['enlace_autorizacion'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_autorizacion']); ?>" target="_blank" class="btn-autorizacion">
                                        <i class="fas fa-edit"></i> Autorización terceros
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="solicitud-layout">
                        <div class="solicitud-icono">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="solicitud-texto">
                            <h1><?php echo htmlspecialchars($fila_solicitud['titulo']); ?></h1>
                            <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_solicitud['texto'])); ?></div>
                            <div class="solicitud-acciones">
                                <?php if (!empty($fila_solicitud['enlace_normativa'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                        <i class="fas fa-file-pdf"></i> Normativa oficial
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($fila_solicitud['enlace_autorizacion'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_autorizacion']); ?>" target="_blank" class="btn-autorizacion">
                                        <i class="fas fa-edit"></i> Autorización terceros
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- HORARIO -->
                <div class="solicitud-horario">
                    <i class="fas fa-clock"></i>
                    <h3>📅 Horario recogida títulos</h3>
                    <p><strong><?php echo htmlspecialchars($fila_solicitud['horario'] ?? 'L-V 9:30-12:00'); ?></strong></p>
                    <p><em>En Secretaría del Centro, previa identificación con DNI/NIE.</em></p>
                </div>

                <!-- TASA PAGO -->
                <div class="solicitud-tasa">
                    <i class="fas fa-euro-sign"></i>
                    <h3>💰 Pago tasa modelo 030</h3>
                    <p><strong>Tasa aproximada: 51,49€</strong></p>
                    <p><em>Pago online en <a href="https://sede.comunidad.madrid" target="_blank">sede.comunidad.madrid</a></em></p>
                </div>

                <!-- AVISO IMPORTANTE -->
                <div class="aviso-importante">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>IMPORTANTE:</strong> Necesitará justificante de pago del modelo 030 + DNI/NIE. Verifique disponibilidad en secretaría antes de tramitar.
                </div>

                <!-- INFO CARDS -->
                <div class="info-solicitud">
                    <div class="info-card-solicitud">
                        <i class="fas fa-id-card"></i>
                        <h4>Documentación</h4>
                        <p>Justificante modelo 030 + DNI/NIE original.</p>
                    </div>
                    <div class="info-card-solicitud">
                        <i class="fas fa-clock"></i>
                        <h4>Secretaría</h4>
                        <p><?php echo htmlspecialchars($fila_solicitud['horario'] ?? '9:30-12:00 L-V'); ?>.</p>
                    </div>
                    <div class="info-card-solicitud">
                        <i class="fas fa-credit-card"></i>
                        <h4>Pago Tasa</h4>
                        <p>Modelo 030: ~51,49€ online Comunidad Madrid.</p>
                    </div>
                </div>

                <?php } else { ?>
                    <div style="text-align: center; padding: 4rem 2rem; color: var(--gris-medio);">
                        <i class="fas fa-file-search" style="font-size: 5rem; color: var(--gris-suave); margin-bottom: 2rem; display: block;"></i>
                        <h1>Solicitud Título Bachillerato</h1>
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


</body>
</html>
