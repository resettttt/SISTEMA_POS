# POS System

A complete and functional Point of Sale (POS) system built with PHP using the MVC architecture pattern. The system connects to a MySQL database and provides a user-friendly interface for managing products, categories, customers, and sales.

## Features

- **Product Management**: Add, edit, delete, and search products
- **Category Management**: Organize products into categories
- **Customer Management**: Store customer information
- **Sales Management**: Process sales transactions with the POS interface
- **Receipt Generation**: Print professional receipts
- **Responsive Design**: Works on various screen sizes

## Architecture

The system follows the Model-View-Controller (MVC) pattern:

- **Models**: Handle database operations
  - ProductModel: Manages product data
  - CategoryModel: Manages category data
  - CustomerModel: Manages customer data
  - SalesModel: Manages sales data

- **Views**: Handle user interface
  - Product views: Product listing, creation, and editing
  - Category views: Category listing, creation, and editing
  - Customer views: Customer listing, creation, and editing
  - Sales views: POS interface and receipt generation

- **Controllers**: Handle business logic
  - ProductController: Manages product operations
  - CategoryController: Manages category operations
  - CustomerController: Manages customer operations
  - SalesController: Manages sales operations

## Database Schema

The system uses the following tables:

- `categories`: Stores product categories
- `products`: Stores product information
- `customers`: Stores customer information
- `sales`: Stores sales transactions
- `sale_items`: Stores items for each sale

## Installation

1. Create the database using the schema in `database_schema.sql`
2. Update the database configuration in `config/database.php` with your database credentials
3. Set up a web server with PHP and MySQL support
4. Access the system through your web browser

## Usage

1. Access the main page to see product listings
2. Use the navigation menu to access different sections:
   - Products: Manage your inventory
   - Categories: Organize products
   - Customers: Manage customer information
   - POS: Process sales
   - Sales: View transaction history

## Technology Stack

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome

## File Structure

```
pos_system/
├── config/
│   └── database.php
├── controllers/
│   ├── BaseController.php
│   ├── ProductController.php
│   ├── CategoryController.php
│   ├── CustomerController.php
│   └── SalesController.php
├── models/
│   ├── ProductModel.php
│   ├── CategoryModel.php
│   ├── CustomerModel.php
│   └── SalesModel.php
├── views/
│   ├── layout.php
│   ├── product/
│   ├── category/
│   ├── customer/
│   └── sales/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── includes/
│   └── view_helper.php
├── database_schema.sql
└── index.php
```