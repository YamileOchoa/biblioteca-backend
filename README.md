# 📚 Biblioteca API

**API RESTful para la gestión moderna de bibliotecas**  
*Desarrollada con Laravel · Documentada con Swagger · Lista para producción*

![Laravel](https://img.shields.io/badge/Laravel-10.x-ff2d20?style=for-the-badge)
![MySQL](https://img.shields.io/badge/MySQL-Compatible-00758f?style=for-the-badge)
![Swagger](https://img.shields.io/badge/Swagger-UI-85ea2d?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)

## 🏗️ Arquitectura del Proyecto
biblioteca-api/
├── app/
│ ├── Http/
│ │ └── Controllers/ # Controladores de la API
│ ├── Models/ # Modelos Eloquent (User, Book, Loan, etc.)
│ └── ...
├── config/ # Configuración de Laravel y paquetes
├── database/
│ ├── migrations/ # Migraciones de base de datos
│ ├── seeders/ # Seeders para datos iniciales
│ └── ...
├── public/ # Archivos públicos (portadas, etc.)
├── routes/
│ └── api.php # Rutas de la API
├── storage/ # Archivos generados y logs
├── .env # Variables de entorno (NO subir a GitHub)
└── ...

## 🛡️ Autenticación
La API usa Laravel Sanctum.  
Incluye tu token en el header para acceder a rutas protegidas:


## 📖 Documentación interactiva
Genera la documentación con:
php artisan l5-swagger:generate


## 🧑‍💻 Endpoints principales

| 🚦 Método | 🌐 Endpoint                 | 📋 Descripción                       |
|----------|-----------------------------|-------------------------------------|
| POST     | /api/login                  | Login de usuario                    |
| GET      | /api/books                  | Listar libros                       |
| POST     | /api/loans                  | Solicitar préstamo                  |
| POST     | /api/loans/{id}/return      | Devolver libro (solo admin)        |
| GET      | /api/users                  | Listar usuarios (solo admin)       |

## 🌐 Despliegue en Railway
Crea un proyecto y base de datos en Railway.  
Copia las variables de conexión a tu `.env`.  
Sube tu código y ejecuta migraciones:


## 💡 ¿Quieres frontend?
¡Conecta esta API fácilmente con React, Vite, Angular o cualquier framework moderno!

## 📝 Licencia
MIT

¡Contribuciones, issues y estrellas son bienvenidas! ⭐
