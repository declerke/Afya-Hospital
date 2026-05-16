# Afya Hospital Management System

A multi-role hospital management web application built with PHP, MySQL, and Bootstrap. It provides separate portals for administrators, doctors, nurses, hospital staff, and patients.

## Portals

| Role | Entry Point | Capabilities |
|---|---|---|
| Admin | `Backend/admindashboard.php` | Full system control — users, doctors, nurses, staff, patients, appointments, schedules, billing, audit logs, notifications |
| Doctor | `Doctor/doctordashboard.php` | Appointments, schedules, medical records, billing, invoices, patient details, notifications |
| Nurse | `Nurse/nursedashboard.php` | View patients and appointments |
| Hospital Staff | `HospitalStaff/hospitalstaffdashboard.php` | View patients, appointments, and doctor schedules |
| Patient | `Patient/index.html` | Book appointments, view records, billing, feedback |

## Tech Stack

- **Backend:** PHP 8.x with PDO (MySQL)
- **Frontend:** Bootstrap 4, jQuery, Chart.js, Font Awesome
- **PDF Generation:** TCPDF
- **Server:** Apache via XAMPP
- **Database:** MySQL (`hospital_management`)

## Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP 8.x)
- Git

### Installation

1. Clone the repository into your XAMPP `htdocs` folder:
   ```bash
   git clone https://github.com/declerke/Afya-Hospital.git FINALPROJECT2025
   ```

2. Start Apache and MySQL from the XAMPP Control Panel.

3. Import the database:
   - Open `http://localhost/phpmyadmin`
   - Create a database named `hospital_management`
   - Import the SQL dump file (ask the project team for the dump)

4. Configure the database connection in `Backend/db_connect.php`:
   ```php
   $host = 'localhost';
   $dbname = 'hospital_management';
   $username = 'root';
   $password = '';
   ```

5. Open the app:
   ```
   http://localhost/FINALPROJECT2025/Backend/loginpage.php
   ```

## Project Structure

```
FINALPROJECT2025/
├── Backend/          # Admin portal + shared assets and API endpoints
│   ├── assets/
│   │   ├── css/      # Bootstrap, Font Awesome, custom styles
│   │   ├── js/       # jQuery, app logic, chart data fetchers
│   │   └── img/      # Uploaded profile images and system images
│   ├── admindashboard.php
│   ├── db_connect.php
│   └── loginpage.php
├── Doctor/           # Doctor portal
├── Nurse/            # Nurse portal
├── HospitalStaff/    # Hospital Staff portal
└── Patient/          # Patient-facing public site
```

## Features

- Role-based access control with PHP session authentication
- Real-time dashboard charts (patient totals, admissions, appointments)
- Appointment booking and management across all roles
- Doctor schedule management
- Medical records with encrypted file uploads
- PDF invoice and billing generation via TCPDF
- Notification system
- Audit logs and data access logs
- Staff self-registration with Staff ID verification
- Feedback system

## Security Notes

- Passwords are hashed with bcrypt (`password_hash` / `password_verify`)
- All database queries use PDO prepared statements
- Uploaded files are stored encrypted under `Backend/assets/fileuploads/`
- Never commit `db_connect.php` credentials to a public repository — update the file locally after cloning

## Contributors

- [Ian Mboya](https://github.com/declerke)
