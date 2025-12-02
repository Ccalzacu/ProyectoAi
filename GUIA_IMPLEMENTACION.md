# Guía Rápida: Cómo Implementar el Proyecto

## ✅ Lo que ya tienes listo

1. ✅ **Base de datos**: Esquema completo con datos de Yelp
2. ✅ **`server.py`**: Servidor Python funcionando en puerto 4450
3. ✅ **`getData.py`**: Carga datos de BD y crea matrices Y y R
4. ✅ **`updateRecommendations.py`**: Guarda recomendaciones en BD

## 🚀 Pasos para completar el proyecto

### Paso 1: Crear tabla `recs` en la BD

Ejecuta el archivo [`create_recs_table.sql`](file:///c:/Users/ccalz/OneDrive/Documentos/GitHub/ProyectoAi/create_recs_table.sql):

```bash
mysql -u aiXX -p aiXX < create_recs_table.sql
```

### Paso 2: Implementar el algoritmo de recomendación

Crear archivo `recommender.py` con:

**Componentes necesarios**:
- Clase `MatrixFactorization` con gradient descent
- Función `train_model()` que entrena y guarda el modelo
- Función `recomendar(user_id)` que:
  1. Carga datos con `getData.get_data()`
  2. Carga o entrena el modelo
  3. Genera predicciones para el usuario
  4. Guarda en BD con `updateRecommendations.update_recommendation()`
  5. Retorna número de recomendaciones generadas

**Algoritmo Matrix Factorization**:
```
Y ≈ X @ Theta.T

Donde:
- Y: matriz de ratings (num_businesses × num_users)
- X: features de negocios (num_businesses × num_features)
- Theta: preferencias de usuarios (num_users × num_features)

Función de costo:
J = (1/2) Σ[(Y - X@Theta.T)² donde R=1] + (λ/2)(||X||² + ||Theta||²)
```

### Paso 3: Desarrollar scripts PHP

**Orden recomendado**:

1. **`db_connect.php`** - Conexión a BD reutilizable
2. **`login.php`** / **`logout.php`** - Sistema de sesiones
3. **`index.php`** - Listado paginado de restaurantes
4. **`search.php`** - Búsqueda por categoría/ciudad
5. **`restaurant_details.php`** - Detalles y reseñas
6. **`submit_review.php`** - Procesar nueva reseña
7. **`recommendations.php`** - Mostrar recomendaciones (llamar a Python)
8. **`conectar.php`** (modificar) - Comunicación con server.py

### Paso 4: Crear estilos CSS

- Archivo `style.css` con diseño profesional
- Layout responsive
- Formularios estilizados

### Paso 5: Elegir funcionalidad opcional (2.5 pts)

**Opciones**:
- 🗺️ Mapa interactivo con Leaflet
- 👥 "A otros usuarios les gusta"
- 🔄 "Restaurantes similares"
- 📚 Usar librería Surprise/RecBole

### Paso 6: Testing

- Probar cada funcionalidad
- Verificar autenticación
- Validar recomendaciones

## 📝 Actualizar credenciales

Antes de empezar, actualiza en **ambos archivos**:

**`getData.py`** (líneas 12-16):
```python
DB_NAME = 'ai04'  # ← TU nombre de BD
USER = 'ai04'     # ← TU usuario
PASSWORD = 'ai04_password'  # ← TU contraseña
```

**`updateRecommendations.py`** (líneas 22-26):
```python
DB_NAME = 'ai04'  # ← Mismo nombre
USER = 'ai04'
PASSWORD = 'ai04_password'
```

## 🎯 Prioridad de tareas

**CRÍTICO (hacer primero)**:
1. Crear tabla `recs`
2. Actualizar credenciales de BD
3. Implementar `recommender.py`

**IMPORTANTE (hacer después)**:
4. Scripts PHP básicos (index, details, search)
5. Sistema de login
6. Envío de reseñas
7. Mostrar recomendaciones

**OPCIONAL (para nota extra)**:
8. Funcionalidad adicional
9. Mejorar estilos CSS
10. Optimizaciones

## 💡 Consejos

- Prueba `getData.py` primero para verificar conexión a BD
- Entrena el modelo con pocos features primero (num_features=3)
- Guarda el modelo entrenado para reutilizarlo
- Usa sesiones PHP para mantener usuario logueado
- Valida TODOS los inputs del usuario en PHP

¿Por cuál parte quieres que empecemos a implementar?
