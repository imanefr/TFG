<?php
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

$submenus_oferta = [
    ['enlace' => 'eso_info.php', 'titulo' => 'ESO', 'icono' => 'fa-school', 'descripcion' => 'Educación Secundaria Obligatoria'],
    ['enlace' => 'bachillerato.php', 'titulo' => 'Bachillerato', 'icono' => 'fa-graduation-cap', 'descripcion' => 'Bachillerato (en desarrollo)'],
    ['enlace' => 'fp_info.php', 'titulo' => 'FP General', 'icono' => 'fa-tools', 'descripcion' => 'Formación Profesional general'],
    ['enlace' => 'desarrollo_videojuegos.php', 'titulo' => 'Desarrollo Videojuegos', 'icono' => 'fa-gamepad', 'descripcion' => 'Curso superior de videojuegos']
];

$colores = ['eso' => '#3B82F6', 'bachillerato' => '#8B5CF6', 'fp_general' => '#10B981', 'desarrollo_videojuegos' => '#F59E0B'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oferta Educativa - Dashboard Admin - IES La Arboleda</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --morado: #8B5CF6; --morado-oscuro: #7C3AED; --morado-claro: #C4B5FD;
            --blanco: #FFFFFF; --gris: #6B7280; --gris-oscuro: #1F2937;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: linear-gradient(135deg, #F8FAFC, #EDE9FE); min-height: 100vh; padding: 2rem; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: var(--blanco); padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(139,92,246,0.1); margin-bottom: 2rem; text-align: center; border: 1px solid var(--morado-claro); }
        .saludo { background: linear-gradient(135deg, var(--morado), var(--morado-oscuro)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; }
        .info-usuario { background: var(--morado-claro); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 1.1rem; font-weight: 600; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 2rem; }
        .cuadro-menu { background: var(--blanco); border-radius: 20px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(139,92,246,0.08); transition: all 0.3s ease; border-top: 5px solid var(--morado); cursor: pointer; position: relative; overflow: hidden; }
        .cuadro-menu:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(139,92,246,0.15); }
        .cuadro-icono { width: 80px; height: 80px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; margin: 0 auto 1.5rem; box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        .cuadro-titulo { font-size: 1.5rem; font-weight: 800; color: var(--gris-oscuro); text-align: center; margin-bottom: 0.8rem; }
        .cuadro-desc { color: var(--gris); text-align: center; font-size: 1rem; margin-bottom: 2rem; }
        .botones-directos { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .submenu-item { background: linear-gradient(135deg, #f8fafc, #e2e8f0); padding: 1rem; border-radius: 12px; text-decoration: none; color: #374151; font-weight: 600; text-align: center; transition: all 0.3s ease; border: 2px solid transparent; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .submenu-item:hover { background: var(--morado); color: white; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(139,92,246,0.3); border-color: var(--morado); }
        .btn-logout { background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; border: none; padding: 1.2rem 2.5rem; border-radius: 15px; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: block; margin: 3rem auto 0; transition: all 0.3s ease; }
        .btn-logout:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.4); }
        .no-admin { text-align: center; padding: 4rem; background: var(--blanco); border-radius: 20px; color: var(--gris); margin: 2rem 0; border: 1px solid var(--morado-claro); }
        @media (max-width: 768px) { .dashboard-grid { grid-template-columns: 1fr; gap: 1.5rem; } .saludo { font-size: 2rem; } .botones-directos { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="saludo">
                <i class="fas fa-graduation-cap"></i>
                Gestión - Oferta Educativa
                <span class="info-usuario"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> (<?php echo ucfirst($_SESSION['usuario_rol']); ?>)</span>
            </h1>
        </div>

        <?php if (!$is_admin): ?>
            <div class="no-admin">
                <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>
            <div class="dashboard-grid">
                <?php foreach ($submenus_oferta as $index => $item): 
                    $key = strtolower(str_replace([' ', 'á'], ['', 'a'], $item['titulo']));
                ?>
                    <div class="cuadro-menu" style="border-top-color: <?php echo $colores[$key] ?? '#3B82F6'; ?>;">
                        <div class="cuadro-icono" style="background: linear-gradient(135deg, <?php echo $colores[$key] ?? '#3B82F6'; ?>, #1D4ED8);">
                            <i class="fas <?php echo $item['icono']; ?>"></i>
                        </div>
                        <h3 class="cuadro-titulo"><?php echo $item['titulo']; ?></h3>
                        <p class="cuadro-desc"><?php echo $item['descripcion']; ?></p>
                        <div class="botones-directos">
                            <a href="<?php echo $item['enlace']; ?>" class="submenu-item" target="_blank">
                                <i class="fas fa-eye"></i> Ver Página
                            </a>
                            <a href="#" class="submenu-item" onclick="alert('Editar <?php echo $item['titulo']; ?> en desarrollo')">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dashboard.php" style="text-align: center;">
            <button type="submit" class="btn-logout">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </button>
        </form>
    </div>
</body>
</html>
