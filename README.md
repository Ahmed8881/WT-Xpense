# Smart Expense & Income Dashboard

A full-stack web application built with PHP, MySQL, and Tailwind CSS for managing income and expenses with interactive charts.

## Features

- **User Authentication** - Secure login system with password hashing
- **User Registration** - Sign up page for new users
- **Income Management** - Add, view, and delete income records
- **Expense Management** - Track expenses by category
- **Category Management** - Create and manage expense categories (CRUD)
- **Dashboard Analytics** - 
  - Total Income, Total Expense, Balance, Today's Expense
  - Monthly Expense Bar Chart
  - Income vs Expense Comparison Chart
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Multi-user Support** - Each user has their own account

## Tech Stack

- **Backend:** PHP (procedural)
- **Database:** MySQL
- **Frontend:** Tailwind CSS (CDN)
- **Charts:** Chart.js (CDN)
- **Server:** XAMPP

## Installation

### Prerequisites

- XAMPP (or any PHP + MySQL server)
- Web browser

### Setup Steps

1. **Copy project to XAMPP htdocs folder:**
   ```
   Copy-Item -Path "C:\Users\User\Desktop\Xpense" -Destination "C:\xampp\htdocs\Xpense" -Recurse
   ```

2. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

3. **Create Database:**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "Import" tab
   - Browse and select: `C:\xampp\htdocs\Xpense\database\schema.sql`
   - Click "Go" to import

   OR manually:
   - Create a database named `expense_tracker`
   - Run the SQL from `database/schema.sql`

4. **Access the Application:**
   - Open browser and go to: http://localhost/Xpense/public/login.php
   - Or use signup page: http://localhost/Xpense/public/signup.php

5. **Login Credentials (Demo Account):**
   - Username: `admin`
   - Password: `admin123`

6. **Or Create Your Own Account:**
   - Go to: http://localhost/Xpense/public/signup.php
   - Fill in the signup form
   - Start managing your expenses!

## Project Structure

```
Xpense/
├── config/
│   └── database.php          # Database configuration
├── public/
│   ├── index.php             # Dashboard
│   ├── income.php            # Income management
│   ├── expense.php           # Expense management
│   ├── categories.php        # Category management
│   ├── login.php             # Login page
│   ├── signup.php            # Sign up page
│   ├── logout.php            # Logout handler
│   ├── includes/
│   │   ├── header.php        # Header layout
│   │   └── footer.php        # Footer layout
│   └── assets/
│       ├── css/
│       │   └── tailwind.css  # Custom styles
│       └── js/
│           └── charts.js     # Chart.js initialization
├── src/
│   ├── functions.php         # Database functions
│   └── auth.php              # Authentication logic
├── database/
│   └── schema.sql            # Database schema
└── setup_admin.php           # Admin setup utility
```

## Database Schema

### Tables

1. **users** - User authentication
2. **categories** - Expense categories
3. **incomes** - Income records
4. **expenses** - Expense records

## Usage

1. **Sign Up / Login** 
   - Create a new account or use demo account (admin/admin123)
   - Secure authentication with password hashing

2. **Dashboard** - View financial summary and charts

3. **Income** - Add income with title, amount, and date

4. **Expenses** - Add expenses with category, amount, and date

5. **Categories** - Manage expense categories (add, edit, delete)

6. **Multi-user** - Each user has separate data and dashboard

## Default Data

The system comes with:
- Default admin user (admin/admin123)
- 6 sample categories (Food, Transport, Entertainment, Utilities, Healthcare, Shopping)

## Security Features

- Password hashing using PHP's `password_hash()`
- Session-based authentication
- Prepared statements for SQL queries (SQL injection prevention)
- XSS protection with `htmlspecialchars()`

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Troubleshooting

**Database Connection Error:**
- Verify MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Ensure database `expense_tracker` exists

**Page Not Found:**
- Verify Apache is running
- Check the URL: `http://localhost/Xpense/public/`

**Login Issues:**
- Clear browser cache
- Verify user exists in database
- Default credentials: admin/admin123

## License

MIT License - Free to use and modify

## Author

Built as a demonstration project for expense and income tracking.
