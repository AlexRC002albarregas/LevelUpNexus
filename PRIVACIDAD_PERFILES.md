# Sistema de Privacidad de Perfiles - LevelUp Nexus

## 📋 Descripción

Los usuarios pueden configurar su perfil como **Público** o **Privado** desde la página de edición de cuenta.

## 🔐 Comportamiento

### Perfiles Públicos (por defecto)
- ✅ Cualquier usuario autenticado puede ver su información
- ✅ Cualquier usuario puede ver sus publicaciones en el feed
- ✅ Sus publicaciones aparecen en el feed general

### Perfiles Privados
- 🔒 Solo los **amigos** pueden ver:
  - Información del perfil (biografía, juegos favoritos)
  - Publicaciones del usuario
  - Estadísticas completas
- 🚫 Los usuarios que **NO son amigos** verán:
  - Un mensaje indicando que el perfil es privado
  - No pueden ver publicaciones ni información personal
- 👤 El **propio usuario** siempre puede ver su perfil completo
- 👑 Los **administradores** pueden ver todos los perfiles

## 🎮 Cómo Activar/Desactivar

1. Ve a tu **Perfil** (click en tu nombre/avatar en la navbar)
2. Click en **"Editar perfil"**
3. Busca la sección **"Privacidad del Perfil"**
4. Activa/desactiva el switch:
   - 🟢 **Público**: Todos pueden ver tu perfil
   - 🔴 **Privado**: Solo amigos pueden ver tu perfil

## 🛠️ Implementación Técnica

### Base de Datos
- Campo `is_private` en tabla `users` (boolean, default: false)
- Migración: `2025_10_30_105852_add_is_private_to_users_table.php`

### Modelo
**`app/Models/User.php`**
- Método `canViewProfile(User $targetUser)`: Verifica si el usuario puede ver el perfil objetivo
- Método `isFriendWith(User $user)`: Verifica si dos usuarios son amigos

### Rutas
**`routes/web.php`**
- `/users/{user}`: Muestra el perfil (aplica lógica de privacidad)
- `/posts`: Feed filtrado por privacidad
- `/posts/{post}`: Verifica acceso antes de mostrar
- `/account/update`: Guarda configuración de privacidad

### Vistas
- **`resources/views/account/edit.blade.php`**: Switch de privacidad
- **`resources/views/users/show.blade.php`**: Perfil con validación de acceso
- **`resources/views/friends/index.blade.php`**: Enlace "Ver perfil" en amigos

## 📊 Lógica de Visibilidad en Feed

El feed de publicaciones (`/posts`) filtra automáticamente:
1. ✅ Todas las publicaciones de perfiles públicos
2. ✅ Publicaciones de tus amigos (aunque tengan perfil privado)
3. ✅ Tus propias publicaciones
4. ❌ Publicaciones de usuarios privados que NO son tus amigos

## 🔄 Actualización

Para aplicar los cambios en la base de datos:

```bash
php artisan migrate
```

## 🧪 Pruebas Sugeridas

1. **Usuario A** (perfil público):
   - Usuario B puede ver su perfil y publicaciones ✅

2. **Usuario C** (perfil privado):
   - Usuario B (NO amigo) NO puede ver su perfil ❌
   - Usuario D (SÍ amigo) puede ver su perfil ✅

3. **Admin**:
   - Puede ver todos los perfiles (públicos y privados) ✅

## 💡 Notas Adicionales

- El estado de privacidad se indica con un badge **"Privado"** en el perfil
- El toggle tiene efecto visual interactivo (cambia texto Público/Privado)
- La privacidad NO afecta a mensajes directos entre amigos
- Los grupos siguen siendo visibles para todos los miembros

---

**Desarrollado para LevelUp Nexus** 🎮

