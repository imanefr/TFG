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

// Define el menú principal con todas las secciones, subsecciones y sus enlaces de visualización pública
$menu_tree = [
    'inicio' => [
        'titulo' => 'Inicio',
        'icono' => 'fa-home',
        'subsecciones' => [
            ['enlace' => 'dashboard_relevanteahora.php', 'enlace_ver' => 'relevante_ahora.php', 'titulo' => 'Relevante Ahora', 'icono' => 'fas fa-bookmark'],
            ['enlace' => 'dashboard_ultimasnoticias.php', 'enlace_ver' => 'ultimas_noticias.php', 'titulo' => 'Noticias', 'icono' => 'fas fa-bolt']
        ]
    ],
    'nuestro_centro' => [
        'titulo' => 'Nuestro Centro',
        'icono' => 'fa-university',
        'subsecciones' => [
            ['enlace' => 'dashboard_organigrama.php', 'enlace_ver' => 'organigrama.php', 'titulo' => 'Organigrama', 'icono' => 'fa-sitemap'],
            ['enlace' => 'dashboard_ampa.php', 'enlace_ver' => 'ampa.php', 'titulo' => 'AMPA', 'icono' => 'fa-users'],
            ['enlace' => 'dashboard_bolsa_empleo.php', 'enlace_ver' => 'bolsa_empleo.php', 'titulo' => 'Bolsa de empleo', 'icono' => 'fa-users'],
            ['enlace' => 'dashboard_resultados_academicos.php', 'enlace_ver' => 'resultados_academicos.php', 'titulo' => 'Resultados Académicos', 'icono' => 'fa-chart-bar']
        ]
    ],
    'secretaria' => [
        'titulo' => 'Secretaría',
        'icono' => 'fa-folder-open',
        'subsecciones' => [
            ['enlace' => 'dashboard_avisos.php', 'enlace_ver' => 'avisos.php', 'titulo' => 'Avisos', 'icono' => 'fa-bullhorn'],
            ['enlace' => 'dashboard_matricula.php', 'enlace_ver' => '', 'titulo' => 'Matriculación', 'icono' => 'fa-file-signature'],
            ['enlace' => 'dashboard_convalidacion.php', 'enlace_ver' => '', 'titulo' => 'Convalidación', 'icono' => 'fa-scale-balanced'],
            ['enlace' => 'dashboard_solicitud_titulos.php', 'enlace_ver' => 'solicitud_titulos.php', 'titulo' => 'Solicitud de títulos', 'icono' => 'fa-certificate'],
            ['enlace' => 'dashboard_otros_tramites.php', 'enlace_ver' => 'otros_tramites.php', 'titulo' => 'Otros trámites', 'icono' => 'fa-folder-tree'],
            ['enlace' => 'dashboard_contacto.php', 'enlace_ver' => 'contacto_secretaria.php', 'titulo' => 'Contacto', 'icono' => 'fa-envelope-open-text']
        ]
    ],
    'oferta_educativa' => [
        'titulo' => 'Oferta Educativa',
        'icono' => 'fa-graduation-cap',
        'subsecciones' => [
            ['enlace' => 'dashboard_eso.php', 'enlace_ver' => 'info_eso.php', 'titulo' => 'ESO', 'icono' => 'fa-book'],
            ['enlace' => 'dashboard_bachillerato.php', 'enlace_ver' => 'info_bachillerato.php', 'titulo' => 'Bachillerato', 'icono' => 'fa-book'],
            ['enlace' => 'dashboard_formacionprofesional.php', 'enlace_ver' => 'info_fp.php', 'titulo' => 'FP', 'icono' => 'fa-wrench']
        ]
    ],
    'departamentos' => [
        'titulo' => 'Departamentos',
        'icono' => 'fa-users-cog',
        'subsecciones' => [
            ['enlace' => 'dashboard_actividadesextraescolares.php', 'enlace_ver' => 'info_departamento.php?id=1', 'titulo' => 'Act. Extraescolares', 'icono' => 'fas fa-star'],
            ['enlace' => 'dashboard_biblioteca.php', 'enlace_ver' => 'info_departamento.php?id=2', 'titulo' => 'Biblioteca', 'icono' => 'fas fa-book'],
            ['enlace' => 'dashboard_biologiaygeologia.php', 'enlace_ver' => 'info_departamento.php?id=3', 'titulo' => 'Biología y Geo.', 'icono' => 'fas fa-leaf'],
            ['enlace' => 'dashboard_dibujo.php', 'enlace_ver' => 'info_departamento.php?id=4', 'titulo' => 'Dibujo', 'icono' => 'fas fa-pencil-alt'],
            ['enlace' => 'dashboard_economia.php', 'enlace_ver' => 'info_departamento.php?id=5', 'titulo' => 'Economía', 'icono' => 'fas fa-chart-line'],
            ['enlace' => 'dashboard_educaciónfísica.php', 'enlace_ver' => 'info_departamento.php?id=6', 'titulo' => 'Ed. Física', 'icono' => 'fas fa-dumbbell'],
            ['enlace' => 'dashboard_filosofía.php', 'enlace_ver' => 'info_departamento.php?id=7', 'titulo' => 'Filosofía', 'icono' => 'fas fa-brain'],
            ['enlace' => 'dashboard_físicayquímica.php', 'enlace_ver' => 'info_departamento.php?id=8', 'titulo' => 'Física y Química', 'icono' => 'fas fa-flask'],
            ['enlace' => 'dashboard_francés.php', 'enlace_ver' => 'info_departamento.php?id=9', 'titulo' => 'Francés', 'icono' => 'fas fa-flag'],
            ['enlace' => 'dashboard_fol.php', 'enlace_ver' => 'info_departamento.php?id=10', 'titulo' => 'FOL', 'icono' => 'fas fa-briefcase'],
            ['enlace' => 'dashboard_geografíaehistoria.php', 'enlace_ver' => 'info_departamento.php?id=11', 'titulo' => 'Geo. e Historia', 'icono' => 'fas fa-globe'],
            ['enlace' => 'dashboard_imagenpersonal.php', 'enlace_ver' => 'info_departamento.php?id=12', 'titulo' => 'Imagen Personal', 'icono' => 'fas fa-cut'],
            ['enlace' => 'dashboard_imagenysonido.php', 'enlace_ver' => 'info_departamento.php?id=13', 'titulo' => 'Imagen y Sonido', 'icono' => 'fas fa-video'],
            ['enlace' => 'dashboard_informatica.php', 'enlace_ver' => 'info_departamento.php?id=14', 'titulo' => 'Informática', 'icono' => 'fas fa-laptop'],
            ['enlace' => 'dashboard_inglés.php', 'enlace_ver' => 'info_departamento.php?id=15', 'titulo' => 'Inglés', 'icono' => 'fas fa-language'],
            ['enlace' => 'dashboard_lenguacastellanayliteratura.php', 'enlace_ver' => 'info_departamento.php?id=16', 'titulo' => 'Lengua y Lit.', 'icono' => 'fas fa-font'],
            ['enlace' => 'dashboard_matematicas.php', 'enlace_ver' => 'info_departamento.php?id=17', 'titulo' => 'Matemáticas', 'icono' => 'fas fa-calculator'],
            ['enlace' => 'dashboard_música.php', 'enlace_ver' => 'info_departamento.php?id=18', 'titulo' => 'Música', 'icono' => 'fas fa-music'],
            ['enlace' => 'dashboard_orientacion.php', 'enlace_ver' => 'info_departamento.php?id=19', 'titulo' => 'Orientación', 'icono' => 'fas fa-compass'],
            ['enlace' => 'dashboard_religión.php', 'enlace_ver' => 'info_departamento.php?id=20', 'titulo' => 'Religión', 'icono' => 'fas fa-pray'],
            ['enlace' => 'dashboard_tecnología.php', 'enlace_ver' => 'info_departamento.php?id=21', 'titulo' => 'Tecnología', 'icono' => 'fas fa-cogs']
        ]
    ],
    'otros' => [
        'titulo' => 'Gestión y Otros',
        'icono' => 'fa-gears',
        'subsecciones' => [
            ['enlace' => 'dashboard_usuarios.php', 'enlace_ver' => '', 'titulo' => 'Gestión de Usuarios', 'icono' => 'fa-user-shield'],
            ['enlace' => 'dashboard_blog.php', 'enlace_ver' => 'blog.php', 'titulo' => 'Blog', 'icono' => 'fa-rss'],
            ['enlace' => 'dashboard_erasmus.php', 'enlace_ver' => 'erasmus.php', 'titulo' => 'Erasmus', 'icono' => 'fa-rss'],
            ['enlace' => 'dashboard_doc_institucionales.php', 'enlace_ver' => 'doc_institucionales.php', 'titulo' => 'Documentos institucionales', 'icono' => 'fa-rss']
        ]
    ]
];
?>

<!DOCTYPE html>  <html lang="es">  <head>  <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1.0">  <title>Dashboard - Secretaría</title>  
        <link rel="stylesheet" href="style_imane.css">  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">  </head>

    <body>  <?php include 'dashboard_head.php'; ?>  <div class="dashboard-wrapper">  <div class="grid-secciones">  <?php
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

                    <div class="card-seccion <?php echo $color_clase; ?>">  <div class="card-icon">  <i class="fa <?php echo $seccion['icono']; ?>"></i>
                        </div>
                        <h2 class="card-title"><?php echo $seccion['titulo']; ?></h2>  <p class="card-desc">Contenidos de <?php echo strtolower($seccion['titulo']); ?></p> 
                        <div class="subsecciones-list">  
                            <?php foreach ($sub_permitidas as $sub): ?>  
                            <div class="item-sub">  
                                <span class="sub-name">
                                    <?php echo $sub['titulo']; ?></span>  
                                <div class="btn-group">  
                                    <?php if (!empty($sub['enlace_ver'])): ?>
                                            <a href="./<?php echo $sub['enlace_ver']; ?>" class="btn-dash btn-view">
                                                <i class="fa-regular fa-eye"></i> Ver
                                            </a>
                                        <?php endif; ?>
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

    </body>  </html>  