-- ================================
-- BASE DE DATOS
-- ================================
CREATE DATABASE IF NOT EXISTS catink CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE catink;

-- ================================
-- USUARIOS (ADMIN / AUTORES)
-- ================================
CREATE TABLE usuarios (
  id_u INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255),
  usuario VARCHAR(255),
  correo VARCHAR(255),
  pass VARCHAR(255),
  registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- CATEGORIAS
-- ================================
CREATE TABLE categorias (
  id_c INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) UNIQUE NOT NULL
);

-- ================================
-- NOTICIAS
-- ================================
CREATE TABLE noticias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT NOT NULL,
  autor INT NOT NULL,
  crop1 VARCHAR(255),
  crop2 VARCHAR(255),
  crop3 VARCHAR(255),
  contenido LONGTEXT NOT NULL,
  fecha_publicacion DATETIME NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  vistas INT DEFAULT 0,
  likes INT DEFAULT 0,
  FOREIGN KEY (autor) REFERENCES usuarios(id_u)
);

-- ================================
-- RELACION NOTICIAS - CATEGORIAS
-- ================================
CREATE TABLE noticia_categoria (
  noticia_id INT NOT NULL,
  categoria_id INT NOT NULL,
  PRIMARY KEY (noticia_id, categoria_id),
  FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id_c) ON DELETE CASCADE
);

-- ================================
-- LIKES DE NOTICIAS
-- ================================
CREATE TABLE noticia_likes (
  id_l INT AUTO_INCREMENT PRIMARY KEY,
  noticia_id INT NOT NULL,
  ip VARCHAR(45),
  pais VARCHAR(255),
  estado VARCHAR(255),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_like (noticia_id, ip),
  FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
);

-- ================================
-- ANALYTICS DE NOTICIAS (TIEMPO DE LECTURA)
-- ================================
CREATE TABLE noticias_stats (
  id_s INT AUTO_INCREMENT PRIMARY KEY,
  noticia_id INT NOT NULL,
  tiempo_segundos INT DEFAULT 0,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
);

-- ================================
-- PUBLICIDAD PRINCIPAL
-- ================================
CREATE TABLE publicidad (
  id_pub INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  url VARCHAR(255) NOT NULL,
  activo TINYINT(1) DEFAULT 1,
  fecha_inicio DATETIME,
  fecha_fin DATETIME,
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- RELACION PUBLICIDAD - CATEGORIAS
-- ================================
CREATE TABLE publicidad_categoria (
  publicidad_id INT NOT NULL,
  categoria_id INT NOT NULL,
  PRIMARY KEY (publicidad_id, categoria_id),
  FOREIGN KEY (publicidad_id) REFERENCES publicidad(id_pub) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id_c) ON DELETE CASCADE
);

-- ================================
-- CLICKS DE PUBLICIDAD
-- ================================
CREATE TABLE publicidad_clicks (
  id_click INT AUTO_INCREMENT PRIMARY KEY,
  publicidad_id INT NOT NULL,
  ip VARCHAR(45),
  pais VARCHAR(255),
  estado VARCHAR(255),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (publicidad_id) REFERENCES publicidad(id_pub) ON DELETE CASCADE
);

-- ================================
-- IMPRESIONES / TIEMPO DE VISUALIZACION
-- ================================
CREATE TABLE publicidad_views (
  id_view INT AUTO_INCREMENT PRIMARY KEY,
  publicidad_id INT NOT NULL,
  ip VARCHAR(45),
  tiempo_segundos INT DEFAULT 0,
  pais VARCHAR(255),
  estado VARCHAR(255),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (publicidad_id) REFERENCES publicidad(id_pub) ON DELETE CASCADE
);

-- ================================
-- SUSCRIPCIONES NEWSLETTER
-- ================================
CREATE TABLE suscripciones (
  id_sub INT AUTO_INCREMENT PRIMARY KEY,
  nombre_completo VARCHAR(255) NOT NULL,
  correo VARCHAR(255) UNIQUE NOT NULL,
  sexo VARCHAR(50),
  ip VARCHAR(45),
  pais VARCHAR(255),
  estado VARCHAR(255),
  activo TINYINT(1) DEFAULT 1,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- INDEXES PARA RENDIMIENTO
-- ================================
CREATE INDEX idx_noticia_categoria ON noticia_categoria(noticia_id);
CREATE INDEX idx_publicidad_categoria ON publicidad_categoria(publicidad_id);
CREATE INDEX idx_clicks_pub ON publicidad_clicks(publicidad_id);
CREATE INDEX idx_views_pub ON publicidad_views(publicidad_id);
CREATE INDEX idx_stats_noticia ON noticias_stats(noticia_id);