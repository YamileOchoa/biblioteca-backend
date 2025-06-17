# 📚 Biblioteca API

**API RESTful para la gestión moderna de bibliotecas**  
*Desarrollada con Laravel · Documentada con Swagger · Lista para producción*

![Laravel](https://img.shields.io/badge/Laravel-10.x-ff2d20?style=for-the-badge)
![MySQL](https://img.shields.io/badge/MySQL-Compatible-00758f?style=for-the-badge)
![Swagger](https://img.shields.io/badge/Swagger-UI-85ea2d?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)

## 🏗️ Arquitectura del Proyecto

```
biblioteca-api/
├── app/
│   ├── Http/
│   │   └── Controllers/        # Controladores de la API
│   ├── Models/                 # Modelos Eloquent (User, Book, Loan, etc.)
│   └── ...
├── config/                     # Configuración de Laravel y paquetes
├── database/
│   ├── migrations/             # Migraciones de base de datos
│   ├── seeders/                # Seeders para datos iniciales
│   └── ...
├── public/                     # Archivos públicos (portadas, etc.)
├── routes/
│   └── api.php                 # Rutas de la API
├── storage/                    # Archivos generados y logs
├── .env                        # Variables de entorno (NO subir a GitHub)
└── ...
```

## 🛡️ Autenticación

La API utiliza **Laravel Sanctum** para autenticación.  
Incluye tu token en el header para acceder a rutas protegidas:

```bash
Authorization: Bearer {tu-token-aqui}
```

## 📖 Documentación Interactiva

Genera la documentación Swagger con:

```bash
php artisan l5-swagger:generate
```

Luego visita: `http://localhost:8000/api/documentation`

## 🧑‍💻 Endpoints Principales

| 🚦 Método | 🌐 Endpoint                 | 📋 Descripción                       |
|----------|-----------------------------|-------------------------------------|
| POST     | `/api/login`                | Login de usuario                    |
| GET      | `/api/books`                | Listar libros                       |
| POST     | `/api/loans`                | Solicitar préstamo                  |
| POST     | `/api/loans/{id}/return`    | Devolver libro (solo admin)        |
| GET      | `/api/users`                | Listar usuarios (solo admin)       |

## 🚀 Instalación y Configuración

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/biblioteca-api.git
   cd biblioteca-api
   ```

2. **Instala dependencias**
   ```bash
   composer install
   ```

3. **Configura el entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configura la base de datos en `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=biblioteca_db
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

5. **Ejecuta migraciones y seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Inicia el servidor**
   ```bash
   php artisan serve
   ```

## 🌐 Despliegue en Railway

1. Crea un proyecto y base de datos en [Railway](https://railway.app)
2. Copia las variables de conexión a tu `.env`
3. Sube tu código y ejecuta migraciones:

```bash
php artisan migrate --seed --force
```

## 💡 ¿Quieres Frontend?

¡Conecta esta API fácilmente con React, Vue, Angular o cualquier framework moderno!

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ve el archivo [LICENSE](LICENSE) para más detalles.

---

¡Contribuciones, issues y estrellas son bienvenidas! ⭐
