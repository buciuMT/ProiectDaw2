# Lib4All Database Specification

## Overview
This document describes the database schema for the Lib4All library management system.

## Tables

### 1. Users
Stores information about library users.

| Column Name | Type | Constraints | Description |
|-------------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | VARCHAR(255) | NOT NULL | User's full name |
| email | VARCHAR(255) | NOT NULL, UNIQUE | User's email address |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| role | ENUM('admin', 'librarian', 'member') | NOT NULL, DEFAULT 'member' | User's role |
| verified | DATE | NULL | Date when user was verified |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation date |

### 2. Books
Stores information about books in the library.

| Column Name | Type | Constraints | Description |
|-------------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| title | VARCHAR(255) | NOT NULL | Book title |
| author | VARCHAR(255) | NOT NULL | Book author |
| isbn | VARCHAR(20) | UNIQUE | ISBN number |
| published_year | INT |  | Year of publication |
| genre | VARCHAR(100) |  | Book genre |
| copies_total | INT | NOT NULL, DEFAULT 1 | Total copies in library |
| copies_available | INT | NOT NULL, DEFAULT 1 | Available copies |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Entry creation date |

### 3. Categories
Stores book categories.

| Column Name | Type | Constraints | Description |
|-------------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | VARCHAR(100) | NOT NULL, UNIQUE | Category name |
| description | TEXT |  | Category description |

### 4. BookCategories
Junction table for books and categories (many-to-many relationship).

| Column Name | Type | Constraints | Description |
|-------------|------|-------------|-------------|
| book_id | INT | FOREIGN KEY (books.id) | Book identifier |
| category_id | INT | FOREIGN KEY (categories.id) | Category identifier |

### 5. Loans
Tracks book loans to users.

| Column Name | Type | Constraints | Description |
|-------------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | INT | FOREIGN KEY (users.id) | User who borrowed |
| book_id | INT | FOREIGN KEY (books.id) | Borrowed book |
| loan_date | DATE | NOT NULL | Date when book was borrowed |
| due_date | DATE | NOT NULL | Date when book is due |
| return_date | DATE |  | Date when book was returned |
| status | ENUM('active', 'returned', 'overdue') | NOT NULL, DEFAULT 'active' | Loan status |

## Relationships
- Users can have many Loans (1:N)
- Books can have many Loans (1:N)
- Books can belong to many Categories (N:M)
- Categories can have many Books (N:M)

## Indexes
- Users: email (unique)
- Books: isbn (unique), title, author
- Loans: user_id, book_id, status