# Arquitectura del Plugin

## Diagrama de alto nivel

```text
Usuario (frontend) -> Shortcode + JS -> REST API (/erm/v1/resources)
                                              |
                                              v
                                WP_Query + Post Meta + Taxonomías
                                              |
                                              v
                                   Tabla custom tracking (wpdb)

Admin WP -> Panel Education Resources -> métricas y filtros
```

## Estructura de carpetas
- `includes/`: clases core (CPT, taxonomías, DB, admin, API, shortcode).
- `admin/`: vista y assets del panel personalizado.
- `public/`: assets frontend para filtros dinámicos.
- `docs/`: documentos de arquitectura, base de datos y API.
- `database/`: scripts SQL de estructura y datos de ejemplo.

## Flujo de datos
1. El shortcode renderiza filtros y contenedor.
2. JS llama a `/erm/v1/resources` con filtros y paginación.
3. El endpoint consulta `erm_resource` con `WP_Query`.
4. En clic de “Ver recurso”, JS invoca `/resources/{id}/track`.
5. Se inserta evento en la tabla `wp_erm_resource_tracking`.
6. El panel admin consume agregados para mostrar estadísticas.

## Decisiones técnicas
- Arquitectura orientada a clases para separar responsabilidades.
- Tabla custom para eventos por volumen/consulta analítica.
- REST API para desacoplar frontend y backend.
- Sanitización/validación en metabox, filtros y endpoints.
