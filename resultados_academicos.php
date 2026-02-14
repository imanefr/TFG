<?php
require_once 'conexion.php';

$sql = "SELECT id, titulo, descripcion, img, orden 
        FROM resultados_academicos 
        WHERE activo = 1 
        ORDER BY orden ASC, id ASC";

$resultado = $conexion->query($sql);

$resultados = [];
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $resultados[] = $row;
    }
}
$conexion->close();
?>

<?php include 'head.php'; ?>

<!-- HEADER -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Resultados Académicos</h1>
                <p class="hero-subtitulo-universal">Excelencia educativa 2023-2024</p>
            </div>
        </div>
    </div>
</section>

<!-- RESULTADOS -->
<main class="resultados-academicos-pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="seccion-contenido-h2 resultados-titulo">Nuestros Resultados 2023-2024</h2>
            
            <?php if (!empty($resultados)): ?>
                <div class="resultados-grid">
                    <?php foreach ($resultados as $res): ?>
                        <div class="resultado-card">
                            <!-- ✅ SIEMPRE MUESTRA LA IMAGEN (sin file_exists) -->
                            <img src="img/<?php echo htmlspecialchars(basename($res['img'])); ?>" 
                                 alt="<?php echo htmlspecialchars($res['titulo']); ?>" 
                                 class="resultado-imagen"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            
                            <!-- PLACEHOLDER si falla la imagen -->
                            <div class="resultado-placeholder" style="display: none; background: #f8f9fa; border-radius: 8px; height: 200px; align-items: center; justify-content: center; margin-bottom: 1.2rem;">
                                <i class="fas fa-image" style="font-size: 2.5rem; color: var(--gris-medio);"></i>
                            </div>
                            
                            <h3 class="resultado-titulo"><?php echo htmlspecialchars($res['titulo']); ?></h3>
                            <p class="resultado-descripcion"><?php echo htmlspecialchars($res['descripcion']); ?></p>
                            <div class="resultado-anio">2023-2024</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="contenido-vacio-universal">
                    <i class="fas fa-chart-bar" style="font-size: 3rem; color: var(--gris-medio);"></i>
                    <p>No hay datos disponibles.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
