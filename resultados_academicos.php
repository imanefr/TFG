<?php
// Incluye conexión a BD (require_once evita cargas múltiples)
require_once 'conexion.php';

// Consulta SQL: selecciona resultados académicos activos ordenados
$sql = "SELECT id, titulo, texto, imagen, orden 
        FROM resultados_academicos 
        WHERE activo = 1 
        ORDER BY orden ASC, id ASC";  // Orden personalizado + ID

$resultado = $conexion->query($sql);  // Ejecuta consulta

// Array para almacenar todos los resultados
$resultados = [];
if ($resultado) {
    // Recorre cada fila del resultado
    while ($row = $resultado->fetch_assoc()) {
        $resultados[] = $row;  // Añade al array
    }
}
// Cierra conexión BD
$conexion->close();
?>

<?php include 'head.php'; ?>

<!-- HEADER -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-chart-line icono_universal"></i>  <!-- Icono gráfico -->
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Resultados Académicos</h1>
                <p class="hero-subtitulo-universal">Excelencia educativa 2023-2024</p>
            </div>
        </div>
    </div>
</section>

<!-- RESULTADOS -->
<main class="resultados_academicos_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="resultados_academicos_titulo">Nuestros Resultados 2023-2024</h2>
            
            <?php if (!empty($resultados)): ?>  <!-- Si hay datos -->
                <!-- Grid responsive de cards -->
                <div class="resultados_academicos_grid">
                    <?php foreach ($resultados as $res): ?>  <!-- Por cada resultado -->
                        <div class="resultados_academicos_card">
                            <!-- Imagen con fallback (si no carga se oculta) -->
                            <img src="img/<?php echo htmlspecialchars(basename($res['imagen'])); ?>" 
                                 alt="<?php echo htmlspecialchars($res['titulo']); ?>" 
                                 class="resultados_academicos_imagen"
                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('resultados_academicos_placeholder_hidden');">
                            
                            <!-- Placeholder si imagen falla -->
                            <div class="resultados_academicos_placeholder resultados_academicos_placeholder_hidden">
                                <i class="fas fa-image resultados_academicos_placeholder_icono"></i>
                            </div>
                            
                            <!-- Contenido de la card -->
                            <h3 class="resultados_academicos_titulo_card"><?php echo htmlspecialchars($res['titulo']); ?></h3>
                            <p class="resultados_academicos_descripcion"><?php echo htmlspecialchars($res['texto']); ?></p>
                            <div class="resultados_academicos_anio">2023-2024</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>  <!-- Si NO hay datos -->
                <div class="resultados_academicos_vacio">
                    <i class="fas fa-chart-bar resultados_academicos_vacio_icono"></i>
                    <p>No hay datos disponibles.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>  <!-- Footer del sitio -->
