<?php
include("conexion.php");

// Consulta ciclos formativos desde BD
$sql = "SELECT * FROM ciclos_fp ORDER BY categoria, nivel, nombre";
$resultado = $conexion->query($sql);
$ciclos = [];
while ($fila = $resultado->fetch_assoc()) {
    $ciclos[] = $fila;
}
?>

       <?php include 'head.php'; ?>


    <!-- HEADER FP -->
    <section class="fp-contenido">
        <div class="contenedor-max">
            <div class="avisos-layout">
                <div class="avisos-logo">
                    <i class="fas fa-graduation-cap" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="avisos-texto">
                    <h2>Formación Profesional</h2>
                    <p>Grado Básico, Medio, Superior y Cursos de Especialización</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CICLOS FP -->
    <main>
        <?php if (!empty($ciclos)): ?>
            <!-- FP BÁSICA -->
            <?php $fp_basica = array_filter($ciclos, fn($c) => $c['nivel'] == 'FPB'); ?>
            <?php if (!empty($fp_basica)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Formación Profesional Básica</h2>
                        <div class="grid-fp">
                            <?php foreach ($fp_basica as $ciclo): ?>
                                <div class="card-fp">
                                    <div class="fp-icono <?php echo strtolower($ciclo['categoria']); ?>">
                                        <i class="<?php echo $ciclo['icono']; ?>"></i>
                                    </div>
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- GRADO MEDIO -->
            <?php $grado_medio = array_filter($ciclos, fn($c) => $c['nivel'] == 'GM'); ?>
            <?php if (!empty($grado_medio)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Grado Medio</h2>
                        <div class="grid-fp">
                            <?php foreach ($grado_medio as $ciclo): ?>
                                <div class="card-fp">
                                    <div class="fp-icono <?php echo strtolower($ciclo['categoria']); ?>">
                                        <i class="<?php echo $ciclo['icono']; ?>"></i>
                                    </div>
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- GRADO SUPERIOR -->
            <?php $grado_superior = array_filter($ciclos, fn($c) => $c['nivel'] == 'GS'); ?>
            <?php if (!empty($grado_superior)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Grado Superior</h2>
                        <div class="grid-fp">
                            <?php foreach ($grado_superior as $ciclo): ?>
                                <div class="card-fp">
                                    <div class="fp-icono <?php echo strtolower($ciclo['categoria']); ?>">
                                        <i class="<?php echo $ciclo['icono']; ?>"></i>
                                    </div>
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ESPECIALIZACIÓN -->
            <?php $especializacion = array_filter($ciclos, fn($c) => $c['nivel'] == 'CE'); ?>
            <?php if (!empty($especializacion)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Cursos Especialización</h2>
                        <div class="grid-fp">
                            <?php foreach ($especializacion as $ciclo): ?>
                                <div class="card-fp especial">
                                    <div class="fp-icono <?php echo strtolower($ciclo['categoria']); ?>">
                                        <i class="<?php echo $ciclo['icono']; ?>"></i>
                                    </div>
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php else: ?>
            <section class="seccion-contenido">
                <div class="contenedor-max">
                    <div class="sin-contenido">
                        <i class="fas fa-briefcase"></i>
                        <h3>No hay ciclos disponibles</h3>
                        <p>Consulta con secretaría nuestra oferta formativa.</p>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

          <?php include 'footer.php'; ?>


    <script src="script.js"></script>
</body>
</html>
