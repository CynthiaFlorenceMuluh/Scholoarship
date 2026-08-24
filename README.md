# ShopEase – Member 1 Customer Account Module

This package implements Member 1 of the 30-page e-commerce project:

1. Login Page
2. Registration Page
3. Forgot / Reset Password Page
4. Customer Profile / Account Page
5. My Addresses Page

## Technologies
- HTML5
- CSS3
- Vanilla JavaScript
- PHP sessions
- No database
- No SQL

## Folder structure

```text
member1_ecommerce/
├── index.php
├── README.md
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── includes/
│   ├── header.php
│   └── footer.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── forgot-password.php
└── account/
    ├── profile.php
    ├── addresses.php
    └── logout.php
```

## Run locally

Put the `member1_ecommerce` folder inside XAMPP `htdocs`, WAMP `www`, Laragon `www`, or another PHP web root.

Then open:

`http://localhost/member1_ecommerce/`

## Demo login

Customer:
- Email: `customer@shopease.test`
- Password: `Customer@123`

Admin:
- Email: `admin@shopease.test`
- Password: `Admin@123`

The admin demo redirects to the profile page as a placeholder. Replace that redirect with your real admin dashboard.

## Database integration

No database connection or SQL is included.

Each PHP file contains a `DATABASE INTEGRATION POINT` comment showing where to connect your own database.

Recommended approach:
- Use PDO.
- Use prepared statements.
- Store passwords with `password_hash()`.
- Verify passwords with `password_verify()`.
- Use `session_regenerate_id(true)` after login.
- Store only necessary account information in sessions.
- Use CSRF tokens on production POST forms.
- Use secure, HttpOnly, SameSite cookies.
- For password recovery, generate cryptographically secure, expiring reset tokens and email them to the customer.

## Accessibility

The global accessibility panel supports:
- Default / Large / Extra-large text
- High contrast
- Reduced motion
- Simplified layout
- Reset preferences

Preferences are stored in browser localStorage for the prototype and persist across pages/sessions.

## Important production note

The current project intentionally uses PHP sessions and demo validation instead of a database. Before production:
- Replace demo authentication with database lookup.
- Replace session address storage with database CRUD.
- Implement real email-based password reset.
- Add CSRF protection.
- Add server-side authorization checks for every protected action.
- Add rate limiting and login attempt protection.
- Validate and sanitize all server-side inputs.
