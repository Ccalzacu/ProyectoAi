# Sistema de Recomendacion de Restaurantes (ProyectoAi)

Este proyecto es una aplicacion web desarrollada en PHP y MySQL que permite explorar una base de datos de restaurantes, ver detalles y fotos, y (en desarrollo) recibir recomendaciones personalizadas mediante un motor de IA en Python.

## Estado Actual del Proyecto

### Funcionalidades Implementadas

#### 1. Gestion de Usuarios y Autenticacion
Se ha implementado un sistema completo de registro e inicio de sesion (`login.php`, `register.php`). Los usuarios pueden crear cuentas y acceder al sistema, lo cual es fundamental para la futura funcionalidad de recomendaciones personalizadas. Las contraseñas se almacenan de forma segura utilizando hash.

#### 2. Catalogo de Restaurantes (`index.php`)
La pagina principal ofrece un listado paginado de todos los restaurantes disponibles en la base de datos.
- **Busqueda:** Incluye una barra de busqueda que permite filtrar restaurantes por nombre en tiempo real.
- **Optimizacion de Consultas:** Se ha implementado logica SQL avanzada (`LEFT JOIN` y `GROUP BY`) para asegurar que cada restaurante aparezca una sola vez en el listado, mostrando una unica imagen representativa, incluso si el restaurante tiene multiples fotos asociadas en la base de datos.
- **Paginacion:** Navegacion eficiente a traves de grandes volumenes de datos.

#### 3. Detalle de Restaurante (`restaurant.php`)
Al seleccionar un restaurante, el usuario accede a una vista detallada que muestra:
- Informacion completa del negocio (nombre, direccion, categoria, etc.).
- **Galeria de Fotos Completa:** A diferencia del listado principal, esta vista recupera y muestra todas las imagenes disponibles asociadas al restaurante, permitiendo al usuario ver el ambiente y los platos.

#### 4. Backend y Estructura de Datos
- **Base de Datos MySQL:** Esquema relacional con tablas para `users`, `business` y `photo`. La relacion entre negocios y fotos se maneja mediante claves foraneas, permitiendo multiples fotos por negocio.
- **Integracion Python:** Se han preparado los scripts base (`server.py`, `getData.py`) para el motor de recomendaciones. Estos scripts estan diseñados para procesar datos y servir predicciones al frontend PHP.

### Estructura del Proyecto
- `assets/`: Contiene hojas de estilo (CSS) y recursos de imagen.
- `includes/`: Archivos de configuracion y funciones reutilizables, como la conexion a la base de datos y la verificacion de sesiones.
- `python/`: Scripts de Python encargados de la logica de Machine Learning y procesamiento de datos.
- `templates/`: Fragmentos de codigo HTML (encabezado, pie de pagina) para mantener la consistencia visual en todas las paginas.

## Requisitos e Instalacion

1. **Entorno Web:** Servidor Apache y PHP (ej. XAMPP, WAMP).
2. **Base de Datos:** MySQL. Se debe importar el esquema inicial proporcionado en `setup_db.sql`.
3. **Python:** Python 3.x es necesario para ejecutar los scripts de recomendacion. Se requieren librerias como `pandas`, `numpy` y `scikit-learn`.

## Proximos Pasos

### 1. Integracion del Motor de Recomendaciones
El objetivo principal a corto plazo es conectar el frontend PHP con el backend de Python.
- **Comunicacion:** Establecer un mecanismo (API REST o ejecucion de scripts) para que PHP pueda solicitar recomendaciones a Python basadas en el ID del usuario.
- **Personalizacion:** Mostrar una seccion de "Recomendados para ti" en el perfil del usuario o en la pagina de inicio, basada en el historial o preferencias del usuario.

### 2. Mejoras en la Interfaz de Usuario (UI/UX)
- **Diseño Visual:** Modernizar la hoja de estilos `style.css` para ofrecer una experiencia mas atractiva y responsiva (mobile-friendly).
- **Navegacion:** Mejorar la visualizacion de la galeria de fotos (ej. implementar un carrusel o lightbox).
- **Filtros Avanzados:** Añadir opciones para filtrar por categoria, ciudad o rango de precios.

### 3. Interaccion y Comunidad
- **Sistema de Valoraciones:** Permitir a los usuarios calificar los restaurantes (1-5 estrellas).
- **Comentarios:** Implementar un sistema para que los usuarios dejen reseñas escritas.
- **Favoritos:** Funcionalidad para guardar restaurantes en una lista personal.
