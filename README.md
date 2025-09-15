# WordPress Article API

## Overview
This project sets up a **WordPress backend** exposing a custom post type `article` via the **REST API**.  
It allows fetching articles, single posts by ID, and categories. This API can be consumed by frontend applications such as **Next.js** or **Flutter**.

---

## WordPress Setup (Hosted / Local)
- **WordPress URL:** `http://localhost/wordpress`  
- **Custom Post Type Plugin:** `article-cpt.php` located in `/wp-content/plugins/`  
- **Plugin Activation:** Admin Dashboard → Plugins → Activate `Article CPT`

The custom post type `article` includes:

| Field | Type |
|-------|------|
| Title | Text |
| Content | WYSIWYG editor |
| Featured Image | Media |
| Author | WordPress user |
| Category | WordPress category |

---

## REST API Endpoints

Base URL: `http://localhost/wordpress/index.php?rest_route=/wp/v2`

| Action | Endpoint | Description | Example |
|--------|----------|-------------|---------|
| Fetch all articles | `/article` | Returns all articles | `GET http://localhost/wordpress/index.php?rest_route=/wp/v2/article` |
| Fetch single article by ID | `/article/{id}` | Replace `{id}` with article ID | `GET http://localhost/wordpress/index.php?rest_route=/wp/v2/article/123` |
| Fetch all categories | `/categories` | Returns all categories | `GET http://localhost/wordpress/index.php?rest_route=/wp/v2/categories` |
| Filter articles by category | `/article?categories={category_id}` | Returns articles in a category | `GET http://localhost/wordpress/index.php?rest_route=/wp/v2/article&categories=5` |

### Optional Custom Plugin Endpoint
If using a custom plugin route:

| Action | Endpoint | Description |
|--------|----------|-------------|
| Ping test | `/myplugin/v1/ping` | Returns a simple message confirming the API works | `GET http://localhost/wordpress/index.php?rest_route=/myplugin/v1/ping` |

---

## How to Use the API

1. Open the URL in your browser or use tools like **Postman** or **curl**.  
2. Example curl request:

```bash
curl http://localhost/wordpress/index.php?rest_route=/wp/v2/article