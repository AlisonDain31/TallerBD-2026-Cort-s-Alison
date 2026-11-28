<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitantes - Inventario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header><h1>📦 Sistema de Control de Inventario</h1></header>
    <nav>
        <a href="index.html">Inicio</a>
        <a href="equipos.html">Equipos</a>
        <a href="categorias.html">Categorías</a>
        <a href="prestamos.html">Préstamos</a>
        <a href="solicitantes.html" class="active">Solicitantes</a>
        <a href="usuarios.html">Usuarios Sistema</a>
    </nav>
    <div class="container">
        <div class="card">
            <h2>Registrar Solicitante</h2>
            <form action="#" method="POST">
                <div class="grid-form">
                    <div class="form-group"><label>Número de Empleado *</label><input type="text" name="numero_empleado" required></div>
                    <div class="form-group"><label>Nombre Completo *</label><input type="text" name="nombre_completo" required></div>
                    <div class="form-group"><label>Departamento *</label><input type="text" name="departamento" required></div>
                    <div class="form-group"><label>Estado</label><select name="activo"><option value="1">Activo</option><option value="0">Inactivo</option></select></div>
                </div>
                <button type="submit">Guardar Solicitante</button>
            </form>
        </div>
        <div class="card">
            <h2>Directorio de Solicitantes</h2>
            <table>
                <thead><tr><th>ID</th><th>No. Empleado</th><th>Nombre</th><th>Departamento</th><th>Activo</th><th>Acciones</th></tr></thead>
                <tbody>
                    <tr><td>1</td><td>EMP-102</td><td>Ana López</td><td>Ingeniería en Sistemas</td><td>Sí</td><td><button class="btn-edit">Editar</button> <button class="btn-del">Eliminar</button></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>