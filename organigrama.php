<?php
include("conexion.php");

// Consulta ordenada por secciones específicas
$query = "SELECT * FROM organigrama ORDER BY 
    FIELD(seccion, 'Equipo Directivo', 'Consejo Escolar', 'Claustro'),
    id ASC";
$result = mysqli_query($conexion, $query);

$datos = [];
if ($result) {
    while ($fila = mysqli_fetch_assoc($result)) {
        $datos[$fila["seccion"]][] = $fila;
    }
    mysqli_free_result($result);
}
mysqli_close($conexion);
?>

<?php include 'head.php'; ?>

<!-- HEADER ORGANIGRAMA -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Organigrama</h1>
                <p class="hero-subtitulo-universal">Estructura organizativa del centro educativo.</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="organigrama-pagina">
    <section class="seccion-contenido">
        <h2 class="seccion-contenido-h2">Organización Institucional</h2>

        <?php if (!empty($datos)): ?>
            <?php foreach ($datos as $seccion => $miembros): ?>
                <div class="seccion-bloque">
                    <h3 class="titulo-seccion-org"><?php echo htmlspecialchars($seccion); ?></h3>
                    <table class="tabla-organigrama">
                        <thead>
                            <tr>
                                <th>Cargo</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($miembros as $persona): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($persona['cargo']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['nombre']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="seccion-bloque">
                <p style="text-align: center; padding: 2rem; color: var(--gris-medio);">
                    No hay datos disponibles en este momento.
                </p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

<script src="script.js"></script>
</body>
</html>
