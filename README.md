# Sistema de Recomendación de Restaurantes (ProyectoAi)

Este proyecto es una aplicación web desarrollada en PHP y MySQL que permite explorar una base de datos de restaurantes, ver detalles y fotos, y (en desarrollo) recibir recomendaciones personalizadas mediante un motor de IA en Python.

## 🚀 Estado Actual del Proyecto

### Funcionalidades Implementadas
- **Gestión de Usuarios:**
  - Registro e inicio de sesión (`login.php`, `register.php`).
  - Sistema de autenticación básico.
- **Catálogo de Restaurantes (`index.php`):**
  - Listado paginado de restaurantes.
  - Buscador por nombre.
  - Visualización optimizada: Muestra una única foto representativa por restaurante en el listado.
- **Detalle de Restaurante (`restaurant.php`):**
  - Información completa del negocio.
  - **Galería de Fotos:** Visualización de todas las imágenes disponibles para el restaurante seleccionado.
- **Backend & Datos:**
  - Conexión robusta a base de datos MySQL.
  - Scripts de Python para procesamiento de datos y lógica de recomendaciones (`server.py`, `getData.py`).
  - Integración de datos desde fuentes PDF (scripts de procesamiento incluidos pero datos crudos ignorados).

### Estructura del Proyecto
- `assets/`: Recursos estáticos (CSS, JS, Imágenes).
  - *Nota: Las imágenes masivas de restaurantes no se incluyen en el repositorio.*
- `includes/`: Lógica compartida (conexión DB, autenticación).
- `python/`: Motor de recomendaciones y procesamiento de datos.
- `templates/`: Componentes de UI reutilizables (header, footer).

## 🛠️ Requisitos e Instalación

1. **Entorno Web:** Servidor Apache y PHP (ej. XAMPP, WAMP).
2. **Base de Datos:** MySQL.
   - Importar el esquema inicial (ej. `setup_db.sql`).
3. **Python:** Python 3.x para los scripts de recomendación.
   - Librerías necesarias: `pandas`, `numpy`, `scikit-learn` (según `getData.py`).

## 📋 Próximos Pasos (Roadmap)

1. **Integración del Motor de Recomendaciones:**
   - Conectar completamente el frontend PHP con el servicio de Python (`server.py`).
   - Mostrar recomendaciones personalizadas en el perfil del usuario.
2. **Mejoras de UI/UX:**
   - Refinar el diseño con CSS moderno.
   - Mejorar la navegación de la galería de fotos.
3. **Interacción del Usuario:**
   - Sistema de valoraciones y comentarios.
   - Lista de favoritos.

## 📝 Notas sobre el Repositorio
- Las carpetas con grandes volúmenes de imágenes (`assets/img/phil-photos/`) y archivos temporales (`read_pdf.py`, `pdf_content.txt`) están excluidas mediante `.gitignore` para mantener el repositorio ligero.
