<?php
require_once 'conexion.php';

// Consulta resultados académicos
$sql = "SELECT id, titulo, descripcion, img, orden 
        FROM resultados_academicos 
        WHERE activo = 1 
        ORDER BY orden ASC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    $resultados = [];
    error_log("Error consulta resultados: " . $conexion->error);
} else {
    $resultados = [];
    while ($row = $resultado->fetch_assoc()) {
        $resultados[] = $row;
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
        body { font-family: var(--fuente-base); background: #fafafa; color: var(--gris-texto); }

        /* HEADER BLANCO */
        .cabecera-top { background: var(--blanco); border-bottom: 1px solid var(--gris-suave); }
        .cabecera-contenido { max-width: 1150px; margin: 0 auto; padding: 0.8rem 1.5rem; display: flex; align-items: center; gap: 1rem; }
        .cabecera-logo { height: 70px; width: auto; }
        .cabecera-texto h1 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.2rem; }
        .cabecera-texto p { font-size: 0.9rem; color: var(--gris-medio); }

        /* BARRA VERDE */
        .barra-menu { background: var(--verde-principal); box-shadow: 0 2px 6px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
        .menu-contenedor { max-width: 1150px; margin: 0 auto; padding: 0 1.5rem; display: flex; align-items: center; gap: 1.5rem; }
        .item-menu { padding: 0.9rem 0; font-size: 0.93rem; color: var(--blanco); text-decoration: none; position: relative; transition: color 0.2s; }
        .item-menu::after { content: ""; position: absolute; left: 0; bottom: 0.25rem; width: 0; height: 2px; background: var(--blanco); transition: width 0.2s ease-out; }
        .item-menu:hover::after, .item-menu.activo::after { width: 70%; }
        .item-menu:hover { color: #fefce8; }

        /* BREADCRUMB */
        .seccion-breadcrumb { background: var(--blanco); padding: 1rem 0; box-shadow: var(--sombra-suave); }
        .breadcrumb-nav { max-width: 1150px; margin: 0 auto; padding: 0 1.5rem; display: flex; align-items: center; font-size: 0.85rem; color: var(--gris-medio); }
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

        /* HEADER RESULTADOS */
        .resultados-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e6f4ea 100%);
            padding: 2.5rem;
            border-radius: 16px;
            border-left: 6px solid var(--verde-principal);
            margin-bottom: 3rem;
            text-align: center;
        }

        .resultados-header i { font-size: 4rem; color: var(--verde-principal); margin-bottom: 1.5rem; display: block; }
        .resultados-header h3 { color: var(--verde-oscuro); font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem; }

        /* GRID RESULTADOS CON IMÁGENES */
        .resultados-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .resultado-item {
            background: var(--blanco);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--sombra-suave);
            transition: all 0.3s ease;
            border-top: 4px solid var(--verde-muy-claro);
            position: relative;
        }

        .resultado-item:hover {
            transform: translateY(-8px);
            box-shadow: var(--sombra-fuerte);
            border-top-color: var(--verde-principal);
        }

        .resultado-imagen {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8fafc, #e6f4ea);
        }

        .resultado-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .resultado-contenido {
            padding: 2rem;
        }

        .resultado-titulo {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gris-texto);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .resultado-desc {
            color: var(--gris-medio);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-size: 0.98rem;
        }

        .resultado-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            background: var(--verde-muy-claro);
            color: var(--verde-oscuro);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* SIN DATOS */
        .sin-contenido {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gris-medio);
        }

        .sin-contenido i { font-size: 4rem; color: var(--gris-suave); margin-bottom: 1.5rem; display: block; }
        .sin-contenido h3 { font-size: 1.5rem; color: var(--gris-texto); margin-bottom: 1rem; font-weight: 600; }

        /* FOOTER */
        .pie-pagina { background: #1a1a1a; color: #e5e7eb; padding: 4rem 0 0; margin-top: 4rem; }
        .pie-contenedor { max-width: 1150px; margin: 0 auto; padding: 0 1.5rem 3rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; text-align: center; }
        .pie-columna h3 { color: var(--blanco); font-size: 1.1rem; margin-bottom: 1.5rem; position: relative; padding-bottom: 0.5rem; }
        .pie-columna h3::after { content: ""; position: absolute; left: 50%; transform: translateX(-50%); bottom: 0; width: 40px; height: 2px; background: var(--verde-principal); }
        .pie-logo { height: 70px; width: auto; margin: 0 auto 1rem; }
        .pie-descripcion { line-height: 1.6; color: #9ca3af; margin-bottom: 1.5rem; }
        .pie-lista { list-style: none; }
        .pie-lista li { margin-bottom: 0.8rem; }
        .pie-lista a { color: #9ca3af; text-decoration: none; }
        .pie-lista a:hover { color: var(--verde-muy-claro); }
        .pie-inferior { background: #111; padding: 1.5rem 0; border-top: 1px solid #333; text-align: center; }
        .pie-inferior-contenido { max-width: 1150px; margin: 0 auto; padding: 0 1.5rem; color: #6b7280; font-size: 0.85rem; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .menu-contenedor { padding: 0 1rem; gap: 1rem; }
            .resultados-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .seccion-contenido { padding: 2rem 1.5rem; }
            .resultado-contenido { padding: 1.5rem; }
            .resultado-imagen { height: 180px; }
        }
    </style>
</head>
<body>

 
    <main>
        <section class="seccion-contenido">
            <h2 class="seccion-contenido-h2">Resultados Académicos</h2>
            
            <div class="resultados-header">
                <i class="fas fa-chart-line"></i>
                <h3>Excelencia educativa año tras año</h3>
                <p>Resultados oficiales verificados de ESO, Bachillerato y Formación Profesional.</p>
            </div>

            <?php if (!empty($resultados)): ?>
                <div class="resultados-grid">
                    <?php foreach ($resultados as $res): ?>
                        <div class="resultado-item">
                            <div class="resultado-imagen">
                                <?php if (file_exists($res['img'])): ?>
                                    <img src="<?php echo htmlspecialchars($res['img']); ?>" 
                                         alt="<?php echo htmlspecialchars($res['titulo']); ?>" 
                                         loading="lazy">
                                <?php else: ?>
                                    <div style="width:100%; height:100%; background:linear-gradient(135deg, #f8fafc, #e6f4ea); display:flex; align-items:center; justify-content:center; color:var(--gris-medio)">
                                        <i class="fas fa-chart-bar" style="font-size:3rem"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="resultado-contenido">
                                <h3 class="resultado-titulo"><?php echo htmlspecialchars($res['titulo']); ?></h3>
                                <p class="resultado-desc"><?php echo nl2br(htmlspecialchars($res['descripcion'])); ?></p>
                                <div class="resultado-stats">
                                    <span class="stat-item"><i class="fas fa-trophy"></i> Resultados oficiales</span>
                                    <span class="stat-item"><i class="fas fa-calendar"></i> 2023-2024</span>
                                    <span class="stat-item"><i class="fas fa-school"></i> IES La Arboleda</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sin-contenido">
                    <i class="fas fa-chart-bar"></i>
                    <h3>No hay datos disponibles</h3>
                    <p>Los resultados académicos se publicarán al finalizar cada curso escolar.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

                 <?php include 'footer.php'; ?>


</body>
</html>
