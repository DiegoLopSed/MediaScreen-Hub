-- =====================================================
-- Sistema de Gestión de Pantallas - Estructura de Base de Datos
-- =====================================================
-- Descripción: Estructura completa de la base de datos
-- Versión: 1.0.0
-- Compatible con: MySQL 5.7+, MariaDB 10.3+
-- =====================================================

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Configuración de caracteres
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- CREACIÓN DE LA BASE DE DATOS
-- =====================================================

CREATE DATABASE IF NOT EXISTS `pantallas_db` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `pantallas_db`;

-- =====================================================
-- TABLA: users (Usuarios del sistema)
-- =====================================================
-- Propósito: Almacena información de usuarios autenticados
-- Relaciones: Ninguna (tabla independiente)

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del usuario',
  `username` varchar(50) NOT NULL COMMENT 'Nombre de usuario único',
  `email` varchar(100) NOT NULL COMMENT 'Correo electrónico único',
  `password` varchar(255) NOT NULL COMMENT 'Hash de la contraseña',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación del usuario',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';

-- =====================================================
-- TABLA: screens (Pantallas del sistema)
-- =====================================================
-- Propósito: Almacena información de las pantallas creadas
-- Relaciones: Relación 1:N con media (una pantalla puede tener múltiples archivos)

CREATE TABLE `screens` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la pantalla',
  `name` varchar(100) NOT NULL COMMENT 'Nombre descriptivo de la pantalla',
  `domain` varchar(255) NOT NULL COMMENT 'Dominio único para la URL de la pantalla',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación de la pantalla',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de pantallas del sistema';

-- =====================================================
-- TABLA: media (Contenido multimedia)
-- =====================================================
-- Propósito: Almacena información del contenido multimedia de las pantallas
-- Relaciones: Relación N:1 con screens (múltiples archivos pueden pertenecer a una pantalla)

CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del archivo multimedia',
  `screen_id` int(11) NOT NULL COMMENT 'ID de la pantalla a la que pertenece',
  `file_path` varchar(500) NOT NULL COMMENT 'Ruta del archivo o URL externa',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de subida del archivo',
  PRIMARY KEY (`id`),
  KEY `screen_id` (`screen_id`),
  CONSTRAINT `media_ibfk_1` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de contenido multimedia';

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índice para búsquedas por fecha de creación de usuarios
CREATE INDEX `idx_users_created_at` ON `users` (`created_at`);

-- Índice para búsquedas por fecha de creación de pantallas
CREATE INDEX `idx_screens_created_at` ON `screens` (`created_at`);

-- Índice para búsquedas por fecha de subida de archivos
CREATE INDEX `idx_media_uploaded_at` ON `media` (`uploaded_at`);

-- Índice para búsquedas por tipo de archivo (basado en extensión)
CREATE INDEX `idx_media_file_type` ON `media` (`file_path`(10));

-- =====================================================
-- VISTAS PARA CONSULTAS FRECUENTES
-- =====================================================

-- Vista: Pantallas con su último contenido
CREATE VIEW `v_screens_with_latest_media` AS
SELECT 
    s.id,
    s.name,
    s.domain,
    s.created_at,
    s.updated_at,
    m.file_path as latest_media,
    m.uploaded_at as media_uploaded_at
FROM screens s
LEFT JOIN (
    SELECT screen_id, file_path, uploaded_at,
           ROW_NUMBER() OVER (PARTITION BY screen_id ORDER BY uploaded_at DESC) as rn
    FROM media
) m ON s.id = m.screen_id AND m.rn = 1;

-- Vista: Estadísticas del sistema
CREATE VIEW `v_system_stats` AS
SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM screens) as total_screens,
    (SELECT COUNT(*) FROM media) as total_media_files,
    (SELECT COUNT(*) FROM media WHERE file_path LIKE 'http%') as external_links,
    (SELECT COUNT(*) FROM media WHERE file_path NOT LIKE 'http%') as local_files;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento: Limpiar archivos huérfanos
DELIMITER //
CREATE PROCEDURE `sp_cleanup_orphaned_files`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE file_path_var VARCHAR(500);
    DECLARE file_cursor CURSOR FOR 
        SELECT file_path FROM media 
        WHERE file_path NOT LIKE 'http%' 
        AND file_path NOT LIKE 'https%';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN file_cursor;
    read_loop: LOOP
        FETCH file_cursor INTO file_path_var;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Aquí se podría agregar lógica para verificar si el archivo existe
        -- y eliminarlo si no está siendo referenciado
    END LOOP;
    CLOSE file_cursor;
END //
DELIMITER ;

-- =====================================================
-- TRIGGERS PARA AUDITORÍA
-- =====================================================

-- Trigger: Log de creación de pantallas
DELIMITER //
CREATE TRIGGER `tr_screens_after_insert` 
AFTER INSERT ON `screens`
FOR EACH ROW
BEGIN
    -- Aquí se podría agregar lógica para logging
    -- INSERT INTO audit_log (table_name, action, record_id, timestamp) 
    -- VALUES ('screens', 'INSERT', NEW.id, NOW());
END //
DELIMITER ;

-- Trigger: Log de eliminación de pantallas
DELIMITER //
CREATE TRIGGER `tr_screens_after_delete` 
AFTER DELETE ON `screens`
FOR EACH ROW
BEGIN
    -- Aquí se podría agregar lógica para logging
    -- INSERT INTO audit_log (table_name, action, record_id, timestamp) 
    -- VALUES ('screens', 'DELETE', OLD.id, NOW());
END //
DELIMITER ;

-- =====================================================
-- CONFIGURACIÓN FINAL
-- =====================================================

-- Restaurar configuración de caracteres
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Finalizar transacción
COMMIT;

-- =====================================================
-- INFORMACIÓN ADICIONAL
-- =====================================================

/*
ESTRUCTURA DE RELACIONES:
- users (1) ←→ (N) screens (creadas por usuarios)
- screens (1) ←→ (N) media (contenido de pantallas)

CAMPOS IMPORTANTES:
- users.username: Debe ser único
- users.email: Debe ser único y válido
- users.password: Hash bcrypt
- screens.domain: Debe ser único (usado en URLs)
- media.file_path: Puede ser ruta local o URL externa

CONSIDERACIONES DE SEGURIDAD:
- Todas las contraseñas deben estar hasheadas
- Los dominios deben validarse antes de insertar
- Los archivos subidos deben validarse por tipo MIME
- Implementar CASCADE DELETE para limpieza automática

OPTIMIZACIONES:
- Índices en campos de búsqueda frecuente
- Vistas para consultas complejas
- Procedimientos para tareas de mantenimiento
- Triggers para auditoría (opcional)
*/