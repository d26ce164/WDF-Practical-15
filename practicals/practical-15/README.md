# Practical 15: Final Integration, Testing, Documentation, GitHub Submission, and Local Deployment

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO2, CO4, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Integrate all modules into a single deployable StudentHub portal. Verify navigation, responsiveness, authentication, database operations, security checks, and AJAX functionality. Deploy locally using XAMPP/Apache virtual host and submit GitHub repository.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are all pages properly linked and navigable?
Yes. The site navigation links all 10+ pages (`index.php`, `about.php`, `events.php`, `faq.php`, `contact.php`, `feedback.php`, `register.php`, `login.php`, `dashboard.php`, `profile.php`, `admin.php`) with consistent header, footer, and active highlight states.

### Q2: Are sessions, roles, validations, and database operations working end-to-end?
- **Auth & Session Flow:** Register -> Login with BCRYPT -> RBAC Redirection (Admin vs Student) -> 15m Timeout -> Logout.
- **Data Flow:** Students and events add/edit/delete seamlessly with MySQLi prepared statements.

### Q3: Are security and accessibility checklists completed?
- **Security:** SQL Injection immune (prepared statements), XSS immune (`htmlspecialchars`, sanitization filters), CSRF hardened cookies (`SameSite=Lax`, `HttpOnly`), password hashes (`BCRYPT`), validated file uploads.
- **Accessibility:** WCAG AA contrast, skip-to-content anchor, semantic HTML5 tags, keyboard navigability.

### Q4: Is project documentation sufficient for installation and testing?
Yes. A step-by-step XAMPP setup guide, database import script, seed data credentials, and test reports are fully provided.

### Q5: Is the project repository clean and properly committed?
Yes. Git folder hierarchy is organized into `assets/`, `includes/`, `data/`, `database/`, `api/`, and `practicals/practical-01` through `practical-15`.

---

## 3. Local Deployment Guide (XAMPP / Apache)

### Step 1: Clone or Copy Repository
Copy this project folder `AllP` to your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\studenthub\
```

### Step 2: Start Apache and MySQL Services
Open **XAMPP Control Panel** and start:
- **Apache** (Port 80/443)
- **MySQL** (Port 3306)

### Step 3: Import the Database
1. Open browser: `http://localhost/phpmyadmin`
2. Create database: `studenthub`
3. Click **Import** and select `database/studenthub.sql`
4. Click **Go** to seed all tables and records.

### Step 4: Launch Application
Open in your browser:
```
http://localhost/studenthub/
```

### Pre-Seeded Credentials for Viva Demonstration:
- **Admin:** `admin` / `Password123!`
- **Student:** `divyakumar` / `Password123!`

---

## 4. End-to-End Test Matrix

| Practical Area | Test Performed | Expected Behavior | Result |
|---|---|---|---|
| P02 / P03 | Mobile Viewport (<768px) | Hamburger menu toggle, single-column flex | PASSED |
| P04 | Dark/Light Switcher | Toggles `--bg-main` and remembers in `localStorage` | PASSED |
| P05 | Registration Validation | Regex blocks weak passwords and invalid emails | PASSED |
| P06 | Events Catalog | Dynamic client-side search and category filtering | PASSED |
| P07 | Contact Form | Appends message into `submissions.json` & `.csv` | PASSED |
| P08 / P09 | DB Registration | Inserts hashed password (`BCRYPT`) without duplicate email | PASSED |
| P10 | Role Access (RBAC) | Blocks non-admin from loading `admin.php` | PASSED |
| P11 | Student CRUD | Deletes with confirm prompt, searches by ID | PASSED |
| P12 | Poster Upload | Verifies MIME type and rejects non-image files | PASSED |
| P13 | REST API | `GET api/events.php` returns JSON array | PASSED |
| P14 | Dashboard Metrics | Dynamic counts match MySQL table totals | PASSED |
