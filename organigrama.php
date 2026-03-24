<?php
// INCLUYE CONEXIÓN BD - Carga MySQLi con $conexion
include("conexion.php");

// CONSULTA SQL AVANZADA - Ordena secciones específicas primero con FIELD() + ID
$query = "SELECT * FROM organigrama ORDER BY 
    FIELD(seccion, 'Equipo Directivo', 'Consejo Escolar', 'Claustro'),  /* Orden fijo: Directivo > Consejo > Claustro > resto */
    id ASC";  /* Luego alfabético por ID dentro cada sección */
$result = mysqli_query($conexion, $query);  /* Ejecuta consulta con mysqli */

// ARRAY AGRUPADO - Organiza datos por sección para HTML
$datos = [];  /* Array asociativo: 'seccion' => [miembros...] */
if ($result) {  /* Verifica consulta exitosa */
    // RECORRE Y AGRUPA - while + índice seccion
    while ($fila = mysqli_fetch_assoc($result)) {
        $datos[$fila["seccion"]][] = $fila;  /* Añade fila a su sección */
    }
    // LIBERA MEMORIA - Resultado consulta
    mysqli_free_result($result);
}
// CIERRA CONEXIÓN - Libera recursos BD
mysqli_close($conexion);
?>

<!-- INCLUYE HEAD COMPLETO - Navbar, CSS global, ArboledaBot -->
<?php include 'head.php'; ?>

<!-- HERO HEADER ORGANIGRAMA - Icono usuarios + títulos -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">  <!-- Wrapper ancho máximo -->
        <div class="hero-layout-universal">  <!-- Layout icono+texto -->
            <!-- ÍCONO USUARIOS - Visual organigrama -->
            <div class="hero-icono-universal">
                <i class="fas fa-users icono_universal"></i>
            </div>
            <!-- TÍTULOS HERO -->
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Organigrama</h1>  <!-- H1 SEO -->
                <p class="hero-subtitulo-universal">Estructura organizativa del centro educativo.</p>
            </div>
        </div>
    </div>
</section>

<!-- MAIN ORGANIGRAMA - Tablas por sección -->
<main class="organigrama_pagina">
    <section class="seccion-contenido">  <!-- Padding/márgenes CSS -->
        <!-- TÍTULO PRINCIPAL -->
        <h2 class="organigrama_titulo">Organización Institucional</h2>

        <?php if (!empty($datos)): ?>  <!-- SI HAY DATOS BD -->
            <!-- LOOP SECCIONES - Directivo, Consejo, Claustro, Departamentos... -->
            <?php foreach ($datos as $seccion => $miembros): ?>
                <div class="organigrama_bloque">  <!-- Bloque individual sección -->
                    <!-- TÍTULO SECCIÓN - Ej: "Equipo Directivo" -->
                    <h3 class="organigrama_seccion"><?php echo htmlspecialchars($seccion); ?></h3>
                    
                    <!-- TABLA MIEMBROS - 2 columnas Cargo/Nombre -->
                    <table class="organigrama_tabla">
                        <thead>  <!-- CABECERA TABLA -->
                            <tr>
                                <th>Cargo</th>      <!-- Director, Secretario, Jefe Estudios... -->
                                <th>Nombre</th>    <!-- Nombres profesores/equipo -->
                            </tr>
                        </thead>
                        <tbody>  <!-- CUERPO DINÁMICO -->
                            <?php foreach ($miembros as $persona): ?>  <!-- LOOP MIEMBROS SECCIÓN -->
                                <tr>  <!-- FILA INDIVIDUAL -->
                                    <td><?php echo htmlspecialchars($persona['cargo']); ?></td>  <!-- Cargo escapado XSS -->
                                    <td><?php echo htmlspecialchars($persona['nombre']); ?></td> <!-- Nombre escapado -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>  <!-- SIN DATOS BD -->
            <!-- MENSAJE VACÍO - Fallback elegante -->
            <div class="organigrama_vacio">
                <p>No hay datos disponibles en este momento.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- FOOTER GLOBAL - Copyright, contacto, redes -->
<?php include 'footer.php'; ?>
