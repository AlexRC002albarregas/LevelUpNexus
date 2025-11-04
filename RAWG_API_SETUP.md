# Configuración de RAWG API

## Cómo obtener tu API Key de RAWG

1. **Visita la página de RAWG**: https://rawg.io/apidocs

2. **Crea una cuenta o inicia sesión**

3. **Obtén tu API Key**:
   - Ve a https://rawg.io/apidocs
   - Haz click en "Get API Key"
   - Copia tu API Key

4. **Añade la clave a tu archivo `.env`**:
   ```
   RAWG_API_KEY=tu_api_key_aqui
   ```

5. **Limpia la caché de configuración**:
   ```bash
   php artisan config:clear
   ```

## Límites de la API (Free Tier)

- **20,000 peticiones por mes**
- **5 peticiones por segundo**
- Acceso a toda la base de datos de juegos
- Búsqueda, filtros, y detalles completos

## Funcionalidades implementadas

✅ Búsqueda de juegos por nombre
✅ Obtener juegos populares
✅ Añadir/eliminar juegos favoritos
✅ Cache automático de resultados (1 hora búsquedas, 24 horas detalles)
✅ Vista previa de favoritos con imágenes
✅ Modal interactivo para seleccionar favoritos

## Uso en la aplicación

1. Ve a `/account/edit`
2. Click en el campo "Juegos favoritos"
3. Busca tus juegos preferidos
4. Click en cualquier juego para añadirlo/quitarlo de favoritos
5. Los cambios se guardan automáticamente

## API Endpoints creados

- `GET /api/games/search?q={query}` - Buscar juegos
- `GET /api/games/popular` - Juegos populares
- `POST /api/favorites/add` - Añadir favorito
- `POST /api/favorites/remove` - Quitar favorito
- `GET /api/favorites` - Obtener favoritos del usuario

