<?php
// NO hacer session_start() aquí - ya se hace en el archivo principal
// Verificar que el usuario sigue logueado (seguridad crítica)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir si sesión expiró
    exit; // Detener ejecución inmediatamente
}

// Título dinámico del dashboard - reutilizable en páginas hijas
// Prioriza título personalizado, sino usa genérico del instituto
$titulo_dashboard = isset($titulo_dashboard) ? $titulo_dashboard : 'Dashboard Administrativo IES La Arboleda';
?>

<!-- HEADER PRINCIPAL DASHBOARD - Reutilizable en todas las páginas admin -->
<div class="dashboard_inicio_header">
    <!-- SECCIÓN IZQUIERDA: Título + Icono del módulo actual -->
    <div class="dashboard_inicio_header_left">
        <h1 class="dashboard_inicio_saludo">
            <!-- Icono dinámico del dashboard (tachómetro genérico) -->
            <i class="fas fa-tachometer-alt"></i>
            <!-- Título seguro contra XSS, adaptable por página -->
            <?php echo htmlspecialchars($titulo_dashboard); ?>
        </h1>
    </div>

    <!-- SECCIÓN DERECHA: Info usuario + Logout -->
    <div class="dashboard_inicio_header_right">
        <!-- INFO USUARIO SIMPLIFICADA - Sin foto para rendimiento -->
        <div class="dashboard_user_simple">
            <!-- Icono usuario genérico -->
            <i class="fas fa-user dashboard_user_icon"></i>
            <!-- Rol del usuario con primera letra mayúscula -->
            <span class="dashboard_user_role">
                <?php echo ucfirst(htmlspecialchars($_SESSION['usuario_rol'])); ?>
            </span>
        </div>
        
        <!-- FORMULARIO LOGOUT - Método POST seguro -->
        <form method="POST" action="logout.php" class="dashboard_logout_simple">
            <!-- Botón submit con icono y texto descriptivo -->
            <button type="submit" class="dashboard_logout_text">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
