# food-order-system
# 🍔 Online Food Ordering System

![Project Banner](assets/images/banner.png)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://php.net/)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

A complete, responsive online food ordering platform built with **PHP, MySQL, and AJAX**, featuring multi-role access (customers, restaurant admins, and super admin).

## 🌟 Features

### 👨‍🍳 Customer Features
- 🏪 Browse restaurant menus with filters
- 🔍 Search dishes by name/cuisine
- 🛒 AJAX-powered shopping cart
- 💳 Checkout with delivery tracking
- ⭐ Rate and review orders
- 📱 Fully responsive design

### 🍕 Restaurant Admin Features
- 📝 Manage menu items (CRUD operations)
- 📊 View order analytics
- 📦 Update order status in real-time
- ⭐ Monitor customer reviews
- 📱 Mobile-friendly dashboard

### 👑 Super Admin Features
- 🏢 Manage all restaurants
- ✅ Approve new restaurant registrations
- 📊 Platform-wide analytics
- 👥 User management system

## 🛠️ Technologies Used

| Category       | Technologies |
|----------------|--------------|
| **Frontend**   | HTML5, CSS3, JavaScript (ES6+), AJAX, Bootstrap 5 |
| **Backend**    | PHP 7.4+, MySQL 5.7+ |
| **Security**   | Password hashing, Prepared statements, CSRF protection |
| **Performance**| Caching, Image optimization |
| **Dev Tools**  | Git, Composer |

## 🚀 Installation

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4+
- MySQL 5.7+
- Composer (for dependencies)

```bash
# Clone the repository
git clone https://github.com/yourusername/food-order-system.git
cd food-order-system

# Import database schema
mysql -u root -p food_order_system < database/schema.sql

# Install dependencies (if any)
composer install
