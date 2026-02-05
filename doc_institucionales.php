<?php
// documentos_institucionales.php - Usa TU conexion.php existente
require_once 'conexion.php';

// Consulta para documentos activos
$sql = "SELECT id, titulo, descripcion, url, tipo_archivo, fecha_publicacion, orden 
        FROM documentos_institucionales 
        WHERE activo = 1 
        ORDER BY orden ASC, fecha_publicacion DESC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    $documentos = [];
    error_log("Error consulta documentos: " . $conexion->error);
} else {
    $documentos = [];
    while ($row = $resultado->fetch_assoc()) {
        $documentos[] = $row;
    }
}
?>
<?php include 'head.php'; ?>


    <style>
        /* TU CSS COMPLETO + ESTILOS DOCUMENTOS */
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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

        .cabecera-logo {
            height: 70px;
            width: auto;
        }

        .cabecera-texto h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .cabecera-texto p {
            font-size: 0.9rem;
            color: var(--gris-medio);
        }

        /* BARRA VERDE NAVEGACIÓN */
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
            position: relative;
            padding: 0.9rem 0;
            font-size: 0.93rem;
            color: var(--blanco);
            text-decoration: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
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
            border-radius: 999px;
            transition: width 0.2s ease-out;
        }

        .item-menu:hover::after,
        .item-menu.activo::after {
            width: 70%;
        }

        .item-menu:hover {
            color: #fefce8;
        }

        /* RUTA DE NAVEGACIÓN */
        .seccion-breadcrumb {
            background: var(--blanco);
            padding: 1rem 0;
            box-shadow: var(--sombra-suave);
            margin-bottom: 1rem;
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

        .breadcrumb-link {
            color: var(--gris-medio);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb-link:hover {
            color: var(--verde-principal);
        }

        .breadcrumb-link.activo {
            color: var(--verde-principal);
            font-weight: 500;
        }

        .breadcrumb-separador {
            margin: 0 0.5rem;
            color: var(--gris-suave);
        }

        /* CONTENIDO PRINCIPAL */
        main {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 1.5rem 2rem;
        }

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

        .texto-intro {
            background: #f8fafc;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 3rem;
            border-left: 5px solid var(--verde-principal);
            text-align: center;
            font-size: 1.1rem;
            color: var(--gris-texto);
            line-height: 1.6;
        }

        /* GRID DOCUMENTOS - INTEGRADO CON TU ESTILO */
        .documentos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .documento-item {
            background: var(--blanco);
            padding: 1.8rem;
            border-radius: 12px;
            box-shadow: var(--sombra-suave);
            border-left: 4px solid var(--verde-principal);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .documento-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--verde-principal), var(--verde-muy-claro));
        }

        .documento-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(19,139,60,0.15);
            border-left-color: var(--verde-oscuro);
        }

        .documento-encabezado {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .documento-icono {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blanco);
            font-size: 1.3rem;
        }

        .documento-tipo {
            background: var(--verde-muy-claro);
            color: var(--verde-oscuro);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .documento-info h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gris-texto);
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }

        .documento-fecha {
            font-size: 0.85rem;
            color: var(--verde-principal);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .documento-descripcion {
            color: var(--gris-medio);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .btn-descargar {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.5rem;
            background: transparent;
            color: var(--verde-principal);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border: 2px solid var(--verde-principal);
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-descargar:hover {
            background: var(--verde-principal);
            color: var(--blanco);
            transform: translateX(4px);
        }

        /* SIN CONTENIDO */
        .sin-contenido {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gris-medio);
        }

        .sin-contenido i {
            font-size: 4rem;
            color: var(--gris-suave);
            margin-bottom: 1.5rem;
            display: block;
        }

        .sin-contenido h3 {
            font-size: 1.5rem;
            color: var(--gris-texto);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        /* PIE DE PÁGINA */
        .pie-pagina {
            background: #1a1a1a;
            color: #e5e7eb;
            padding: 4rem 0 0;
            margin-top: 4rem;
            font-size: 0.95rem;
            text-align: center;
        }

        .pie-contenedor {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 1.5rem 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            text-align: center;
            justify-items: center;
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

        .pie-logo {
            height: 70px;
            width: auto;
            display: block;
            margin: 0 auto 1rem;
        }

        .pie-descripcion {
            line-height: 1.6;
            color: #9ca3af;
            margin-bottom: 1.5rem;
        }

        .pie-lista {
            list-style: none;
            padding: 0;
        }

        .pie-lista li {
            margin-bottom: 0.8rem;
        }

        .pie-lista a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.2s;
        }

        .pie-lista a:hover {
            color: var(--verde-muy-claro);
        }

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

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .menu-contenedor {
                padding: 0 1rem;
                gap: 1rem;
            }

            .documentos-grid {
                grid-template-columns: 1fr;
                gap: 1.2rem;
            }

            .seccion-contenido {
                padding: 2rem 1.5rem;
            }

            .documento-item {
                padding: 1.5rem;
            }

            .breadcrumb-nav {
                font-size: 0.8rem;
                padding: 0 1rem;
                flex-wrap: wrap;
            }

            .pie-contenedor {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 480px) {
            .seccion-contenido-h2 {
                font-size: 1.8rem;
            }
            
            .documento-item {
                padding: 1.3rem;
            }
        }
    </style>
</head>
<body>

    

    <main>
        <section class="seccion-contenido">
            <h2 class="seccion-contenido-h2">Documentos Institucionales</h2>
            
            <div class="texto-intro">
                <i class="fas fa-file-archive" style="font-size: 2rem; margin-bottom: 1rem; display: block; color: var(--verde-principal);"></i>
                Todos los documentos oficiales e institucionales del centro organizados y actualizados. 
                Cumpliendo la normativa de transparencia educativa de la Comunidad de Madrid.
            </div>

            <?php if (!empty($documentos)): ?>
                <div class="documentos-grid">
                    <?php foreach ($documentos as $doc): ?>
                        <div class="documento-item">
                            <div class="documento-encabezado">
                                <div class="documento-icono">
                                    <?php 
                                    $iconos = [
                                        'pdf' => 'fas fa-file-pdf',
                                        'docx' => 'fas fa-file-word',
                                        'doc' => 'fas fa-file-word'
                                    ];
                                    echo '<i class="' . ($iconos[$doc['tipo_archivo']] ?? 'fas fa-file') . '"></i>';
                                    ?>
                                </div>
                                <div class="documento-tipo"><?php echo strtoupper(htmlspecialchars($doc['tipo_archivo'])); ?></div>
                                <div class="documento-info">
                                    <h3><?php echo htmlspecialchars($doc['titulo']); ?></h3>
                                    <div class="documento-fecha">
                                        <i class="fas fa-calendar-alt"></i> 
                                        <?php echo date('d/m/Y', strtotime($doc['fecha_publicacion'])); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="documento-descripcion">
                                <?php echo htmlspecialchars($doc['descripcion']); ?>
                            </p>
                            
                            <a href="<?php echo htmlspecialchars($doc['url']); ?>" 
                               class="btn-descargar" 
                               target="_blank" 
                               rel="noopener">
                                <i class="fas fa-download"></i>
                                Descargar documento
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sin-contenido">
                    <i class="fas fa-folder-open"></i>
                    <h3>No hay documentos disponibles</h3>
                    <p>En estos momentos no hay documentos institucionales publicados. 
                    Contacta con secretaría para más información.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    
       <?php include 'footer.php'; ?>

