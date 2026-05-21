# Rediseño de la página web del instituto IES La Arboleda

## Descripción breve del proyecto
Desarrollo a medida de una aplicación web corporativa para el IES La Arboleda, diseñada para sustituir un sitio antiguo basado en WordPress. Consta de una Parte Pública (responsive) orientada a la información institucional que incluye un asistente virtual inteligente (ArboledaBot), y un Área Privada (Dashboard) con control de acceso por roles (Administrador, Profesor, Alumno, Otro) para la gestión dinámica de contenidos, usuarios y un blog colaborativo.

## Tecnologías utilizadas
* Frontend: HTML5, CSS3, JavaScript nativo y Fetch API.
* Backend: PHP Nativo y extensión PDO (acceso seguro a datos).
* Base de Datos: MySQL.
* Inteligencia Artificial: Conexión asíncrona con Groq Cloud API para el chatbot.
* Entorno de desarrollo: Servidor local Apache mediante XAMPP.

## Integrantes del grupo
* Alumnas: Eli Matarrán María e Imane Fahim Rida
* Tutor: José Barba Gutierrez

## Instrucciones básicas de ejecución
1. Mover el proyecto: Copia la carpeta del código dentro de la ruta de tu servidor local (C:\xampp\htdocs\TFG).
2. Iniciar Servidor: Abre el Panel de Control de XAMPP y arranca los módulos de Apache y MySQL.
3. Base de Datos: Accede a MySQL Workbench o phpMyAdmin, crea la base de datos (arboledatablas) e importa el script SQL incluido en el proyecto.
4. Configurar Conexión: Abre el archivo conexion.php y edita las credenciales de acceso a la base de datos de tu entorno local.
5. Ejecutar: Abre tu navegador e ingresa a la URL: http://localhost/TFG/.
