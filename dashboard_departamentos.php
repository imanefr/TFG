<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

$sql = "SELECT * FROM departamentos";
$resultado = $conexion->query($sql);
$departamentos = [];
while ($fila = $resultado->fetch_assoc()) {
    $departamentos[] = $fila;
}
$resultado->close();

$submenus_nuestro_centro = [
    ['enlace' => 'info_departamento.php?id=1', 'titulo' => 'Actividades Extraescolares', 'icono' => 'fas fa-star', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las actividades extraescolares.'],
    ['enlace' => 'info_departamento.php?id=2', 'titulo' => 'Biblioteca', 'icono' => 'fas fa-book', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de la biblioteca.'],
    ['enlace' => 'info_departamento.php?id=3', 'titulo' => 'Biología y Geología', 'icono' => 'fas fa-leaf', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de biología y geología.'],
    ['enlace' => 'info_departamento.php?id=4', 'titulo' => 'Dibujo', 'icono' => 'fas fa-pencil-alt', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de dibujo.'],
    
    ['enlace' => 'info_departamento.php?id=5', 'titulo' => 'Economía', 'icono' => 'fas fa-chart-line', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de economía.'],
    ['enlace' => 'info_departamento.php?id=6', 'titulo' => 'Educación Física', 'icono' => 'fas fa-dumbbell', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de educación física.'],
    ['enlace' => 'info_departamento.php?id=7', 'titulo' => 'Filosofía', 'icono' => 'fas fa-brain', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de filosofía.'],
    ['enlace' => 'info_departamento.php?id=8', 'titulo' => 'Física y Química', 'icono' => 'fas fa-flask', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de física y química.'],
    
    ['enlace' => 'info_departamento.php?id=9', 'titulo' => 'Francés', 'icono' => 'fas fa-flag', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de francés.'],
    ['enlace' => 'info_departamento.php?id=10', 'titulo' => 'FOL', 'icono' => 'fas fa-briefcase', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de FOL.'],
    ['enlace' => 'info_departamento.php?id=11', 'titulo' => 'Geografía e Historia', 'icono' => 'fas fa-globe', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de geografía e historia.'],
    ['enlace' => 'info_departamento.php?id=12', 'titulo' => 'Imagen Personal', 'icono' => 'fas fa-cut', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de imagen personal.'],
    
    ['enlace' => 'info_departamento.php?id=13', 'titulo' => 'Imagen y Sonido', 'icono' => 'fas fa-video', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de imagen y sonido.'],
    ['enlace' => 'info_departamento.php?id=14', 'titulo' => 'Informática', 'icono' => 'fas fa-laptop', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de informática.'],
    ['enlace' => 'info_departamento.php?id=15', 'titulo' => 'Inglés', 'icono' => 'fas fa-language', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de inglés.'],
    ['enlace' => 'info_departamento.php?id=16', 'titulo' => 'Lengua Castellana y Literatura', 'icono' => 'fas fa-font', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de lengua castellana y literatura.'],
    
    ['enlace' => 'info_departamento.php?id=17', 'titulo' => 'Matemáticas', 'icono' => 'fas fa-calculator', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de matemáticas.'],
    ['enlace' => 'info_departamento.php?id=18', 'titulo' => 'Música', 'icono' => 'fas fa-music', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de música.'],
    ['enlace' => 'info_departamento.php?id=19', 'titulo' => 'Orientación', 'icono' => 'fas fa-compass', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de orientación.'],
    ['enlace' => 'info_departamento.php?id=20', 'titulo' => 'Religión', 'icono' => 'fas fa-pray', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de religión.'],
    
    ['enlace' => 'info_departamento.php?id=21', 'titulo' => 'Tecnología', 'icono' => 'fas fa-cogs', 'descripcion' => 'Lista, Crea, Actualiza o Elimina las noticias de tecnología.']
];

$colores = ['relevante_ahora' => '#10B981', 'ultimas_noticias' => '#F59E0B'];

// Título dinámico para el header global
$titulo_dashboard = "Dashboard Inicio";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_nuestro_centro_container">
        
        <!-- HEADER GLOBAL -->
        <?php include 'dashboard_head.php'; ?>


        <?php if (!$is_admin): ?>
            <div class="dashboard_nuestro_centro_no_admin">
                <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
                <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
            </div>
        <?php else: ?>
            <!-- CUADROS DEL MENÚ - 3 ELEMENTOS -->
            <div class="dashboard_nuestro_centro_dashboard_grid">
                <?php foreach ($submenus_nuestro_centro as $item): 
                    $key = strtolower(str_replace([' ', 'á'], ['', 'a'], $item['titulo']));
                    $dashboard_page = 'dashboard_' . $key . '.php';
                ?>
                    <div class="dashboard_nuestro_centro_cuadro_menu" style="border-top-color: <?php echo $colores[$key] ?? '#10B981'; ?>;">
                        <div class="dashboard_nuestro_centro_cuadro_icono" style="background: linear-gradient(135deg, <?php echo $colores[$key] ?? '#10B981'; ?>, #059669);">
                            <i class="fas <?php echo $item['icono']; ?>"></i>
                        </div>
                        <h3 class="dashboard_nuestro_centro_cuadro_titulo"><?php echo $item['titulo']; ?></h3>
                        <p class="dashboard_nuestro_centro_cuadro_desc"><?php echo $item['descripcion']; ?></p>
                        
                        <div class="dashboard_nuestro_centro_botones_directos">
                            <a href="<?php echo $item['enlace']; ?>" class="dashboard_nuestro_centro_submenu_item" target="_blank">
                                <i class="fas fa-eye"></i> Ver Página
                            </a>
                            <a href="<?php echo $dashboard_page; ?>" class="dashboard_nuestro_centro_submenu_item">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- BOTÓN LOGOUT -->
        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"> </i>  Volver
            </button>
        </form>
    </div>
</body>
</html>