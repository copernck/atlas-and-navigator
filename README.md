Atlas & Navigator - A Full-Stack Blog Platform

https://atlasandnavigator.site/

**Atlas & Navigator** is a complete, database-driven blog and content platform built from the ground up with PHP and MySQL Along with html css JavaScript. This project was an exercise in creating a dynamic website without relying on a traditional CMS like WordPress, focusing on clean code, security, and a custom-built administrative backend.

---

### Core Features

This isn't just a template. It's a fully functional web application with a custom-coded backend.
it does what blog supposed to do anyways it has:

* **Custom Admin Panel:** A secure, password-protected dashboard (`/admin`) for creating, editing, and managing all blog posts. Features a rich text editor (TinyMCE) for a smooth writing experience.
* **Dynamic Routing:** Implemented a clean URL structure using a front-controller (`index.php`) and `.htaccess` rules. This turns ugly URLs like `?page=blog&id=123` into clean, SEO-friendly paths like `/blog/my-awesome-post`.
* **Database-Driven Content:** All posts are stored and retrieved from a MySQL database using secure, prepared PDO statements to prevent SQL injection.
* **Secure Authentication:** The admin panel is protected by a session-based login system with hashed passwords.
* **Categorization & Pagination:** The blog supports post categories and includes logic for paginating through articles.
* **Modern Front-End:** The user-facing site is styled with Tailwind CSS for a clean, responsive design.

---

### 🛠️ Tech Stack

* **Backend:** PHP
* **Database:** MySQL
* **Frontend:** HTML, Tailwind CSS, Vanilla JavaScript
* **Server:** Apache (using `.htaccess` for routing)

---

### 🚀 Local Setup & Installation

To run this project locally, you'll need a server environment like XAMPP, MAMP, or Docker with PHP and MySQL.

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/copernck/atlas-and-navigator
    cd atlas-and-navigator
    ```

2.  **Database Setup:**
    * Create a new MySQL database.
    * Import the database structure. You can use the `CREATE TABLE` statements found in the `/includes/database.php` file to set up the `posts` and `admins` tables.
    * **(Important)** You will need to manually create an admin user in the `admins` table. Use a tool like PHP's `password_hash()` to generate a secure password hash.

3.  **Configuration:**
    * Rename `config-sample.php` to `config.php` (or create it from scratch).
    * Open `config.php` and fill in your local database credentials (host, database name, user, and password).
    * **NEVER commit your actual `config.php` file with real passwords to a public repository.**

4.  **Run the Server:**
    * Point your local server's document root to the project directory.
    * Navigate to the site in your browser (e.g., `http://localhost/atlas-and-navigator`).
    * Access the admin panel by navigating to `/login.php`.

for your own vps or using service like godaddy you can just get the vps and upload the folder
