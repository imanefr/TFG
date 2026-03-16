<?php
// NO hacer session_start() aquí - ya se hace en el archivo principal
// Verificar que el usuario sigue logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Si no se define un título personalizado, se muestra el genérico
$titulo_dashboard = isset($titulo_dashboard) ? $titulo_dashboard : 'Dashboard Administrativo IES La Arboleda';
?>

<div class="dashboard_inicio_header">
    <div class="dashboard_inicio_header_left">
        <h1 class="dashboard_inicio_saludo">
            <i class="fas fa-tachometer-alt"></i>
            <?php echo htmlspecialchars($titulo_dashboard); ?>
        </h1>
    </div>

    <div class="dashboard_inicio_header_right">
        <div class="dashboard_user_simple">
            <i class="fas fa-user dashboard_user_icon"></i>
            <span class="dashboard_user_role"><?php echo ucfirst($_SESSION['usuario_rol']); ?></span>
        </div>
        <form method="POST" action="logout.php" class="dashboard_logout_simple">
            <button type="submit" class="dashboard_logout_text">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
