<?php
require 'conexion.php';
$mensaje = '';
$editando = null;

if (isset($_GET['eliminar'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios_sistema WHERE id_usuario = ?");
        $stmt->execute([$_GET['eliminar']]);
        header("Location: usuarios.php");
        exit;
    } catch (PDOException $e) {
        $mensaje = "No se puede eliminar porque este usuario ha registrado préstamos.";
    }
}

if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios_sistema WHERE id_usuario = ?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['nombre_usuario']);
    $pass = $_POST['contraseña_usuario'];
    $nom  = trim($_POST['nombre_completo']);
    $rol  = $_POST['rol'];

    if (!empty($_POST['id_usuario'])) {
        if (!empty($pass)) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios_sistema SET nombre_usuario=?, contraseña_usuario=?, nombre_completo=?, rol=? WHERE id_usuario=?");
            $stmt->execute([$user, $hash, $nom, $rol, $_POST['id_usuario']]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios_sistema SET nombre_usuario=?, nombre_completo=?, rol=? WHERE id_usuario=?");
            $stmt->execute([$user, $nom, $rol, $_POST['id_usuario']]);
        }
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios_sistema (nombre_usuario, contraseña_usuario, nombre_completo, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user, $hash, $nom, $rol]);
    }
    header("Location: usuarios.php");
    exit;
}

$usuarios = $pdo->query("SELECT * FROM usuarios_sistema ORDER BY id_usuario ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - Inventario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header><h1>📦 Sistema de Control de Inventario</h1></header>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="equipos.php">Equipos</a>
        <a href="categorias.php">Categorías</a>
        <a href="prestamos.php">Préstamos</a>
        <a href="solicitantes.php">Solicitantes</a>
        <a href="usuarios.php" class="active">Usuarios Sistema</a>
    </nav>
    <div class="container">
        <?php if ($mensaje): ?>
            <div class="card" style="border-left: 4px solid #ef4444; color: #b91c1c;"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <div class="card">
            <h2><?= $editando ? 'Editar Usuario' : 'Registrar Usuario del Sistema' ?></h2>
            <form action="usuarios.php" method="POST">
                <input type="hidden" name="id_usuario" value="<?= $editando['id_usuario'] ?? '' ?>">
                <div class="grid-form">
                    <div class="form-group"><label>Nombre de Usuario *</label><input type="text" name="nombre_usuario" value="<?= htmlspecialchars($editando['nombre_usuario'] ?? '') ?>" required></div>
                    <div class="form-group"><label>Contraseña <?= $editando ? '(dejar vacío para no cambiar)' : '*' ?></label><input type="password" name="contraseña_usuario" <?= $editando ? '' : 'required' ?>></div>
                    <div class="form-group"><label>Nombre Completo *</label><input type="text" name="nombre_completo" value="<?= htmlspecialchars($editando['nombre_completo'] ?? '') ?>" required></div>
                    <div class="form-group">
                        <label>Rol *</label>
                        <select name="rol">
                            <option value="Administrador" <?= (isset($editando['rol']) && $editando['rol'] == 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                            <option value="Consulta" <?= (isset($editando['rol']) && $editando['rol'] == 'Consulta') ? 'selected' : '' ?>>Consulta</option>
                        </select>
                    </div>
                </div>
                <button type="submit"><?= $editando ? 'Actualizar Usuario' : 'Guardar Usuario' ?></button>
                <?php if ($editando): ?><a href="usuarios.php" style="margin-left:10px; color:#475569;">Cancelar</a><?php endif; ?>
            </form>
        </div>
        <div class="card">
            <h2>Usuarios del Sistema</h2>
            <table>
                <thead><tr><th>ID</th><th>Usuario</th><th>Nombre Completo</th><th>Rol</th><th>Activo</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td><?= $u['rol'] ?></td>
                        <td><?= $u['activo'] ? 'Sí' : 'No' ?></td>
                        <td>
                            <a href="usuarios.php?editar=<?= $u['id_usuario'] ?>"><button type="button" class="btn-edit">Editar</button></a>
                            <a href="usuarios.php?eliminar=<?= $u['id_usuario'] ?>" onclick="return confirm('¿Eliminar usuario?')"><button type="button" class="btn-del">Eliminar</button></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>