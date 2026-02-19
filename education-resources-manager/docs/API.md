# Documentación API

## Base URL
`/wp-json/erm/v1`

## GET `/resources`
Lista recursos con filtros.

Parámetros:
- `page` (int)
- `per_page` (int, max 20)
- `search` (string)
- `type` (`curso|tutorial|ebook|video`)
- `level` (`principiante|intermedio|avanzado`)
- `category` (term_id)

Respuesta 200:
```json
{
  "items": [
    {
      "id": 12,
      "title": "Curso de PHP",
      "type": "curso",
      "level": "intermedio"
    }
  ],
  "total": 1,
  "total_pages": 1
}
```

## GET `/resources/{id}`
Devuelve un recurso específico.

Errores:
- `404 erm_not_found`

## POST `/resources/{id}/track`
Registra visualización/descarga.

Body:
```json
{ "action_type": "view" }
```

Respuesta 200:
```json
{ "success": true }
```

Errores:
- `400 erm_track_failed`

## GET `/stats`
Devuelve estadísticas para dashboard.

Permisos:
- Público para facilitar pruebas técnicas e integración con herramientas como Postman.

Respuesta 200:
```json
{
  "top_resources": [{ "resource_id": 12, "total": 20 }],
  "resources_by_month": [{ "ym": "2026-01", "total": 10 }]
}
```
