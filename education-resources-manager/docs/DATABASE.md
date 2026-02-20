# Base de Datos

## Tabla personalizada
Nombre: `{wp_prefix}erm_resource_tracking`

Campos:
- `id` BIGINT UNSIGNED PK AI
- `resource_id` BIGINT UNSIGNED (ID de `wp_posts`)
- `user_id` BIGINT UNSIGNED (ID de `wp_users`, opcional)
- `action_date` DATETIME
- `action_type` VARCHAR(20) (`view` o `download`)
- `ip_address` VARCHAR(45)

## Índices
- PK (`id`)
- KEY (`resource_id`)
- KEY (`user_id`)
- KEY (`action_type`)
- KEY (`action_date`)

## Relaciones
- `resource_id` -> `wp_posts.ID` (post type `erm_resource`)
- `user_id` -> `wp_users.ID`

## Queries optimizadas principales
- Top recursos visualizados:
  - `GROUP BY resource_id` + índice por `action_type`.
- Tendencia mensual de creación:
  - agregación sobre `wp_posts.post_date` filtrando por `post_type`.
