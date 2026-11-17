# Sistema de control de préstamos de equipos tecnológicos
Este sistema se encarga de llevar un mejor orden y control de préstamos de equipos teniendo en cuenta las solicitudes, préstamos y devoluciones.

## Autora
Alison Daiana Cortés Cruz

## Entorno y tecnologías
- Sistema operativo: Debian 13
- Base de datos: MariaDB 11.8.6
- Servidor web: Apache y PHP 8.4

## Archivos del proyecto
- Basededatos/Prestamos_escuela.sql: Script con la creación de la base de datos y sus tablas.

## Tablas incluidas en db_inventario
- categorias: Tipo de equipos disponibles
- equipos: Descripción del equipo (marca, modelo, etc.)
- solicitantes: Persona que solicita el equipo
- usuarios_sistema: Persona con autorización de acceder al sistema 
- prestamos: historial, equipo, solicitante y fecha de devolución

## Modelo de Datos (Relaciones)
En esta base de datos se sigue un modelo relacional compuesto por 5 tablas principales conectadas mediante llaves foráneas:
- categorias y equipos: Cada equipo pertenece a una categoría y una categoría puede tener varios equipos.
- equipos y prestamos: Un equipo puede prestarse varias veces en diferentes fechas y cada préstamo queda guardado en el historial.
- solicitantes y prestamos: Un alumno o maestro puede pedir varios equipos prestados en distintas fechas.
- usuarios_sistema y prestamos: El usuario registra en el sistema quién entrega el equipo y quién lo recibe cuando lo devuelven.