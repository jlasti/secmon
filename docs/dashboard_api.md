# API Dokumentácia

Dostupné API volania pre správu dashboardov a widgetov
| Method | Endpoint |
|---|---|
| GET | `/api/dashboard/dashboards` |
| POST | `/api/dashboard/create` |
| PUT | `/api/dashboard/{id}` |
| DELETE | `/api/dashboard/{id}` |
| POST | `/api/dashboard/change-active` |
| POST | `/api/dashboard/create-widget` |
| DELETE | `/api/dashboard/delete-widget?widgetId={id}` |
| POST | `/api/dashboard/update-widget-layout` |
| GET | `/api/dashboard/filters` |



---

# Získanie Dashboardov

**Endpoint:** `GET /api/dashboard/dashboards`

## Popis
Získanie všetkých dashboardov pre aktuálneho používateľa.  
Ak neexistujú žiadne dashboardy, vytvorí sa predvolený.

## Príklad požiadavky

```http
GET /api/dashboard/dashboards
Content-Type: application/json
```

## Príklad odpovede

```json
[
  {
    "id": 1,
    "name": "Predvolený Dashboard",
    "active": true,
    "refresh_time": "5S"
  }
]
```

---

# Vytvorenie Dashboardu

**Endpoint:** `POST /api/dashboard/create`

## Popis
Vytvorenie nového dashboardu pre aktuálneho používateľa.

## Príklad požiadavky

```http
POST /api/dashboard/create
Content-Type: application/json
```

```json
{
  "name": "Nový Dashboard",
  "refresh_time": "10S"
}
```

## Príklad odpovede

```json
{
  "id": 2,
  "name": "Nový Dashboard",
  "active": true,
  "refresh_time": "10S"
}
```

---

# Aktualizácia Dashboardu

**Endpoint:** `PUT /api/dashboard/{id}`

## Popis
Aktualizácia existujúceho dashboardu.

## Príklad požiadavky

```http
PUT /api/dashboard/1
Content-Type: application/json
```

```json
{
  "name": "Aktualizovaný Dashboard",
  "refresh_time": "15S"
}
```

## Príklad odpovede

```json
{
  "id": 1,
  "name": "Aktualizovaný Dashboard",
  "active": true,
  "refresh_time": "15S"
}
```

---

# Vymazanie Dashboardu

**Endpoint:** `DELETE /api/dashboard/{id}`

## Popis
Vymazanie existujúceho dashboardu.

## Príklad požiadavky

```http
DELETE /api/dashboard/1
Content-Type: application/json
```

## Príklad odpovede

```http
204 No Content
```

---

# Zmena Aktívneho Dashboardu

**Endpoint:** `POST /api/dashboard/change-active`

## Popis
Zmena aktívneho dashboardu pre aktuálneho používateľa.

## Príklad požiadavky

```http
POST /api/dashboard/change-active
Content-Type: application/json
```

```json
{
  "newDashboardId": 2
}
```

## Príklad odpovede

```json
[
  {
    "id": 13,
    "title": "New Widget",
    "chart_type": "barChart",
    "timeframe": "1M",
    "config": "{\"bar_chart_variable\":\"type\"}",
    "dashboard_id": 3,
    "filter_id": null,
    "layout": {
      "id": 13,
      "widget_id": 13,
      "x": 8,
      "y": 4,
      "w": 4,
      "h": 4
    }
  },
  {
    "id": 11,
    "title": "New Widget",
    "chart_type": "pieChart",
    "timeframe": "1M",
    "config": "{\"pie_chart_variable\":\"cef_version\",\"show_labels\":true}",
    "dashboard_id": 3,
    "filter_id": 41,
    "layout": {
      "id": 11,
      "widget_id": 11,
      "x": 0,
      "y": 0,
      "w": 4,
      "h": 4
    }
  }
]
```

---

# Vytvorenie Widgetu

**Endpoint:** `POST /api/dashboard/create-widget`

## Popis
Vytvorenie nového widgetu pre konkrétny dashboard.

## Príklad požiadavky

```http
POST /api/dashboard/create-widget
Content-Type: application/json
```

```json
{
  "dashboard_id": 2,
  "title": "Nový Widget",
  "chart_type": null
}
```

## Príklad odpovede

```json
{
  "widget": {
    "id": 1,
    "dashboard_id": 2,
    "title": "New Widget",
    "chart_type": null,
    "config": null
  }
}
```

---

# Vymazanie Widgetu

**Endpoint:** `DELETE /api/dashboard/delete-widget`

## Popis
Vymazanie widgetu podľa jeho ID.

## Príklad požiadavky

```http
DELETE /api/dashboard/delete-widget?widgetId=1
Content-Type: application/json
```

## Príklad odpovede

```http
204 No Content
```

---

# Aktualizácia Rozloženia Widgetov

**Endpoint:** `POST /api/dashboard/update-widget-layout`

## Popis
Aktualizácia rozloženia widgetov pre konkrétny dashboard.

## Príklad požiadavky

```http
POST /api/dashboard/update-widget-layout
Content-Type: application/json
```

```json
{
  "dashboard_id": 2,
  "widgetsPositionalInformation": [
    {
      "widget_id": 1,
      "x": 0,
      "y": 0,
      "w": 3,
      "h": 4
    }
  ]
}
```

## Príklad odpovede

```json
{
  "success": true,
  "message": "Widget layouts updated successfully",
  "count": 1
}
```

---

# Získanie Filtrov

**Endpoint:** `GET /api/dashboard/filters`

## Popis
Získanie všetkých filtrov pre aktuálneho používateľa.

## Príklad požiadavky

```http
GET /api/dashboard/filters
Content-Type: application/json
```

## Príklad odpovede

```json
[
  {
    "id": 50,
    "name": "Názov Filtra",
    "time_filter": true
  },
  {
    "id": 51,
    "name": "Dalsi filter",
    "time_filter": false
  }
]
```