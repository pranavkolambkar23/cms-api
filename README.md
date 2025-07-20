# 📝 CMS API

A Content Management System (CMS) built with Laravel that allows users to manage **articles**, **categories**, and perform **filtered listings** using a clean RESTful API architecture. Includes background job handling and custom filters like category, publication date, and status.

---

## 🚀 Features

-   CRUD for Articles and Categories
-   Filter Articles by:
    -   Status (`published`, `draft`, `archived`)
    -   Category
    -   Publish Date (`published_from`, `published_to`)
-   Background Job Dispatching using Laravel Queues
-   Laravel Resource Transformers
-   API Routing via `routes/api.php`

---

## 📦 Requirements

-   PHP >= 8.1
-   Composer
-   Laravel >= 10.x
-   MySQL

---

## ⚙️ Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/pranavkolambkar23/cms-api.git
cd cms-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env

```

Edit your `.env` file and set the correct database credentials.

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Queue Configuration (for background jobs)

```bash
php artisan queue:work
```

> Keep this command running in the background to process queued jobs like slug and summary generation.

### 6. Serve the Application

```bash
php artisan db:seed
```

Run this command to create sample users admin and author

### 7. Serve the Application

```bash
php artisan serve
```

The app will be accessible at `http://127.0.0.1:8000`.

---

## 📬 Postman Collection

You can test the API using Postman. Download or import the following collection:

👉 [CMS API Postman Collection](postman/cms-api.postman_collection.json)

## 🧪 API Usage

Base URL: `http://127.0.0.1:8000/api`

### Article Filters

-   `GET /articles?status=published`
-   `GET /articles?category_id=3`
-   `GET /articles?published_from=2024-01-01&published_to=2024-12-31`

### Create Article

```json
POST /api/articles

{
  "title": "My First Article",
  "body": "This is the content of the article.",
  "status": "published",
  "category_ids": [1, 2]
}
```

---

## 🧰 Technologies Used

-   Laravel 10.x
-   PHP 8.1+
-   MySQL
-   Laravel Queue (Sync / Database driver)
-   Laravel Eloquent & Resource Transformers

---

## 👨‍💻 Author

**Pranav Kolambkar**  
GitHub: [@pranavkolambkar23](https://github.com/pranavkolambkar23)
