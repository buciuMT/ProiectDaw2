-- Sample data for Lib4All

-- Insert sample books
INSERT INTO books (title, author, isbn, published_year, genre, copies_total, copies_available) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 1925, 'Fiction', 3, 2),
('To Kill a Mockingbird', 'Harper Lee', '9780061120084', 1960, 'Fiction', 2, 1),
('1984', 'George Orwell', '9780451524935', 1949, 'Dystopian Fiction', 4, 3),
('Pride and Prejudice', 'Jane Austen', '9780141439518', 1813, 'Romance', 2, 2),
('The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 1951, 'Fiction', 3, 1),
('Lord of the Flies', 'William Golding', '9780571056862', 1954, 'Fiction', 2, 2);

-- Insert sample users
INSERT INTO users (name, email, password, role, verified) VALUES
('Admin User', 'admin@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-08-27'),
('Librarian User', 'librarian@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian', '2025-08-27'),
('Member User', 'member@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', NULL);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Fiction', 'Fictional works'),
('Non-Fiction', 'Non-fictional works'),
('Science Fiction', 'Science fiction books'),
('Mystery', 'Mystery and thriller books'),
('Romance', 'Romance novels'),
('Biography', 'Biographical works');

-- Insert sample book-category relationships
INSERT INTO book_categories (book_id, category_id) VALUES
(1, 1), -- The Great Gatsby - Fiction
(2, 1), -- To Kill a Mockingbird - Fiction
(3, 3), -- 1984 - Science Fiction
(4, 5), -- Pride and Prejudice - Romance
(5, 1), -- The Catcher in the Rye - Fiction
(6, 1); -- Lord of the Flies - Fiction