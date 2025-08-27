# Test User Credentials

## Pre-existing Users (Already in Database)

### Admin User
Email: admin@lib4all.com
Password: password
Role: admin

### Librarian User
Email: librarian@lib4all.com
Password: password
Role: librarian

### Member User
Email: member@lib4all.com
Password: password
Role: member

## Test Users (Added for Debugging)

### Test User
Email: test@example.com
Password: password
Role: member

### Admin Test User
Email: admin@example.com
Password: password
Role: admin

## How to Use
1. Use any of these pre-existing credentials to log in directly
2. The password for all test users is "password"
3. Admin and Librarian users have access to the admin panel and book management features
4. Member users can reserve books

## Creating New Users
1. Visit the registration page at `/register`
2. Fill in the registration form
3. You'll be redirected to the login page after registration
4. Log in with your new credentials

## Admin Features
Admin and Librarian users can:
- Access the admin panel at `/admin/migrations`
- Add, edit, and delete books
- Run database migrations

Note: These are default test credentials for development purposes only. 
In a production environment, you should change these passwords and use proper authentication mechanisms.