from pathlib import Path

# Create the full README.md content as a string
readme_content = """
# 🌸 Bloom Cosmetic – E-commerce Web App

A lightweight and responsive cosmetic store built as a real-world freelance project. This web app is developed using **PHP**, **MySQL**, **HTML/CSS**, and **vanilla JavaScript**.

Designed for a professional client, it handles order submissions, coupon validation, review management, and PDF report generation.

---

## 💻 Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8+, Composer, PHPMailer
- **Database**: MySQL (see `bloom_db.sql`)
- **Tools**: VS Code, XAMPP/Laragon

---

## 🧩 Key Features

✅ Multilingual-friendly UI (EN/FR/AR)  
✅ Product Order Form with coupon validation  
✅ Reviews system with rating and feedback  
✅ Dynamic product data with JSON  
✅ PDF report generation (using TCPDF or FPDF)  
✅ Responsive design for mobile & desktop  
✅ Clean folder structure, modular PHP scripts

---

## 📁 Project Structure

bloom-cosmetic-ecommerce/
├── backend/
│ ├── submit_order.php
│ ├── validate_coupon.php
│ ├── submit_review.php
│ ├── get_reviews.php
│ ├── generate_order_report.php
├── frontend/
│ ├── index.html
│ ├── Shop.html
│ ├── style.css
├── database/
│ └── bloom_db.sql
├── composer.json
└── README.md



---

## 🗃️ MySQL Database Schema

You can import the provided `bloom_db.sql` into your local MySQL server.

```sql
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100),
  email VARCHAR(100),
  product_name VARCHAR(100),
  quantity INT,
  total_price DECIMAL(10,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  customer_name VARCHAR(100),
  rating INT CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE,
  discount_percentage INT CHECK (discount_percentage BETWEEN 0 AND 100),
  used BOOLEAN DEFAULT 0
);

🧪 How to Run Locally
Clone the repo:

git clone https://github.com/mo1726/bloom-cosmetic-ecommerce.git

Import the bloom_db.sql file into your MySQL server

Host the project using XAMPP or Laragon

Access via localhost/bloom-cosmetic-ecommerce/frontend/index.html
