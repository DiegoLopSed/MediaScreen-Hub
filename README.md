# MediaScreen Hub

Un sistema web completo para la gestión y visualización de contenido multimedia en pantallas digitales. Permite crear, configurar y administrar pantallas con contenido dinámico incluyendo imágenes, videos y enlaces externos.

## 📋 Tabla de Contenidos

- [Características Principales](#-características-principales)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Configuración de Base de Datos](#-configuración-de-base-de-datos)
- [Documentación de Componentes](#-documentación-de-componentes)
- [Funcionalidades](#-funcionalidades)
- [API y Endpoints](#-api-y-endpoints)
- [Seguridad](#-seguridad)
- [Personalización](#-personalización)
- [Troubleshooting](#-troubleshooting)

## 🚀 Características Principales

- **Dashboard Administrativo**: Interfaz moderna para gestionar pantallas
- **Sistema de Autenticación**: Login y registro de usuarios
- **Gestión de Contenido**: Subida de archivos y enlaces externos
- **Visualización en Tiempo Real**: Pantallas optimizadas para displays
- **Diseño Responsivo**: Compatible con dispositivos móviles y tablets
- **Paleta de Colores Azules**: Diseño profesional y moderno
- **Soporte Multimedia**: Imágenes, videos y contenido web embebido

## 📁 Estructura del Proyecto

```
MediaScreen-Hub/
├── assets/
│   └── uploads/                 # Archivos multimedia subidos
├── css/
│   └── main.css                 # Estilos principales del sistema
├── functions/                   # Funcionalidades del sistema
│   ├── add.php                  # Crear nuevas pantallas
│   ├── screen_config.php        # Configurar contenido de pantallas
│   └── screen_delete.php        # Eliminar pantallas
├── includes/                    # Archivos de configuración
│   ├── auth.php                 # Sistema de autenticación
│   └── db.php                   # Conexión a base de datos
├── screens/                     # Pantallas generadas dinámicamente
│   └── Screen_Sistemas.php      # Ejemplo de pantalla
├── index.php                    # Dashboard principal
├── login.php                    # Página de inicio de sesión
├── register.php                 # Página de registro
├── logout.php                   # Cierre de sesión
└── README.md                    # Este archivo
```

## ⚙️ Requisitos del Sistema

### Servidor Web
- **Apache** 2.4+ o **Nginx** 1.18+
- **PHP** 7.4+ (recomendado PHP 8.0+)
- **MySQL** 5.7+ o **MariaDB** 10.3+

### Extensiones PHP Requeridas
- `pdo_mysql` - Para conexión a base de datos
- `gd` o `imagick` - Para procesamiento de imágenes
- `fileinfo` - Para validación de tipos de archivo
- `openssl` - Para funciones de seguridad

### Navegadores Soportados
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🛠️ Instalación

### 1. Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd MediaScreen-Hub
```

### 2. Configurar Servidor Web
- Colocar el proyecto en el directorio web (ej: `htdocs`, `www`, `public_html`)
- Asegurar permisos de escritura en `assets/uploads/`

### 3. Configurar Base de Datos
```sql
CREATE DATABASE pantallas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Importar Estructura de Base de Datos
```sql
-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pantallas
CREATE TABLE screens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    domain VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de contenido multimedia
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    screen_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE CASCADE
);
```

## 🗄️ Configuración de Base de Datos

### Archivo: `includes/db.php`
```php
<?php
$host = 'localhost';        // Servidor de base de datos
$dbname = 'pantallas_db';   // Nombre de la base de datos
$username = 'root';         // Usuario de MySQL
$password = '';             // Contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
```

**Características:**
- Conexión PDO segura con manejo de errores
- Configuración de modo de error para debugging
- Soporte para UTF-8

## 📚 Documentación de Componentes

### 🔐 Sistema de Autenticación

#### `includes/auth.php`
**Propósito**: Verificar sesiones de usuario activas en MediaScreen Hub
```php
<?php
session_start();

// Verificar si la sesión del usuario está activa
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
```

**Funcionalidades:**
- Inicio de sesión automático
- Redirección a login si no hay sesión
- Protección de rutas administrativas

#### `login.php`
**Propósito**: Página de inicio de sesión de MediaScreen Hub
**Características:**
- Formulario de autenticación seguro
- Validación de credenciales
- Hash de contraseñas con `password_verify()`
- Diseño responsivo con paleta azul
- Iconos FontAwesome integrados

**Flujo de Autenticación:**
1. Usuario ingresa credenciales
2. Validación contra base de datos
3. Verificación de hash de contraseña
4. Creación de sesión
5. Redirección al dashboard

#### `register.php`
**Propósito**: Registro de nuevos usuarios
**Validaciones:**
- Campos obligatorios
- Formato de email válido
- Coincidencia de contraseñas
- Usuario/email únicos
- Sanitización de datos

#### `logout.php`
**Propósito**: Cierre seguro de sesión
**Características:**
- Destrucción completa de sesión
- Limpieza de cookies
- Headers anti-caché
- Redirección segura

### 🏠 Dashboard Principal

#### `index.php`
**Propósito**: Panel de administración principal de MediaScreen Hub
**Características:**
- Listado de pantallas disponibles
- Vista previa de contenido
- Acciones rápidas (Ver, Editar, Eliminar)
- Botón para agregar nuevas pantallas
- Diseño de tarjetas responsivo

**Funcionalidades:**
- Consulta optimizada con JOIN
- Manejo de contenido multimedia
- Enlaces directos a pantallas
- Confirmación de eliminación

### 📱 Gestión de Pantallas

#### `functions/add.php`
**Propósito**: Crear nuevas pantallas
**Validaciones:**
- Nombre y dominio obligatorios
- Formato de dominio válido (letras, números, guiones)
- Dominio único en base de datos

**Proceso de Creación:**
1. Validación de datos
2. Inserción en base de datos
3. Creación de archivo PHP dinámico
4. Generación de estructura de pantalla
5. Confirmación de éxito

**Archivo Generado:**
```php
<?php
include '../includes/db.php';

// Obtener información de pantalla
$stmt = $pdo->prepare("SELECT id, name FROM screens WHERE domain = ?");
$stmt->execute(['DOMINIO']);
$screen = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener contenido multimedia
$stmt = $pdo->prepare("SELECT * FROM media WHERE screen_id = ?");
$stmt->execute([$screen['id']]);
$mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<!-- Estructura HTML completa -->
</html>
```

#### `functions/screen_config.php`
**Propósito**: Configurar contenido de pantallas
**Tipos de Contenido Soportados:**
- **Archivos Locales**: Imágenes (JPG, PNG, GIF, WebP), Videos (MP4, WebM, OGG)
- **Enlaces Externos**: URLs de YouTube, Vimeo, sitios web
- **Conversión Automática**: YouTube a formato embed

**Características:**
- Vista previa del contenido actual
- Interfaz de subida de archivos
- Campo para URLs externas
- Validación de tipos de archivo
- Patrón Post/Redirect/Get (PRG)

**Proceso de Actualización:**
1. Eliminación de contenido anterior
2. Validación de nuevo contenido
3. Procesamiento según tipo
4. Actualización en base de datos
5. Redirección con confirmación

#### `functions/screen_delete.php`
**Propósito**: Eliminar pantallas y su contenido
**Proceso de Eliminación:**
1. Verificación de ID válido
2. Eliminación de archivo PHP
3. Eliminación de archivos multimedia
4. Limpieza de registros de base de datos
5. Redirección con confirmación

### 🎨 Sistema de Estilos

#### `css/main.css`
**Propósito**: Estilos principales de MediaScreen Hub
**Características:**
- **Paleta de Colores Azules**: Gradientes modernos
- **Diseño Responsivo**: Mobile-first approach
- **Componentes Modulares**: Reutilización de estilos
- **Efectos Modernos**: Sombras, transiciones, hover effects

**Estructura de Estilos:**
```css
/* Reset y configuración base */
* { margin: 0; padding: 0; box-sizing: border-box; }

/* Header con gradiente azul */
.header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}

/* Tarjetas de pantallas */
.screen-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

/* Formularios de login */
.login-body {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}

/* Botones con diferentes estilos */
.btn-primary { background-color: #1e3c72; }
.btn-secondary { background-color: #6c757d; }
.btn-info { background-color: #4a90e2; }
.btn-danger { background-color: #dc3545; }
```

**Componentes Estilizados:**
- Headers con gradientes
- Tarjetas con sombras
- Formularios con iconos
- Botones con efectos hover
- Alertas de éxito/error
- Navegación responsiva

### 📺 Pantallas de Visualización

#### `screens/Screen_Sistemas.php` (Ejemplo)
**Propósito**: Pantalla de visualización de contenido de MediaScreen Hub
**Características:**
- **Fondo con Gradiente Azul**: Diseño atractivo
- **Header Flotante**: Información de la pantalla
- **Contenido Centrado**: Optimizado para displays
- **Vista Fullscreen**: Para imágenes
- **Soporte Multimedia**: Imágenes, videos, iframes

**Tipos de Contenido:**
```php
// Enlaces externos (YouTube, Vimeo, etc.)
if (filter_var($path, FILTER_VALIDATE_URL)) {
    echo '<iframe src="' . htmlspecialchars($path) . '" class="media-content" allowfullscreen></iframe>';
}

// Imágenes locales
elseif (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    echo '<img src="' . htmlspecialchars($localPath) . '" class="media-content" onclick="openFullscreen()">';
}

// Videos locales
elseif (in_array(strtolower($ext), ['mp4', 'webm', 'ogg'])) {
    echo '<video controls autoplay loop muted class="media-content">';
}
```

**JavaScript Integrado:**
- Función de vista fullscreen
- Cierre con tecla Escape
- Cierre con clic fuera de imagen
- Botón de cierre flotante

## 🔧 Funcionalidades

### Gestión de Usuarios
- ✅ Registro de nuevos usuarios
- ✅ Autenticación segura
- ✅ Hash de contraseñas
- ✅ Validación de datos
- ✅ Cierre de sesión seguro

### Gestión de Pantallas
- ✅ Creación de pantallas
- ✅ Configuración de contenido
- ✅ Eliminación completa
- ✅ Vista previa en tiempo real
- ✅ Enlaces directos

### Gestión de Contenido
- ✅ Subida de archivos multimedia
- ✅ Enlaces externos (YouTube, Vimeo)
- ✅ Conversión automática de URLs
- ✅ Validación de tipos de archivo
- ✅ Vista fullscreen para imágenes

### Interfaz de Usuario
- ✅ Diseño responsivo
- ✅ Paleta de colores azules
- ✅ Iconos FontAwesome
- ✅ Efectos hover y transiciones
- ✅ Alertas de confirmación


## 🔒 Seguridad

### Medidas Implementadas
- **Autenticación de Sesiones**: Verificación en cada página protegida
- **Hash de Contraseñas**: Uso de `password_hash()` y `password_verify()`
- **Sanitización de Datos**: `htmlspecialchars()` en todas las salidas
- **Validación de Entrada**: Verificación de tipos y formatos
- **Preparación de Consultas**: Uso de PDO prepared statements
- **Validación de Archivos**: Verificación de tipos MIME
- **Limpieza de Sesiones**: Destrucción completa al cerrar sesión

### Archivos Protegidos
- `index.php` - Requiere autenticación
- `functions/add.php` - Requiere autenticación
- `functions/screen_config.php` - Requiere autenticación
- `functions/screen_delete.php` - Requiere autenticación

### Archivos Públicos
- `login.php` - Acceso público
- `register.php` - Acceso público
- `screens/*.php` - Acceso público (pantallas de visualización)

## 🎨 Personalización

### Cambiar Paleta de Colores
Editar variables en `css/main.css`:
```css
/* Colores principales */
:root {
    --primary-blue: #1e3c72;
    --secondary-blue: #2a5298;
    --accent-blue: #4a90e2;
    --light-blue: #e3f2fd;
}
```

### Modificar Estructura de Base de Datos
1. Actualizar `includes/db.php` con nueva configuración
2. Modificar consultas SQL en archivos PHP
3. Actualizar formularios según nuevos campos

### Personalizar Pantallas de Visualización
1. Editar plantilla en `functions/add.php`
2. Modificar estilos CSS en archivos generados
3. Agregar nuevas funcionalidades JavaScript

## 🐛 Troubleshooting

### Problemas Comunes

#### Error de Conexión a Base de Datos
```
Error de conexión: SQLSTATE[HY000] [1045] Access denied
```
**Solución**: Verificar credenciales en `includes/db.php`

#### Archivos No Se Suben
```
Error al subir el archivo
```
**Solución**: Verificar permisos de escritura en `assets/uploads/`

#### Pantallas No Se Generan
```
Error al crear el archivo
```
**Solución**: Verificar permisos de escritura en directorio `screens/`

#### Sesión No Se Mantiene
```
Redirección constante al login
```
**Solución**: Verificar configuración de sesiones PHP

### Logs de Debugging
Agregar al inicio de archivos PHP:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Verificación de Requisitos
```bash
# Verificar extensiones PHP
php -m | grep -E "(pdo_mysql|gd|fileinfo|openssl)"

# Verificar permisos
ls -la assets/uploads/
ls -la screens/
```

## 📝 Notas de Desarrollo

### Estructura de Base de Datos
- **users**: Almacena información de usuarios
- **screens**: Información de pantallas creadas
- **media**: Contenido multimedia asociado a pantallas

### Flujo de Datos
1. Usuario se autentica
2. Crea pantalla con nombre y dominio
3. Sistema genera archivo PHP dinámico
4. Usuario configura contenido multimedia
5. Pantalla se actualiza automáticamente

### Consideraciones de Rendimiento
- Consultas optimizadas con JOIN
- Índices en campos únicos
- Limpieza automática de archivos huérfanos
- Cache de sesiones configurado

## 📄 Licencia

Este proyecto está disponible bajo licencia MIT. Ver archivo LICENSE para más detalles.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📞 Soporte

Para soporte técnico o preguntas:
- **Email**: diegolopsed160703@gmail.com
- Crear un issue en el repositorio
- Documentar el problema con detalles
- Incluir logs de error si aplica
- Especificar versión de PHP y navegador

---

**Versión**: 1.0.0  
**Última actualización**: 2024  
**Autor**: Diego Lopez Sedeño (Diego Develop)  
**Proyecto**: MediaScreen Hub  