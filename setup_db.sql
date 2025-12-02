-- Script para actualizar la estructura de la base de datos

-- 0. Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS proyecto_ai;
USE proyecto_ai;

-- 1. Crear la tabla de recomendaciones (recs)
CREATE TABLE IF NOT EXISTS recs (
    user_id INT(11) NOT NULL,
    business_id INT(11) NOT NULL,
    rec_score FLOAT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, business_id),
    CONSTRAINT fk_recs_user FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT fk_recs_business FOREIGN KEY (business_id) REFERENCES business(id) ON DELETE CASCADE
);

-- 2. Añadir columna password a la tabla user para el login
-- Se añade un valor por defecto temporal para los usuarios existentes
ALTER TABLE user ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; -- Password: 'password'
