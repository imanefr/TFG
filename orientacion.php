<?php
require_once 'conexion.php';

// Consulta orientacion activa
$sql = "SELECT id, titulo, descripcion, enlace, orden 
        FROM orientacion 
        WHERE activo = 1 
        ORDER BY orden ASC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    $orientacion = [];
    error_log("Error consulta orientacion: " . $conexion->error);
} else {
    $orientacion = [];
    while ($row = $resultado->fetch_assoc()) {
        $orientacion[] = $row;
    }
}
?>

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
        }

        /* HEADER BLANCO */
        .cabecera-top {
            background: var(--blanco);
            border-bottom: 1px solid var(--gris-suave);
        }

        .cabecera-contenido {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cabecera-logo { height: 70px; width: auto; }

        .cabecera-texto h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .cabecera-texto p {
            font-size: 0.9rem;
            color: var(--gris-medio);
        }

        /* BARRA VERDE */
        .barra-menu {
            background: var(--verde-principal);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-contenedor {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .item-menu {
            padding: 0.9rem 0;
            font-size: 0.93rem;
            color: var(--blanco);
            text-decoration: none;
            position: relative;
            transition: color 0.2s;
        }

        .item-menu::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0.25rem;
            width: 0;
            height: 2px;
            background: var(--blanco);
            transition: width 0.2s ease-out;
        }

        .item-menu:hover::after, .item-menu.activo::after { width: 70%; }
        .item-menu:hover { color: #fefce8; }

        /* BREADCRUMB */
        .seccion-breadcrumb {
            background: var(--blanco);
            padding: 1rem 0;
            box-shadow: var(--sombra-suave);
        }

        .breadcrumb-nav {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: var(--gris-medio);
        }

        .breadcrumb-link { color: var(--gris-medio); text-decoration: none; }
        .breadcrumb-link:hover, .breadcrumb-link.activo { color: var(--verde-principal); font-weight: 500; }
        .breadcrumb-separador { margin: 0 0.5rem; color: var(--gris-suave); }

        /* MAIN */
        main { max-width: 1150px; margin: 0 auto; padding: 0 1.5rem 2rem; }

        .seccion-contenido {
            background: var(--blanco);
            border-radius: 12px;
            box-shadow: var(--sombra-suave);
            padding: 3rem 2.5rem;
            margin-bottom: 2rem;
        }

        .seccion-contenido-h2 {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--verde-principal);
            margin: 0 0 3rem 0;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            position: relative;
        }

        .seccion-contenido-h2::after {
            content: "";
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--verde-principal);
        }

        /* HEADER ORIENTACIÓN */
        .orientacion-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e6f4ea 100%);
            padding: 2.5rem;
            border-radius: 16px;
            border-left: 6px solid var(--verde-principal);
            margin-bottom: 3rem;
            text-align: center;
        }

        .orientacion-header i {
            font-size: 4rem;
            color: var(--verde-principal);
            margin-bottom: 1.5rem;
            display: block;
        }

        .orientacion-header h3 {
            color: var(--verde-oscuro);
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* SECCIONES ESO */
        .seccion-eso {
            margin-bottom: 3rem;
        }

        .eso-titulo {
            color: var(--verde-principal);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--verde-muy-claro);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .eso-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .opcion-item {
            background: var(--blanco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra-suave);
            border-left: 4px solid var(--verde-muy-claro);
            transition: all 0.3s ease;
        }

        .opcion-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-fuerte);
            border-left-color: var(--verde-principal);
        }

        .opcion-icono {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blanco);
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .opcion-titulo {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--gris-texto);
            margin-bottom: 0.8rem;
        }

        .opcion-desc {
            color: var(--gris-medio);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .opcion-enlace {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--verde-principal);
            text-decoration: none;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border: 2px solid var(--verde-principal);
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .opcion-enlace:hover {
            background: var(--verde-principal);
            color: var(--blanco);
            transform: translateX(4px);
        }

        /* SIN ESO */
        .sin-eso {
            background: linear-gradient(135deg, #fef2f2 0%, #fce7e7 100%);
            border-left-color: #ef4444 !important;
        }

        .sin-eso .opcion-icono {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .menu-contenedor { padding: 0 1rem; gap: 1rem; }
            .seccion-contenido { padding: 2rem 1.5rem; }
            .eso-grid { grid-template-columns: 1fr; }
            .orientacion-header { padding: 2rem 1.5rem; }
        }

        /* FOOTER */
        .pie-pagina {
            background: #1a1a1a;
            color: #e5e7eb;
            padding: 4rem 0 0;
            margin-top: 4rem;
        }

        .pie-contenedor {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 1.5rem 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .pie-columna h3 {
            color: var(--blanco);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .pie-columna h3::after {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 40px;
            height: 2px;
            background: var(--verde-principal);
        }

        .pie-logo { height: 70px; width: auto; margin: 0 auto 1rem; }
        .pie-descripcion { line-height: 1.6; color: #9ca3af; margin-bottom: 1.5rem; }

        .pie-lista { list-style: none; }
        .pie-lista li { margin-bottom: 0.8rem; }
        .pie-lista a { color: #9ca3af; text-decoration: none; }
        .pie-lista a:hover { color: var(--verde-muy-claro); }

        .pie-inferior {
            background: #111;
            padding: 1.5rem 0;
            border-top: 1px solid #333;
            text-align: center;
        }

        .pie-inferior-contenido {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 1.5rem;
            color: #6b7280;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    

    <main>
        <section class="seccion-contenido">
            <h2 class="seccion-contenido-h2">Orientación Post-ESO</h2>
            
            <div class="orientacion-header">
                <i class="fas fa-compass"></i>
                <h3>¿Qué hacer después de la ESO?</h3>
                <p>Te ayudamos a decidir tu futuro educativo con toda la información actualizada.</p>
            </div>

            <?php if (!empty($orientacion)): ?>
                <?php foreach ($orientacion as $item): ?>
                    <?php if (strpos(strtolower($item['titulo']), 'no has superado') !== false || strpos(strtolower($item['titulo']), 'sin') !== false): ?>
                        <div class="seccion-eso">
                            <h3 class="eso-titulo">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo htmlspecialchars($item['titulo']); ?>
                            </h3>
                            <div class="eso-grid">
                                <?php 
                                // Mostrar opciones relacionadas
                                $sql_opciones = "SELECT * FROM orientacion WHERE activo=1 AND orden > " . $item['orden'] . " ORDER BY orden ASC LIMIT 2";
                                $opciones_result = $conexion->query($sql_opciones);
                                while ($opcion = $opciones_result->fetch_assoc()): 
                                ?>
                                    <div class="opcion-item sin-eso">
                                        <div class="opcion-icono">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <h4 class="opcion-titulo"><?php echo htmlspecialchars($opcion['titulo']); ?></h4>
                                        <p class="opcion-desc"><?php echo htmlspecialchars($opcion['descripcion']); ?></p>
                                        <?php if ($opcion['enlace']): ?>
                                            <a href="<?php echo htmlspecialchars($opcion['enlace']); ?>" class="opcion-enlace" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> Más información
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="seccion-eso">
                            <h3 class="eso-titulo">
                                <i class="fas fa-check-circle"></i>
                                <?php echo htmlspecialchars($item['titulo']); ?>
                            </h3>
                            <div class="eso-grid">
                                <div class="opcion-item">
                                    <div class="opcion-icono">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <h4 class="opcion-titulo"><?php echo htmlspecialchars($item['titulo']); ?></h4>
                                    <p class="opcion-desc"><?php echo nl2br(htmlspecialchars($item['descripcion'])); ?></p>
                                    <?php if ($item['enlace']): ?>
                                        <a href="<?php echo htmlspecialchars($item['enlace']); ?>" class="opcion-enlace" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Ver detalles
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center; padding:4rem 2rem; color:var(--gris-medio);">
                    <i class="fas fa-folder-open" style="font-size:4rem; color:var(--gris-suave); margin-bottom:1.5rem; display:block;"></i>
                    <h3>Información en preparación</h3>
                    <p>El contenido de orientación está siendo actualizado. Contacta con el Departamento de Orientación.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

                  <?php include 'footer.php'; ?>


</body>
</html>
