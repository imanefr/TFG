<?php
session_start();  // Inicia la sesión del usuario
include 'conexion.php';  // Conecta a la base de datos MySQL

if (!isset($_SESSION['usuario_id'])) {  // Verifica si el usuario está logueado
    header('Location: login.php');  // Redirige al login si no hay sesión
    exit;  // Termina la ejecución
}

$usuario_id = $_SESSION['usuario_id'];  // Obtiene el ID del usuario logueado
// Consulta los accesos permitidos para este usuario
$query_accesos = "
    SELECT a.url_dashboard 
    FROM accesos a
    INNER JOIN usuarios_accesos ua ON a.id = ua.acceso_id
    WHERE ua.usuario_id = ?";
$stmt = $conexion->prepare($query_accesos);  // Prepara consulta preparada
$stmt->bind_param("i", $usuario_id);  // Vincula el ID del usuario
$stmt->execute();  // Ejecuta la consulta
$res = $stmt->get_result();  // Obtiene resultados

$accesos_permitidos = [];  // Array vacío para almacenar URLs permitidas
while ($row = $res->fetch_assoc()) {  // Recorre todos los accesos
    $accesos_permitidos[] = $row['url_dashboard'];  // Agrega URL al array
}

// Define el menú principal con todas las secciones y subsecciones
$menu_tree = [
    'inicio' => [
        'titulo' => 'Inicio',
        'icono' => 'fa-home',
        'subsecciones' => [
            ['enlace' => 'dashboard_relevanteahora.php', 'titulo' => 'Relevante Ahora', 'icono' => 'fas fa-bookmark'],
            ['enlace' => 'dashboard_ultimasnoticias.php', 'titulo' => 'Noticias', 'icono' => 'fas fa-bolt']
        ]
    ],
    'nuestro_centro' => [
        'titulo' => 'Nuestro Centro',
        'icono' => 'fa-university',
        'subsecciones' => [
            ['enlace' => 'dashboard_organigrama.php', 'titulo' => 'Organigrama', 'icono' => 'fa-sitemap'],
            ['enlace' => 'dashboard_ampa.php', 'titulo' => 'AMPA', 'icono' => 'fa-users'],
            ['enlace' => 'dashboard_bolsa_empleo.php', 'titulo' => 'Bolsa de empleo', 'icono' => 'fa-users'],
            ['enlace' => 'dashboard_resultados_academicos.php', 'titulo' => 'Resultados Académicos', 'icono' => 'fa-chart-bar']
        ]
    ],
    'secretaria' => [
        'titulo' => 'Secretaría',
        'icono' => 'fa-folder-open',
        'subsecciones' => [
            ['enlace' => 'dashboard_avisos.php', 'titulo' => 'Avisos', 'icono' => 'fa-bullhorn'],
            ['enlace' => 'dashboard_matricula.php', 'titulo' => 'Matriculación', 'icono' => 'fa-file-signature'],
            ['enlace' => 'dashboard_convalidacion.php', 'titulo' => 'Convalidación', 'icono' => 'fa-scale-balanced'],
            ['enlace' => 'dashboard_solicitud_titulos.php', 'titulo' => 'Solicitud de títulos', 'icono' => 'fa-certificate'],
            ['enlace' => 'dashboard_otros_tramites.php', 'titulo' => 'Otros trámites', 'icono' => 'fa-folder-tree'],
            ['enlace' => 'dashboard_contacto.php', 'titulo' => 'Contacto', 'icono' => 'fa-envelope-open-text']
        ]
    ],
    'oferta_educativa' => [
        'titulo' => 'Oferta Educativa',
        'icono' => 'fa-graduation-cap',
        'subsecciones' => [
            ['enlace' => 'dashboard_eso.php', 'titulo' => 'ESO', 'icono' => 'fa-book'],
            ['enlace' => 'dashboard_bachillerato.php', 'titulo' => 'Bachillerato', 'icono' => 'fa-book'],
            ['enlace' => 'dashboard_formacionprofesional.php', 'titulo' => 'FP', 'icono' => 'fa-wrench']
        ]
    ],
    'departamentos' => [
        'titulo' => 'Departamentos',
        'icono' => 'fa-users-cog',
        'subsecciones' => [
            ['enlace' => 'dashboard_actividadesextraescolares.php', 'titulo' => 'Act. Extraescolares', 'icono' => 'fas fa-star'],
            ['enlace' => 'dashboard_biblioteca.php', 'titulo' => 'Biblioteca', 'icono' => 'fas fa-book'],
            ['enlace' => 'dashboard_biologiaygeologia.php', 'titulo' => 'Biología y Geo.', 'icono' => 'fas fa-leaf'],
            ['enlace' => 'dashboard_dibujo.php', 'titulo' => 'Dibujo', 'icono' => 'fas fa-pencil-alt'],
            ['enlace' => 'dashboard_economia.php', 'titulo' => 'Economía', 'icono' => 'fas fa-chart-line'],
            ['enlace' => 'dashboard_educaciónfísica.php', 'titulo' => 'Ed. Física', 'icono' => 'fas fa-dumbbell'],
            ['enlace' => 'dashboard_filosofía.php', 'titulo' => 'Filosofía', 'icono' => 'fas fa-brain'],
            ['enlace' => 'dashboard_físicayquímica.php', 'titulo' => 'Física y Química', 'icono' => 'fas fa-flask'],
            ['enlace' => 'dashboard_francés.php', 'titulo' => 'Francés', 'icono' => 'fas fa-flag'],
            ['enlace' => 'dashboard_fol.php', 'titulo' => 'FOL', 'icono' => 'fas fa-briefcase'],
            ['enlace' => 'dashboard_geografíaehistoria.php', 'titulo' => 'Geo. e Historia', 'icono' => 'fas fa-globe'],
            ['enlace' => 'dashboard_imagenpersonal.php', 'titulo' => 'Imagen Personal', 'icono' => 'fas fa-cut'],
            ['enlace' => 'dashboard_imagenysonido.php', 'titulo' => 'Imagen y Sonido', 'icono' => 'fas fa-video'],
            ['enlace' => 'dashboard_informatica.php', 'titulo' => 'Informática', 'icono' => 'fas fa-laptop'],
            ['enlace' => 'dashboard_inglés.php', 'titulo' => 'Inglés', 'icono' => 'fas fa-language'],
            ['enlace' => 'dashboard_lenguacastellanayliteratura.php', 'titulo' => 'Lengua y Lit.', 'icono' => 'fas fa-font'],
            ['enlace' => 'dashboard_matematicas.php', 'titulo' => 'Matemáticas', 'icono' => 'fas fa-calculator'],
            ['enlace' => 'dashboard_música.php', 'titulo' => 'Música', 'icono' => 'fas fa-music'],
            ['enlace' => 'dashboard_orientacion.php', 'titulo' => 'Orientación', 'icono' => 'fas fa-compass'],
            ['enlace' => 'dashboard_religión.php', 'titulo' => 'Religión', 'icono' => 'fas fa-pray'],
            ['enlace' => 'dashboard_tecnología.php', 'titulo' => 'Tecnología', 'icono' => 'fas fa-cogs']
        ]
    ],
    'otros' => [
        'titulo' => 'Gestión y Otros',
        'icono' => 'fa-gears',
        'subsecciones' => [
            ['enlace' => 'dashboard_usuarios.php', 'titulo' => 'Gestión de Usuarios', 'icono' => 'fa-user-shield'],
            ['enlace' => 'dashboard_blog.php', 'titulo' => 'Blog', 'icono' => 'fa-rss'],
            ['enlace' => 'dashboard_erasmus.php', 'titulo' => 'Erasmus', 'icono' => 'fa-rss'],
            ['enlace' => 'dashboard_doc_institucionales.php', 'titulo' => 'Documentos institucionales', 'icono' => 'fa-rss']
        ]
    ]
];
?>

<!DOCTYPE html>  <!-- Declara documento HTML5 -->
<html lang="es">  <!-- Página en español -->

    <head>  <!-- Sección de metadatos -->
        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Responsive design -->
        <title>Dashboard - Secretaría</title>  
        <link rel="stylesheet" href="style_imane.css">  <!-- Hoja de estilos personalizada -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">  <!-- Iconos FontAwesome -->
    </head>

    <body>  <!-- Cuerpo de la página -->

        <?php include 'dashboard_head.php'; ?>  <!-- Incluye header del dashboard -->

        <div class="dashboard-wrapper">  <!-- Contenedor principal del dashboard -->
            <div class="grid-secciones">  <!-- Grid para las tarjetas de secciones -->
                <?php
                $colores = ['card-red', 'card-green', 'card-orange', 'card-blue', 'card-purple', 'card-pink'];  // Array de clases de colores
                shuffle($colores);  // Mezcla colores aleatoriamente
                $color_idx = 0;  // Índice para rotar colores
// Recorre cada sección del menú
                foreach ($menu_tree as $key => $seccion):
                    // Filtra solo subsecciones permitidas para este usuario
                    $sub_permitidas = array_filter($seccion['subsecciones'], function ($sub) use ($accesos_permitidos) {
                        return in_array($sub['enlace'], $accesos_permitidos);
                    });

                    if (empty($sub_permitidas))  // Salta secciones sin permisos
                        continue;

                    $color_clase = $colores[$color_idx % count($colores)];  // Asigna color rotativo
                    $color_idx++;  // Siguiente color
                    ?>

                    <div class="card-seccion <?php echo $color_clase; ?>">  <!-- Tarjeta de sección con color -->
                        <div class="card-icon">  <!-- Icono principal de la sección -->
                            <i class="fa <?php echo $seccion['icono']; ?>"></i>
                        </div>
                        <h2 class="card-title"><?php echo $seccion['titulo']; ?></h2>  <!-- Título de la sección -->
                        <p class="card-desc">Contenidos de <?php echo strtolower($seccion['titulo']); ?></p>  <!-- Descripción -->

                        <div class="subsecciones-list">  <!-- Lista de subsecciones permitidas -->
                            <?php foreach ($sub_permitidas as $sub): ?>  <!-- Recorre subsecciones permitidas -->
                                <div class="item-sub">  <!-- Ítem individual de subsección -->
                                    <span class="sub-name"><?php echo $sub['titulo']; ?></span>  <!-- Nombre de la subsección -->
                                    <div class="btn-group">  <!-- Grupo de botones de acción -->
                                        <!-- Botón "Ver" - abre página pública -->
                                        <a href="./<?php echo str_replace('dashboard_', '', $sub['enlace']); ?>"
                                           class="btn-dash btn-view">
                                            <i class="fa-regular fa-eye"></i> Ver
                                        </a>
                                        <!-- Botón "Editar" - abre panel de administración -->
                                        <a href="<?php echo $sub['enlace']; ?>" class="btn-dash btn-edit">
                                            <i class="fa-regular fa-pen-to-square"></i> Editar
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>

    </body>  <!-- Fin del cuerpo -->

</html>  <!-- Fin del documento HTML -->