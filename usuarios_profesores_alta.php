<?php
// usuarios_profesores_alta.php
session_start();
include 'conexion.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_dashboard = "Alta de Acceso Docente";
$error = $success = '';
$profesores = [];
$prof_data = null;
$step = 1; 
$roles = [];
$accesos = [];

// CARGAR ROLES Y ACCESOS
$roles_result = $conexion->query("SELECT id, nombre_rol FROM roles ORDER BY id ASC");
while ($row = $roles_result->fetch_assoc()) { $roles[] = $row; }

$accesos_result = $conexion->query("SELECT id, nombre_dashboard FROM accesos ORDER BY id ASC");
while ($row = $accesos_result->fetch_assoc()) { $accesos[] = $row; }

/**
 * FILTRO CLAVE:
 * Seleccionamos solo los profesores donde usuario_id sea NULL.
 * Esto evita duplicar cuentas para un mismo docente.
 */
$sql_base_busqueda = "SELECT * FROM profesores WHERE usuario_id IS NULL";

// PASO 1: Búsqueda de docentes sin usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $search = "%" . trim($_POST['search']) . "%";
    // Combinamos el filtro de NULL con la búsqueda por nombre/dni
    $sql_search = $sql_base_busqueda . " AND (nombre LIKE ? OR dni LIKE ?)";
    $stmt = $conexion->prepare($sql_search);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $profesores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// PASO 1 → PASO 2: Selección de docente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profesor_id']) && !isset($_POST['crear_usuario'])) {
    $prof_id = (int)$_POST['profesor_id'];
    // Verificamos que siga siendo NULL por seguridad
    $stmt = $conexion->prepare("SELECT * FROM profesores WHERE id = ? AND usuario_id IS NULL");
    $stmt->bind_param("i", $prof_id);
    $stmt->execute();
    $prof_data = $stmt->get_result()->fetch_assoc();
    if ($prof_data) { 
        $step = 2; 
    } else {
        $error = "El profesor seleccionado ya tiene un usuario asignado o no existe.";
    }
    $stmt->close();
}

// PASO 2 → CREAR USUARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $prof_id = (int)$_POST['profesor_id'];
    $password_plana = $_POST['password'];
    $email_final = isset($_POST['email_docente']) ? trim($_POST['email_docente']) : '';
    $usuario_clean = strtolower(preg_replace('/[^a-z0-9]/', '', $_POST['usuario']));
    $rol_id = (int)$_POST['rol_id'];
    $nombre_real = $_POST['nombre_real'];
    
    if (empty($password_plana) || strlen($password_plana) < 6) {
        $error = "La contraseña mínima es de 6 caracteres.";
    } elseif (empty($email_final)) {
        $error = "El correo electrónico es obligatorio.";
    } else {
        $password_segura = password_hash($password_plana, PASSWORD_DEFAULT);

        $conexion->begin_transaction();
        try {
            // 1. Insertar en tabla usuarios
            $sql_user = "INSERT INTO usuarios (usuario, password, nombre, email, rol_id, activo, fecha_registro) 
                         VALUES (?, ?, ?, ?, ?, 1, NOW())";
            $stmt_u = $conexion->prepare($sql_user);
            $stmt_u->bind_param("ssssi", $usuario_clean, $password_segura, $nombre_real, $email_final, $rol_id);
            $stmt_u->execute();
            $nuevo_usuario_id = $conexion->insert_id;

            // 2. Insertar accesos
            if (isset($_POST['accesos']) && is_array($_POST['accesos'])) {
                $sql_acc = "INSERT INTO usuarios_accesos (usuario_id, acceso_id) VALUES (?, ?)";
                $stmt_a = $conexion->prepare($sql_acc);
                foreach ($_POST['accesos'] as $acceso_id) {
                    $stmt_a->bind_param("ii", $nuevo_usuario_id, $acceso_id);
                    $stmt_a->execute();
                }
            }

            // 3. Vincular profesor con el nuevo usuario
            $upd = $conexion->prepare("UPDATE profesores SET usuario_id = ? WHERE id = ?");
            $upd->bind_param("ii", $nuevo_usuario_id, $prof_id);
            $upd->execute();

            $conexion->commit();
            $success = "Acceso creado correctamente para " . htmlspecialchars($nombre_real);
            $step = 3;
        } catch (Exception $e) {
            $conexion->rollback();
            $error = "Error crítico: " . $e->getMessage();
        }
    }
}

// Carga inicial (solo los que no tienen cuenta)
if (empty($profesores) && $step == 1 && !isset($_POST['buscar'])) {
    $res = $conexion->query($sql_base_busqueda . " ORDER BY nombre ASC LIMIT 20");
    $profesores = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta Docente - IES La Arboleda</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
    <link rel="stylesheet" href="style_imane.css">
</head>
<body>

<div class="alta_profesor_container">
    
    <header class="dashboard_avisos_header">
        <h1 class="dashboard_avisos_saludo">
            <i class="fas fa-user-shield"></i> <?= $titulo_dashboard ?>
        </h1>
    </header>

    <?php if ($success): ?>
        <div class="dashboard_avisos_alert dashboard_avisos_alert_success card_alert_success_custom">
            <i class="fas fa-check-circle"></i> <?= $success ?>
            <br><br>
            <a href="dashboard_usuarios.php" class="btn_accion_morado btn_ver_usuarios_custom">Ver todos los usuarios</a>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="dashboard_avisos_alert dashboard_avisos_alert_error card_alert_error_custom">
            <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($step === 1 && !$success): ?>
        <div class="alta_card">
            <h2 class="titulo_seccion">1. Seleccionar Docente</h2>
            <form method="POST" class="buscador_docente buscador_docente_flex">
                <input type="text" name="search" class="form_input_alta input_search_custom" placeholder="Nombre o DNI...">
                <button type="submit" name="buscar" class="btn_accion_morado btn_search_custom">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <form method="POST">
                <div class="profesor_grid profesor_grid_layout">
                    <?php foreach ($profesores as $p): ?>
                        <label class="profesor_item_card label_profesor_card">
                            <input type="radio" name="profesor_id" value="<?= $p['id'] ?>" required>
                            <div class="info_prof">
                                <strong class="nombre_prof_block"><?= htmlspecialchars($p['nombre']) ?></strong><br>
                                <small class="dni_prof_color"><i class="fas fa-id-card"></i> <?= $p['dni'] ?></small>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn_alta_submit btn_submit_full">
                    Configurar Credenciales <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

    <?php elseif ($step === 2): ?>
        <div class="alta_card">
            <h2 class="titulo_seccion">2. Crear cuenta: <?= htmlspecialchars($prof_data['nombre']) ?></h2>
            <form method="POST">
                <input type="hidden" name="profesor_id" value="<?= $prof_data['id'] ?>">
                <input type="hidden" name="nombre_real" value="<?= $prof_data['nombre'] ?>">

                <div class="form_row_doble row_doble_flex">
                    <div class="form_group_alta group_alta_flex1">
                        <label class="form_label_alta label_alta_bold">Usuario</label>
                        <input type="text" name="usuario" class="form_input_alta input_alta_full" value="<?= strtolower(explode(' ', $prof_data['nombre'])[0]) ?>" required>
                    </div>
                    <div class="form_group_alta group_alta_flex1">
                        <label class="form_label_alta label_alta_bold">Contraseña</label>
                        <input type="password" name="password" class="form_input_alta input_alta_full" placeholder="Mín. 6 carac." required>
                    </div>
                </div>

                <div class="form_group_alta group_alta_mb">
                    <label class="form_label_alta label_alta_bold">Email Institucional</label>
                    <?php 
                        // Limpieza de tildes para el email
                        $email_sugerido = strtolower(str_replace(' ', '.', $prof_data['nombre'])) . "@ieslarboleda.es";
                        $buscar_tildes =  array('á','é','í','ó','ú','ñ','Á','É','Í','Ó','Ú','Ñ');
                        $reemplazar_con = array('a','e','i','o','u','n','a','e','i','o','u','n');
                        $email_limpio = str_replace($buscar_tildes, $reemplazar_con, $email_sugerido);
                    ?>
                    <input type="email" name="email_docente" class="form_input_alta input_alta_full" value="<?= $email_limpio ?>" required>
                </div>

                <div class="form_group_alta group_alta_mb">
                    <label class="form_label_alta label_alta_bold">Rol del Usuario</label>
                    <select name="rol_id" id="rol_id" class="form_input_alta input_alta_full" onchange="actualizarInterfazAccesos()" required>
                        <option value="">-- Seleccionar Rol --</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id'] ?>"><?= $rol['nombre_rol'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="contenedor_accesos" class="d-none contenedor_accesos_estilo">
                    <label class="form_label_alta label_alta_bold_mb">Módulos Permitidos</label>
                    <div class="contenedor_accesos_grid accesos_grid_layout">
                        <?php foreach ($accesos as $acc): ?>
                            <label class="acceso-wrapper acceso_wrapper_layout">
                                <input type="checkbox" name="accesos[]" class="check-acceso" value="<?= $acc['id'] ?>">
                                <span><?= $acc['nombre_dashboard'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form_botones_footer botones_footer_flex">
                    <a href="usuarios_profesores_alta.php" class="btn_secundario btn_cancelar_layout">Cancelar</a>
                    <button type="submit" name="crear_usuario" class="btn_alta_submit btn_verde btn_finalizar_layout">Finalizar Alta</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <form method="POST" action="dashboard_usuarios.php" class="dashboard_universal_volver">
        <button type="submit" class="dashboard_universal_btn_volver">
            <i class="fas fa-arrow-left"></i> Volver 
        </button>
    </form>

</div>
<script>
    function actualizarInterfazAccesos() {
        const rol = document.getElementById('rol_id').value; 
        const contenedor = document.getElementById('contenedor_accesos');
        const wrappers = document.querySelectorAll('.acceso-wrapper');

        // 1. Reset inicial
        if (rol === "") {
            contenedor.style.display = 'none';
            resetearAccesos(wrappers);
            return;
        }

        contenedor.style.display = 'block';
        resetearAccesos(wrappers); 

        // 2. Lógica específica
        if (rol == "1") { 
            // ADMIN: Todo marcado
            wrappers.forEach(w => {
                w.querySelector('input').checked = true;
            });

        } else if (rol == "2") { 
            // PROFESOR: Solo departamentos
            wrappers.forEach(w => {
                const nombre = w.querySelector('span').textContent.toLowerCase().trim();
                if (!esDepartamento(nombre)) {
                    w.style.display = 'none';
                }
            });

        } else if (rol == "3") { 
            // ALUMNO: OCULTAR TODO EXCEPTO BLOG
            wrappers.forEach(w => {
                const nombre = w.querySelector('span').textContent.toLowerCase().trim();
                
                // Si el nombre es exactamente "blog", lo mostramos y marcamos
                if (nombre === "blog") {
                    w.style.display = 'flex';
                    w.querySelector('input').checked = true;
                } else {
                    // CUALQUIER OTRO se oculta
                    w.style.display = 'none';
                }
            });

        } else if (rol == "4") { 
            // OTRO: Se queda como está (todo visible tras el reset)
        }
    }

    function resetearAccesos(wrappers) {
        wrappers.forEach(w => {
            w.style.display = 'flex';
            const input = w.querySelector('input');
            if(input) input.checked = false;
        });
    }

    function esDepartamento(nombre) {
        const dptos = [
            "biología", "dibujo", "economía", "física", "filosofía", 
            "francés", "fol", "geografía", "historia", "imagen", 
            "inglés", "latín", "lengua", "matemáticas", "música", 
            "orientación", "religión", "tecnología", "educación física"
        ];
        return dptos.some(d => nombre.includes(d));
    }
</script>
</body>
</html>