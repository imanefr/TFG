<?php include 'head.php'; ?>  <!-- Carga el head.php -->

<?php
include("conexion.php");  // Conecta a base de datos MySQLi

// Consulta: TODOS avisos activos ordenados (importantes primero, luego fecha reciente)
$sql = "SELECT a.*, u.nombre as ultima_edicion_usuario_nombre
        FROM avisos a 
        LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id 
        ORDER BY a.importante DESC, a.fecha DESC";  // Prioriza avisos marcados importante
$resultado = $conexion->query($sql);  // Ejecuta consulta SQL
?>

<!-- HEADER AVISOS -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users" class="icono_universal"></i> 
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Avisos del Centro</h1>  <!-- Título H1 página -->
                <p class="hero-subtitulo-universal">Comunicaciones oficiales, plazos importantes y novedades administrativas.</p>  <!-- Subtítulo descriptivo -->
            </div>
        </div>
    </div>
</section>

<!-- LISTA DE AVISOS -->
<main class="info_avisos_pagina">  <!-- Contenedor principal página -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="info_avisos_titulo">Todos los Avisos</h2>  <!-- Título sección avisos -->

            <?php if ($resultado && $resultado->num_rows > 0): ?>  <!-- Verifica si hay resultados -->
                <div class="info_avisos_lista">  <!-- Contenedor grid/lista avisos -->
                    <?php while ($fila = $resultado->fetch_assoc()): ?>  <!-- Recorre CADA aviso -->
                        <!-- Cada aviso individual con clase especial si es importante -->
                        <div class="info_avisos_item <?php echo $fila['importante'] ? 'info_avisos_importante' : ''; ?>">
                            
                            <?php if ($fila['importante']): ?>  <!-- BADGE si es importante -->
                                <div class="info_avisos_badge">¡IMPORTANTE!</div>  <!-- Etiqueta destacada -->
                            <?php endif; ?>

                            <div class="info_avisos_contenido">  <!-- Contenido del aviso -->
                                <!-- FECHA + USUARIO EDITOR -->
                                <p class="info_avisos_fecha">
                                    <?php echo date('d/m/Y', strtotime($fila['fecha'])); ?>  <!-- Convierte fecha SQL a formato español -->
                                    <?php if (!empty($fila['ultima_edicion_usuario_nombre'])): ?>  <!-- Muestra quién editó -->
                                        <br><small class="letra-666"><?php echo htmlspecialchars($fila['ultima_edicion_usuario_nombre']); ?></small>  <!-- Nombre escapado -->
                                    <?php endif; ?>
                                </p>

                                <!-- TÍTULO DEL AVISO -->
                                <h3 class="info_avisos_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>  <!-- Título seguro XSS -->

                                <!-- CONTENIDO DEL AVISO -->
                                <p class="info_avisos_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>  <!-- Texto con saltos línea preservados -->

                                <!-- ENLACE DOCUMENTO (opcional) -->
                                <?php if (!empty($fila['enlace'])): ?>  
                                    <a href="<?php echo htmlspecialchars($fila['enlace']); ?>" class="info_avisos_enlace" target="_blank">
                                        Ver documento →  <!-- Abre PDF/nueva pestaña -->
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>  <!-- Fin bucle avisos -->
                </div>
            <?php else: ?>  <!-- En caso de que NO haya avisos -->
                <div class="info_avisos_sin_contenido"> 
                    <i class="fas fa-info-circle"></i>  <!-- Icono información -->
                    <h3>No hay avisos disponibles</h3>
                    <p>Revisa más tarde para nuevas comunicaciones oficiales.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conexion->close();  // Libera conexión base de datos
include 'footer.php';  // Carga footer 
?>
