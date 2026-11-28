<?php
require 'conexion.php';
$mensaje = '';
$editando = null;

if (isset($_GET['eliminar'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$_GET['eliminar']]);
        header("Location: categorias.php");
        exit;
    } catch (PDOException $e) {
        $mensaje = "No se puede eliminar la categoría porque tiene equipos asociados.";
    }
}

if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
    $stmt->execute([$_GET['editar']]);
    $editando = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if (!empty($_POST['id_categoria'])) {
        $stmt = $pdo->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
        $stmt->execute([$nombre, $descripcion, $_POST['id_categoria']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->execute([$nombre, $descripcion]);
    }
    header("Location: categorias.php");
    exit;
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY id_categoria ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías - Inventario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header><h1>📦 Sistema de Control de Inventario</h1></header>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="equipos.php">Equipos</a>
        <a href="categorias.php" class="active">Categorías</a>
        <a href="prestamos.php">Préstamos</a>
        <a href="solicitantes.php">Solicitantes</a>
        <a href="usuarios.php">Usuarios Sistema</a>
    </nav>
    <div class="container">
        <?php if ($mensaje): ?>
            <div class="card" style="border-left: 4px solid #ef4444; color: #b91c1c;"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <div class="card">
            <h2><?= $editando ? 'Editar Categoría' : 'Nueva Categoría' ?></h2>
            <form action="categorias.php" method="POST">
                <input type="hidden" name="id_categoria" value="<?= $editando['id_categoria'] ?? '' ?>">
                <div class="grid-form">
                    <div class="form-group">
                        <label>Nombre de Categoría *</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($editando['nombre'] ?? '') ?>" placeholder="Ej. Cómputo" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" value="<?= htmlspecialchars($editando['descripcion'] ?? '') ?>" placeholder="Equipos portátiles y de escritorio">
                    </div>
                </div>
                <button type="submit"><?= $editando ? 'Actualizar Categoría' : 'Guardar Categoría' ?></button>
                <?php if ($editando): ?>
                    <a href="categorias.php" style="margin-left:10px; color:#475569;">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="card">
            <h2>Categorías Registradas</h2>
            <table>
                <thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?= $cat['id_categoria'] ?></td>
                        <td><?= htmlspecialchars($cat['nombre']) ?></td>
                        <td><?= htmlspecialchars($cat['descripcion']) ?></td>
                        <td>
                            <a href="categorias.php?editar=<?= $cat['id_categoria'] ?>"><button type="button" class="btn-edit">Editar</button></a>
                            <a href="categorias.php?eliminar=<?= $cat['id_categoria'] ?>" onclick="return confirm('¿Eliminar esta categoría?')"><button type="button" class="btn-del">Eliminar</button></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>