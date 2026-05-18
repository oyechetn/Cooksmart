# CookSmart

CookSmart is a recipe and meal management web application developed using PHP and MySQL. The project allows users to browse, upload, and manage recipes through a simple and responsive interface.

## Features

* User authentication system
* Recipe upload and management
* Search functionality
* Category-based recipes
* Admin panel
* Responsive interface
* Favorite recipes system

## Tech Stack

Frontend:

* HTML
* CSS
* JavaScript
* Bootstrap

Backend:

* PHP
* MySQL (mysqli)

Server:

* XAMPP / Apache

## Installation

1. Clone the repository

```bash
git clone https://github.com/oyechetn/cooksmart.git
```

2. Move the project folder to:

```bash
C:/xampp/htdocs/
```

3. Start Apache and MySQL from XAMPP.

4. Open phpMyAdmin and create a database named:

```sql
cooksmart
```

5. Import the SQL file provided in the project.

6. Configure database connection in:

```php
config/db.php
```

```php
<?php
$conn = new mysqli("localhost", "root", "", "cooksmart");
?>
```

7. Run the project:

```bash
http://localhost/cooksmart
```

## Default Admin Login

Email: [admin@gmail.com](mailto:admin@gmail.com)
Password: admin123

## Future Improvements

* Recipe ratings and comments
* AI recipe suggestions
* Meal planner
* Grocery list feature
* Mobile application support

## Developer

Hari Chetan Singh
