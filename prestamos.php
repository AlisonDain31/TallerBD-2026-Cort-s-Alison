<?php
require 'conexion.php';
$mensaje = '';

if (isset($_GET['devolver'])) {
    $id_prestamo = $_GET['devolver'];
    
    $stmt = $pdo->prepare("SELECT id_equipo FROM prestamos WHERE id_prestamo = ?");
    $stmt->execute([$id_prestamo]);
    $prestamo = $stmt->fetch();

    if ($prestamo) {
        $pdo->beginTransaction();
        try {
            $updPrestamo = $pdo->prepare("UPDATE prestamos SET fecha_devolucion_real = NOW(), condicion_devolucion = 'Buena', id_usuario_recibe = 1 WHERE id_prestamo = ?");
            $updPrestamo->execute([$id_prestamo]);

            $updEquipo = $pdo->prepare("UPDATE equipos SET estado = 'Disponible' WHERE id_equipo = ?");
            $updEquipo->execute([$prestamo['id_equipo']]);

            $pdo->commit();
            header("Location: prestamos.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje = "Error al registrar la devolución.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_equipo = $_POST['id_equipo'];
    $id_solicitante = $_POST['id_solicitante'];
    $id_usuario = $_POST['id_usuario_entrega'];
    $fecha_prevista = $_POST['fecha_devolucion_prevista'];
    $condicion = $_POST['condicion_devolucion'];
    $obs = trim($_POST['observaciones']);

    $pdo->beginTransaction();
    try {
        $check = $pdo->prepare("SELECT estado FROM equipos WHERE id_equipo = ? FOR UPDATE");
        $check->execute([$id_equipo]);
        if ($check->fetchColumn() !== 'Disponible') {
            throw new Exception("El equipo ya no está disponible.");
        }

        $ins = $pdo->prepare("INSERT INTO prestamos (id_equipo, id_solicitante, id_usuario_entrega, fecha_devolucion_prevista, condicion_devolucion, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$id_equipo, $id_solicitante, $id_usuario, $fecha_prevista, $condicion, $obs]);

        $upd = $pdo->prepare("UPDATE equipos SET estado = 'Prestado' WHERE id_equipo = ?");
        $upd->execute([$id_equipo]);

        $pdo->commit();
        header("Location: prestamos.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensaje = $e->getMessage();
    }
}

$equipos_disp = $pdo->query("SELECT id_equipo, numero_inventario, descripcion FROM equipos WHERE estado = 'Disponible'")->fetchAll();
$solicitantes = $pdo->query("SELECT id_solicitante, numero_empleado, nombre_completo, departamento FROM solicitantes WHERE activo = 1")->fetchAll();
$usuarios     = $pdo->query("SELECT id_usuario, nombre_completo FROM usuarios_sistema WHERE activo = 1")->fetchAll();

$historial = $pdo->query("
    SELECT p.*, e.numero_inventario, e.descripcion AS equipo_desc, s.nombre_completo AS solicitante_nombre
    FROM prestamos p
    INNER JOIN equipos e ON p.id_equipo = e.id_equipo
    INNER JOIN solicitantes s ON p.id_solicitante = s.id_solicitante
    ORDER BY p.fecha_prestamo DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos - Inventario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header><h1>📦 Sistema de Control de Inventario</h1></header>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="equipos.php">Equipos</a>
        <a href="categorias.php">Categorías</a>
        <a href="prestamos.php" class="active">Préstamos</a>
        <a href="solicitantes.php">Solicitantes</a>
        <a href="usuarios.php">Usuarios Sistema</a>
    </nav>
    <div class="container">
        <?php if ($mensaje): ?>
            <div class="card" style="border-left: 4px solid #ef4444; color: #b91c1c;"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <div class="card">
            <h2>Registrar Nuevo Préstamo</h2>
            <form action="prestamos.php" method="POST">
                <div class="grid-form">
                    <div class="form-group">
                        <label>Equipo Disponible *</label>
                        <select name="id_equipo" required>
                            <option value="">Seleccione un equipo...</option>
                            <?php foreach ($equipos_disp as $eq): ?>
                                <option value="<?= $eq['id_equipo'] ?>"><?= htmlspecialchars($eq['numero_inventario'] . ' - ' . $eq['descripcion']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Solicitante *</label>
                        <select name="id_solicitante" required>
                            <?php foreach ($solicitantes as $sol): ?>
                                <option value="<?= $sol['id_solicitante'] ?>"><?= htmlspecialchars($sol['numero_empleado'] . ' - ' . $sol['nombre_completo'] . ' (' . $sol['departamento'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Usuario que Entrega *</label>
                        <select name="id_usuario_entrega" required>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha Devolución Prevista *</label>
                        <input type="datetime-local" name="fecha_devolucion_prevista" required>
                    </div>
                    <div class="form-group">
                        <label>Condición / Estado</label>
                        <select name="condicion_devolucion">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Buena">Buena</option>
                            <option value="Con falla">Con falla</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <input type="text" name="observaciones" placeholder="Incluye cargador y maletín">
                    </div>
                </div>
                <button type="submit">Registrar Préstamo</button>
            </form>
        </div>
        <div class="card">
            <h2>Historial de Préstamos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Equipo</th><th>Solicitante</th><th>Fecha Préstamo</th><th>Devolución Prevista</th><th>Condición</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                    <tr>
                        <td><?= $h['id_prestamo'] ?></td>
                        <td><?= htmlspecialchars($h['numero_inventario'] . ' (' . $h['equipo_desc'] . ')') ?></td>
                        <td><?= htmlspecialchars($h['solicitante_nombre']) ?></td>
                        <td><?= $h['fecha_prestamo'] ?></td>
                        <td><?= $h['fecha_devolucion_prevista'] ?></td>
                        <td><?= $h['condicion_devolucion'] ?></td>
                        <td>
                            <?php if ($h['fecha_devolucion_real'] === null): ?>
                                <a href="prestamos.php?devolver=<?= $h['id_prestamo'] ?>" onclick="return confirm('¿Confirmar devolución en buen estado?')"><button type="button" class="btn-edit">Registrar Devolución</button></a>
                            <?php else: ?>
                                <span class="badge disp">Devuelto (<?= $h['fecha_devolucion_real'] ?>)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>