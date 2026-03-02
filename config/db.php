<?php
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/env.php");

// DATABASE CONFIGURATION
$host     = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname   = $_ENV['DB_NAME'];

// CREATE CONNECTION (NO DB YET)
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// CREATE DATABASE IF NOT EXISTS
$sql = "CREATE DATABASE IF NOT EXISTS `$dbname`
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci";

if (!$conn->query($sql)) {
    die("Failed to create database: " . $conn->error);
}

//SELECT DATABASE
$conn->select_db($dbname);

//SET UTF8MB4
$conn->query("SET NAMES utf8mb4");

//REATE TABLES

/* USERS */
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30),
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    role ENUM('User','Admin') DEFAULT 'User',
    status ENUM('Active','Pending','Suspended') DEFAULT 'Active',
    is_deleted tinyint(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* ADMINS */
$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    role ENUM('Super','Regular') DEFAULT 'Regular',
    status ENUM('Active','Suspended') DEFAULT 'Active',
    is_deleted tinyint(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* PRODUCTS */
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    quantity INT DEFAULT 0,
    is_best_seller tinyint(1) DEFAULT 0,
    status ENUM('In Stock','Out of Stock') DEFAULT 'In Stock',
    is_deleted tinyint(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* ORDERS */
$conn->query("CREATE TABLE IF NOT EXISTS orders
 (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    order_number VARCHAR(50) UNIQUE NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_ref VARCHAR(100) UNIQUE NULL,
    delivery_address TEXT NOT NULL,
    status ENUM('Pending','Paid','Delivered','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* ORDER ITEMS */
$conn->query("CREATE TABLE IF NOT EXISTS order_items
(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    item_subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* ENQUIRIES */
$conn->query("CREATE TABLE IF NOT EXISTS enquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enquiry_code VARCHAR(30) UNIQUE,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    message TEXT NOT NULL,
    status ENUM('New','Read','Replied') DEFAULT 'New',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* SETTINGS */
$conn->query("CREATE TABLE IF NOT EXISTS settings(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(150),
    contact_email VARCHAR(150),
    contact_phone VARCHAR(50),
    welcome_text TEXT,
    logo VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

