# Plan de Proyecto: Aplicación de Recomendación de Restaurantes

Este documento detalla el plan de implementación para el proyecto de desarrollo web y análisis de datos basado en las especificaciones del PDF.

## 1. Configuración del Entorno y Estructura

### 1.1. Estructura de Directorios (Separación Frontend/Backend)
Para mantener el código organizado, usaremos la siguiente estructura:

*   **`/assets`**: (Frontend) Archivos estáticos públicos.
    *   `/css`: Hojas de estilo (`style.css`).
    *   `/js`: Scripts del lado del cliente (`main.js`, mapas).
    *   `/img`: Imágenes de la interfaz.
*   **`/templates`**: (Frontend) Fragmentos de HTML reutilizables.
    *   `header.php`: Cabecera común.
    *   `footer.php`: Pie de página común.
*   **`/includes`**: (Backend PHP) Lógica de negocio y configuración.
    *   `db_connect.php`: Conexión a BBDD.
    *   `functions.php`: Funciones auxiliares (helpers).
    *   `auth.php`: Lógica de sesión y registro.
*   **`/python`**: (Backend Python) Motor de recomendaciones.
    *   `server.py`, `recommender.py`, `getData.py`.
*   **Raíz**: Controladores principales (Páginas).
    *   `index.php`, `login.php`, `restaurant.php`.

### 1.2. Estructura de la Base de Datos (Existente + Modificaciones)
Basado en el esquema proporcionado, trabajaremos con las siguientes tablas:

**Tablas Existentes:**
- **`user`**: `id` (PK), `name`, `review_count`, `yelping_since`, `average_stars`.
  - *Nota: Necesitaremos añadir un campo `password` para el login.*
- **`business`**: `id` (PK), `name`, `city`, `state`, `latitude`, `longitude`, `stars`, `review_count`, `categories`, `is_open`.
- **`review`**: `id` (PK), `user_id` (FK), `business_id` (FK), `stars`, `date`.
- **`photo`**: `id` (PK), `business_id` (FK), `caption`, `label`.
- **`comments`**: `business_id`, `user_id`, `comment`.

**Tablas a Crear:**
- **`recs`**: `user_id` (FK), `business_id` (FK), `rec_score`, `time`. (Requerida por el PDF para guardar recomendaciones).

### 1.2. Configuración de Conexión
- Crear `db_connect.php`: Script PHP centralizado para la conexión a la BBDD.
- Actualizar `getData.py` y `updateRecommendations.py` con las credenciales correctas de la BBDD local.

## 2. Backend (Python) - Motor de Recomendación

### 2.1. Servidor TCP
- Verificar y configurar `server.py` para escuchar en el puerto 4450.
- Asegurar que puede cargar dinámicamente los módulos de Python.

### 2.2. Algoritmo de Filtrado Colaborativo
- Crear `recommender.py`:
    - Implementar la función `recomendar(user_id)`.
    - Usar `getData.py` para obtener las matrices $Y$ (calificaciones) y $R$ (indicador).
    - Implementar el algoritmo de aprendizaje (Cost Function & Gradient Descent o librería existente) para predecir calificaciones faltantes.
    - Filtrar las predicciones para el `user_id` dado.
    - Usar `updateRecommendations.py` para guardar los resultados en la tabla `recs`.

## 3. Frontend y Backend (PHP) - Aplicación Web

### 3.1. Estructura Común
- `header.php`: Navegación, inicio de sesión/logout, enlaces comunes.
- `footer.php`: Información de pie de página.
- `style.css`: Estilos generales para la aplicación.

### 3.2. Gestión de Usuarios
- `login.php`: Formulario de inicio de sesión.
- `register.php`: Formulario de registro de nuevos usuarios.
- `logout.php`: Cerrar sesión.

### 3.3. Catálogo de Restaurantes (`index.php`)
- Listado de restaurantes paginado.
- Implementación del **Ranking Bayesiano** para ordenación:
  $$pp_i = \frac{NR + n_i r_i}{N + n_i}$$
- Filtros básicos (opcional).

### 3.4. Detalle del Restaurante (`restaurant.php`)
- Mostrar información detallada (foto, mapa, horario).
- Listar comentarios existentes.
- Formulario para añadir puntuación y comentario (solo usuarios logueados).

### 3.5. Gestión de Negocios (`add_restaurant.php`)
- Formulario para registrar un nuevo restaurante (nombre, ubicación, categorías, foto).

### 3.6. Perfil y Recomendaciones (`profile.php`)
- Mostrar datos del usuario.
- Botón "Generar Recomendaciones":
    - Llama a un script PHP interno (`trigger_recommendation.php`).
    - Este script se conecta al socket Python (puerto 4450) y ejecuta `recommender.recomendar(user_id)`.
- Mostrar lista de restaurantes recomendados (ordenados por `rec_score` de la tabla `recs`).

## 4. Funcionalidad Opcional (Extensiones)

- **Mapas**: Integrar Leaflet.js en `restaurant.php` para mostrar la ubicación basada en lat/long.
- **Restaurantes Similares**: Añadir botón en `restaurant.php` que busque restaurantes con perfiles de votación similares (correlación de Pearson o distancia coseno).

## 5. Pasos de Ejecución Inmediata

1.  **Crear esquema SQL** y poblar con datos de prueba (o importar dataset si está disponible).
2.  **Configurar `server.py`** y probar conexión simple desde PHP.
3.  **Implementar `recommender.py`** (versión básica).
4.  **Desarrollar estructura PHP** (Login -> Catálogo -> Detalle).
5.  **Integrar** la llamada a Python desde el perfil de usuario.
