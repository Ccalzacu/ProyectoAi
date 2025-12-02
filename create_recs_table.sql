-- Script SQL para crear la tabla de recomendaciones
-- Ejecutar este script en tu base de datos aiXX

CREATE TABLE IF NOT EXISTS recs (
    user_id INT(11) NOT NULL,
    business_id INT(11) NOT NULL,
    rec_score FLOAT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, business_id),
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES business(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear índices para mejorar el rendimiento de las consultas
CREATE INDEX idx_user_score ON recs(user_id, rec_score DESC);
CREATE INDEX idx_business_score ON recs(business_id, rec_score DESC);
