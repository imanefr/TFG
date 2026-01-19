<?php
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // ✅ Corregido: header() en lugar de redir()
    exit;
}

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// Estadísticas
$stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE activo = 1");
$stmt->execute();
$total_usuarios = $stmt->get_result()->fetch_row()[0];
$stmt->close();

$stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin' AND activo = 1");
$stmt->execute();
$total_admins = $stmt->get_result()->fetch_row()[0];
$stmt->close();

$menu_principal = [
    'inicio' => ['icono' => 'fa-home', 'titulo' => 'Página Inicio', 'descripcion' => 'Página principal del IES La Arboleda', 'enlace' => 'index.php'],
    'nuestro_centro' => [
        'icono' => 'fa-building', 
        'titulo' => 'Nuestro Centro', 
        'descripcion' => 'Organigrama, AMPA, Resultados Académicos',
        'submenu' => [
            ['enlace' => 'organigrama.php', 'titulo' => 'Organigrama'],
            ['enlace' => 'ampa.php', 'titulo' => 'AMPA'],
            ['enlace' => 'resultados_academicos.php', 'titulo' => 'Resultados Académicos']
        ]
    ],
    'oferta_educativa' => [
        'icono' => 'fa-graduation-cap', 
        'titulo' => 'Oferta Educativa', 
        'descripcion' => 'ESO, Bachillerato, Formación Profesional',
        'submenu' => [
            ['enlace' => 'eso_info.php', 'titulo' => 'ESO'],
            ['enlace' => 'bachillerato.php', 'titulo' => 'Bachillerato'],
            ['enlace' => 'fp_info.php', 'titulo' => 'FP'],
            ['enlace' => 'desarrollo_videojuegos.php', 'titulo' => 'Desarrollo Videojuegos']
        ]
    ],
    'secretaria' => [
        'icono' => 'fa-file-alt', 
        'titulo' => 'Secretaría', 
        'descripcion' => 'Matriculaciones, convalidaciones, títulos',
        'submenu' => [
            ['enlace' => 'avisos.php', 'titulo' => 'Avisos'],
            ['enlace' => 'matriculacion_eso.php', 'titulo' => 'Matriculación ESO'],
            ['enlace' => 'convalidacion_fp.php', 'titulo' => 'Convalidación FP'],
            ['enlace' => 'solicitud_titulo_eso.php', 'titulo' => 'Título ESO'],
            ['enlace' => 'otros_tramites.php', 'titulo' => 'Otros trámites']
        ]
    ],
    'departamentos' => ['icono' => 'fa-users', 'titulo' => 'Departamentos', 'descripcion' => 'Listado de departamentos académicos', 'enlace' => 'departamentos.php'],
    'erasmus' => ['icono' => 'fa-plane', 'titulo' => 'Erasmus+', 'descripcion' => 'Programa de movilidad europea', 'enlace' => 'erasmus.php'],
    'familias' => ['icono' => 'fa-user-friends', 'titulo' => 'Información Familias', 'descripcion' => 'Comunicaciones para familias', 'enlace' => 'info_familias.php'], // ✅ Corregido
    'documentos' => ['icono' => 'fa-file-pdf', 'titulo' => 'Documentos Institucionales', 'descripcion' => 'Documentos oficiales del centro', 'enlace' => 'doc_institucionales.php'],
    'orientacion' => ['icono' => 'fa-chalkboard-teacher', 'titulo' => 'Orientación', 'descripcion' => 'Departamento de orientación', 'enlace' => 'orientacion.php']
];

// Colores para cada icono
$colores = [
    'inicio' => '#8B5CF6', 'nuestro_centro' => '#10B981', 'oferta_educativa' => '#3B82F6',
    'secretaria' => '#F59E0B', 'departamentos' => '#EC4899', 'erasmus' => '#06B6D4',
    'familias' => '#10B981', 'documentos' => '#F59E0B', 'orientacion' => '#8B5CF6'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - IES La Arboleda</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --morado: #8B5CF6; --morado-oscuro: #7C3AED; --morado-claro: #C4B5FD;
            --blanco: #FFFFFF; --gris: #6B7280; --gris-oscuro: #1F2937;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: system-ui, sans-serif; 
            background: linear-gradient(135deg, #F8FAFC, #EDE9FE);
            min-height: 100vh; padding: 2rem;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { 
            background: var(--blanco); padding: 2.5rem; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(139,92,246,0.1); margin-bottom: 2rem; 
            text-align: center; border: 1px solid var(--morado-claro);
        }
        .saludo { 
            background: linear-gradient(135deg, var(--morado), var(--morado-oscuro));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;
            display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap;
        }
        .info-usuario { 
            background: var(--morado-claro); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            font-size: 1.1rem; font-weight: 600;
        }
        
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 1.5rem; margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--blanco); padding: 2rem; border-radius: 15px; 
            box-shadow: 0 8px 25px rgba(139,92,246,0.08); text-align: center;
            border-top: 4px solid var(--morado);
        }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--morado); }
        .stat-label { color: var(--gris); font-weight: 600; margin-top: 0.5rem; }

        .dashboard-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 2rem; 
        }
        .cuadro-menu { 
            background: var(--blanco); border-radius: 20px; padding: 2.5rem; 
            box-shadow: 0 10px 30px rgba(139,92,246,0.08); transition: all 0.3s ease;
            border-top: 5px solid var(--morado); cursor: pointer; position: relative; overflow: hidden;
        }
        .cuadro-menu:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(139,92,246,0.15); }
        .cuadro-icono { 
            width: 80px; height: 80px; border-radius: 16px; display: flex;
            align-items: center; justify-content: center; font-size: 2rem; color: white;
            margin: 0 auto 1.5rem; box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .cuadro-titulo { font-size: 1.5rem; font-weight: 800; color: var(--gris-oscuro); text-align: center; margin-bottom: 0.8rem; }
        .cuadro-desc { color: var(--gris); text-align: center; font-size: 1rem; margin-bottom: 2rem; }
        
        .submenu-grid { display: none; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .cuadro-menu.activo .submenu-grid { display: grid; }
        .submenu-item { 
            background: linear-gradient(135deg, #f8fafc, #e2e8f0); padding: 1rem; border-radius: 12px;
            text-decoration: none; color: #374151; font-weight: 600; text-align: center;
            transition: all 0.3s ease; border: 2px solid transparent; display: flex;
            align-items: center; justify-content: center; gap: 0.5rem;
        }
        .submenu-item:hover { background: var(--morado); color: white; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(139,92,246,0.3); border-color: var(--morado); }
        
        .btn-logout { 
            background: linear-gradient(135deg, var(--morado-oscuro), var(--morado)); color: white; 
            border: none; padding: 1.2rem 2.5rem; border-radius: 15px; font-weight: 700; 
            font-size: 1.1rem; cursor: pointer; display: block; margin: 3rem auto 0;
            transition: all 0.3s ease;
        }
        .btn-logout:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.4); }
        .no-admin { text-align: center; padding: 4rem; background: var(--blanco); border-radius: 20px; color: var(--gris); margin: 2rem 0; border: 1px solid var(--morado-claro); }
        
        @media (max-width: 768px) { 
            .dashboard-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .saludo { font-size: 2rem; flex-direction: column; gap: 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1 class="saludo">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard Administrativo IES La Arboleda
                <span class="info-usuario">
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> 
                    (<?php echo ucfirst($_SESSION['usuario_rol']); ?>)
                </span>
            </h1>
        </div>

        <?php if (!$is_admin): ?>
            <div class="no-admin">
                <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
                <p>Tu rol actual: <strong><?php echo ucfirst($_SESSION['usuario_rol']); ?></strong></p>
            </div>
        <?php else: ?>
            <!-- ESTADÍSTICAS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_usuarios; ?></div>
                    <div class="stat-label">Total Usuarios</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_admins; ?></div>
                    <div class="stat-label">Administradores</div>
                </div>
            </div>

            <!-- CUADROS DEL MENÚ -->
            <div class="dashboard-grid">
                <?php foreach ($menu_principal as $key => $item): ?>
                    <div class="cuadro-menu" onclick="toggleSubmenu(this)" style="border-top-color: <?php echo $colores[$key] ?? '#8B5CF6'; ?>;">
                        <div class="cuadro-icono" style="background: linear-gradient(135deg, <?php echo $colores[$key] ?? '#8B5CF6'; ?>, <?php echo $colores[$key] ?? '#7C3AED'; ?>);">
                            <i class="fas <?php echo $item['icono']; ?>"></i>
                        </div>
                        <h3 class="cuadro-titulo"><?php echo $item['titulo']; ?></h3>
                        <p class="cuadro-desc"><?php echo $item['descripcion']; ?></p>
                        
                        <?php if (isset($item['submenu']) && count($item['submenu']) > 0): ?>
                            <div class="submenu-grid">
                                <?php foreach ($item['submenu'] as $subitem): ?>
                                    <a href="<?php echo $subitem['enlace']; ?>" class="submenu-item" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                        <?php echo $subitem['titulo']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="submenu-grid">
                                <a href="<?php echo $item['enlace']; ?>" class="submenu-item" target="_blank">
                                    <i class="fas fa-eye"></i> Ver Página
                                </a>
                                <a href="#" class="submenu-item" onclick="alert('Editar página en desarrollo')">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="logout.php" style="text-align: center;">
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </form>
    </div>

    <script>
        function toggleSubmenu(cuadro) {
            document.querySelectorAll('.cuadro-menu').forEach(c => {
                if (c !== cuadro) c.classList.remove('activo');
            });
            cuadro.classList.toggle('activo');
        }
    </script>
</body>
</html>
