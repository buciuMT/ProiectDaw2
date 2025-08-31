# Lib4All Database Specification

## Overview
This document describes the database schema for the Lib4All library management system. The system supports three user roles (admin, librarian, member) and provides functionality for book management, reservations, loans, and visit tracking.

## Tables

### 1. Users
Stores user account information.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'librarian', 'member') NOT NULL DEFAULT 'member',
    verified DATE NULL,
    verification_token VARCHAR(255) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Columns:**
- `id`: Unique identifier for each user
- `name`: User's full name
- `email`: User's email address (must be unique)
- `password`: Hashed password
- `role`: User's role in the system (admin, librarian, or member)
- `verified`: Date when the user's email was verified (NULL if not verified)
- `verification_token`: Token used for email verification
- `created_at`: Timestamp when the user account was created

### 2. Books
Stores information about books in the library.

```sql
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    published_year INT,
    genre VARCHAR(100),
    copies_total INT NOT NULL DEFAULT 1,
    copies_available INT NOT NULL DEFAULT 1,
    cover_image VARCHAR(255) NULL,
    loan_period INT DEFAULT 14,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Columns:**
- `id`: Unique identifier for each book
- `title`: Book title
- `author`: Book author
- `isbn`: International Standard Book Number (unique)
- `published_year`: Year the book was published
- `genre`: Book genre/category
- `copies_total`: Total number of copies in the library
- `copies_available`: Number of copies currently available
- `cover_image`: URL or path to book cover image
- `loan_period`: Default loan period in days
- `created_at`: Timestamp when the book was added to the library

### 3. Categories
Stores book categories.

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);
```

**Columns:**
- `id`: Unique identifier for each category
- `name`: Category name (must be unique)
- `description`: Description of the category

### 4. Book_Categories
Junction table for the many-to-many relationship between books and categories.

```sql
CREATE TABLE book_categories (
    book_id INT,
    category_id INT,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

**Columns:**
- `book_id`: Reference to a book
- `category_id`: Reference to a category

### 5. Reservations
Stores book reservation information.

```sql
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    due_date DATE NULL,
    returned_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
```

**Columns:**
- `id`: Unique identifier for each reservation
- `user_id`: Reference to the user who made the reservation
- `book_id`: Reference to the reserved book
- `reserved_at`: Timestamp when the reservation was made
- `status`: Current status of the reservation (active, completed, or cancelled)
- `due_date`: Date when the book is due to be returned
- `returned_at`: Timestamp when the book was returned

### 6. Loans
Stores book loan information.

```sql
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('active', 'returned', 'overdue') NOT NULL DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);
```

**Columns:**
- `id`: Unique identifier for each loan
- `user_id`: Reference to the user who borrowed the book
- `book_id`: Reference to the borrowed book
- `loan_date`: Date when the book was borrowed
- `due_date`: Date when the book is due to be returned
- `return_date`: Date when the book was actually returned
- `status`: Current status of the loan (active, returned, or overdue)

### 7. Visits
Stores website visit tracking information.

```sql
CREATE TABLE visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    page_url VARCHAR(255) NOT NULL,
    is_logged_in BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

**Columns:**
- `id`: Unique identifier for each visit
- `user_id`: Reference to the logged-in user (NULL if guest)
- `ip_address`: IP address of the visitor
- `user_agent`: Browser user agent string
- `page_url`: URL of the page visited
- `is_logged_in`: Whether the visitor was logged in
- `created_at`: Timestamp when the visit occurred

## Relationships

1. **Users and Reservations**: One-to-many (one user can have many reservations)
2. **Users and Loans**: One-to-many (one user can have many loans)
3. **Users and Visits**: One-to-many (one user can have many visits)
4. **Books and Reservations**: One-to-many (one book can have many reservations)
5. **Books and Loans**: One-to-many (one book can have many loans)
6. **Books and Categories**: Many-to-many (books can have multiple categories, categories can have multiple books)
7. **Categories and Books**: Many-to-many (through book_categories junction table)

## Indexes

The following indexes are recommended for optimal performance:

1. `users_email_idx` on `users(email)` - for fast email lookups during login
2. `books_isbn_idx` on `books(isbn)` - for fast ISBN lookups
3. `reservations_user_id_idx` on `reservations(user_id)` - for user reservation queries
4. `reservations_book_id_idx` on `reservations(book_id)` - for book reservation queries
5. `reservations_status_idx` on `reservations(status)` - for filtering reservations by status
6. `loans_user_id_idx` on `loans(user_id)` - for user loan queries
7. `loans_book_id_idx` on `loans(book_id)` - for book loan queries
8. `loans_status_idx` on `loans(status)` - for filtering loans by status
9. `visits_created_at_idx` on `visits(created_at)` - for visit analytics
10. `visits_is_logged_in_idx` on `visits(is_logged_in)` - for distinguishing logged-in vs guest visits

## Constraints

1. **Foreign Key Constraints**: All relationships are enforced with foreign key constraints
2. **Unique Constraints**: 
   - `users.email` must be unique
   - `users.verification_token` must be unique
   - `books.isbn` must be unique
   - `categories.name` must be unique
3. **Check Constraints**: 
   - `copies_total` >= 0
   - `copies_available` >= 0 and <= `copies_total`
   - `loan_period` > 0

## Triggers

1. **Reservation Triggers**: 
   - Prevent duplicate active reservations for the same user and book
   - Automatically update book availability when reservations are created or completed

2. **Loan Triggers**: 
   - Automatically update book availability when loans are created or completed
   - Update reservation status when a loan is created from a reservation

## Views

1. **Overdue Loans View**: Shows all loans with status 'overdue'
2. **Available Books View**: Shows all books with `copies_available` > 0
3. **Active Reservations View**: Shows all reservations with status 'active'
4. **User Loan History View**: Shows loan history for each user
5. **Book Popularity View**: Shows books ordered by reservation/loan count

## Notes

1. All timestamps are stored in UTC
2. Passwords are hashed using a secure hashing algorithm (bcrypt)
3. Email verification is required for user accounts
4. The system tracks both reservations (pre-bookings) and loans (actual borrowing)
5. Visit tracking helps with analytics and understanding user behavior
6. Cover images are stored as URLs or file paths, not binary data