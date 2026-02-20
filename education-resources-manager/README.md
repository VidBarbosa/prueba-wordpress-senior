# Education Resources Manager

Plugin de WordPress para gestionar recursos educativos con panel administrativo, filtros frontend y endpoints REST.

## Requisitos
- WordPress 6.0+
- PHP 7.4+

## Instalación
1. Copia la carpeta `education-resources-manager` en `wp-content/plugins/`.
2. Activa el plugin desde el panel de WordPress.
3. Crea recursos desde **Recursos Educativos**.

## Uso
- Shortcode frontend: `[recursos_educativos]`
- Panel administrativo: **Education Resources** en el menú principal.
- Colección Postman: `docs/postman/ERM.postman_collection.json`.

## Funcionalidades principales
- CPT `erm_resource` con metadatos custom.
- Taxonomías: categorías de recursos y etiquetas de habilidades.
- REST API personalizada (`/wp-json/erm/v1`).
- Tabla personalizada para tracking de visualizaciones/descargas.
- Filtros dinámicos por AJAX en frontend.
