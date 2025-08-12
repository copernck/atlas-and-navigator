<?php
// function to establish database connection (pdo mysql)

function getDbConnection() {
    static $db = null; // hold the connection

    if ($db === null) {
        // connection string (dsn) for mysql
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        // pdo options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
        ];

        try {
            // use details from config.php
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // don't show detailed errors in production! log them.
            error_log("database connection error: " . $e->getMessage());
            // generic error for user - maybe show a nicer error page later
            die("database connection failed. check config.php and dreamhost panel.");
        }
    }
    return $db;
}

/*
sql command to create the initial 'posts' table in your mysql database (run via phpmyadmin sql tab):

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL, -- 'finance' or 'travel'
    content TEXT NOT NULL,
    excerpt TEXT, -- short summary for list views
    image_url VARCHAR(255), -- optional featured image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Add indexes for faster lookups later if needed
-- CREATE INDEX idx_slug ON posts (slug);
-- CREATE INDEX idx_category ON posts (category);

*/
?>
