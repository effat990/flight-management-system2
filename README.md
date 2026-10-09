# FLY AIR — Flight Management System

An academic Database Management Systems (DBMS) project: a locally hosted flight management web application built with PHP and MySQL and run through XAMPP. AI tools assisted during development.

## Features

- Passenger registration
- Flight listing and flight management
- Flight search by airline/route text
- Airport information and airport management
- Flight booking
- Payment management
- Boarding-pass view with flight, seat, gate, and timing details
- MySQL database integration

> Feature availability reflects the included project files. This is an academic/local project, not a production airline booking platform.

## Tech Stack

- PHP
- MySQL
- HTML and CSS
- XAMPP (Apache and MySQL/MariaDB)
- SQL

## Project Structure

- `index.php` — home page
- `passenger.php` — passenger registration
- `flights.php` — flight management and search
- `airport.php` — airport management
- `booking.php` — booking workflow
- `payment.php` — payment workflow and boarding-pass access
- `boarding-pass-view.php` — boarding-pass view
- `database.php` — database-related management page
- `db.php` — database connection
- `database.sql` — database schema and sample data
- `style.css` — application styling
- `fly-air-hero.jpg` — home-page image asset

## Run Locally with XAMPP

1. Install XAMPP and start **Apache** and **MySQL**.
2. Copy the project folder into your XAMPP `htdocs` directory. For example: `C:\xampp\htdocs\sworna`
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Create a database named `flyair` (if it does not already exist).
5. Import `database.sql` into the `flyair` database.
6. Check `db.php` and update the database host, username, password, or database name if your local XAMPP configuration differs.
7. Open `http://localhost/sworna/` in your browser. If you rename the folder, use that folder name in the URL.

## Database

The SQL file defines these main tables:

- `passengers`
- `airports`
- `flights`
- `bookings`
- `payments`

## Notes and Security

- This project is intended for local academic demonstration and learning.
- Do not use real passenger details, card numbers, or sensitive payment credentials.
- Review database credentials before publishing. Never commit real passwords, secrets, or private data.
- The included configuration may assume the default local XAMPP MySQL account. Adjust it for your setup.
- AI tools assisted in development; the project is also an opportunity to study and understand the implementation.

## Future Improvements

- Add stronger input validation and security checks.
- Improve authentication and role-based access control.
- Add automated tests and clearer error handling.
- Improve mobile responsiveness and accessibility.
- Replace the demo payment workflow with a secure payment provider only if developing a properly secured production system.

## Author
Effat Ara Sworna  

