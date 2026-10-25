# StudentHub - CHARUSAT Academic & Campus Engagement Portal
### Subject: Web Development Frameworks (ITUE203)
**Faculty:** Faculty of Technology & Engineering (FTE / CSPIT / DEPSTAR)  
**University:** Charotar University of Science and Technology (CHARUSAT), Changa  
**Semester:** 3rd Semester, 2026-27 (Odd)  

---

## 📖 Project Overview
**StudentHub** is an integrated, semester-long web application engineered to fulfill all 15 practical laboratory requirements for **ITUE203**. The application covers modern frontend responsive UI design, accessibility, client-side asynchronous JavaScript, server-side PHP processing, relational database modeling, role-based access control (RBAC), and REST APIs.

---

## 🗂️ Project Directory Structure

```
D:\WDF\AllP\
├── api/
│   └── events.php                  # Practical 13: RESTful JSON API endpoint (GET, POST, DELETE)
├── assets/
│   ├── css/
│   │   └── main.css                # Practical 2 & 3: CSS Grid, Flexbox, Light/Dark theme, responsive queries
│   ├── js/
│   │   └── main.js                 # Practical 4: Theme toggle, hamburger drawer, accordions, toast notifications
│   ├── uploads/                    # Practical 12: Directory for validated uploaded event posters
│   └── images/                     # Static media and campus logos
├── data/
│   ├── events.json                 # Practical 6: 15 pre-seeded campus events
│   ├── students.json               # Practical 6: 15 verified student records
│   ├── faqs.json                   # Practical 4 & 6: 15 campus support FAQs
│   ├── contact_submissions.json    # Practical 7: Form processing JSON storage
│   └── contact_submissions.csv     # Practical 7: Form processing CSV storage
├── database/
│   └── studenthub.sql              # Practical 8: Complete normalized schema, foreign keys & 20+ seed records
├── includes/
│   ├── db.php                      # Practical 8: MySQLi database connection & audit logging helper
│   ├── auth_guard.php              # Practical 10: Session timeout (15 min), RBAC guards, regeneration
│   ├── header.php                  # Practical 2: Semantic HTML5 header, navigation, skip-to-content
│   └── footer.php                  # Practical 2: Semantic HTML5 footer, WCAG compliance
├── practicals/                     # Practical-by-practical lab manuals, viva Q&A, and reports
│   ├── practical-01/               # Practical 1: Requirements, sitemap, wireframes, Git setup
│   ├── practical-02/               # Practical 2: Semantic HTML5 & accessibility checklist
│   ├── practical-03/               # Practical 3: CSS Grid, Flexbox & responsive breakpoints
│   ├── practical-04/               # Practical 4: JavaScript DOM, events & localStorage theme
│   ├── practical-05/               # Practical 5: Regex validation & password strength meter
│   ├── practical-06/               # Practical 6: Fetch API, search, filter, sort & pagination
│   ├── practical-07/               # Practical 7: Server-side validation, JSON & CSV file writing
│   ├── practical-08/               # Practical 8: ER model, MySQL normalization & connectivity
│   ├── practical-09/               # Practical 9: Duplicate email check & BCRYPT password hashing
│   ├── practical-10/               # Practical 10: Sessions, RBAC, timeout & remember-me token
│   ├── practical-11/               # Practical 11: Student CRUD with search, filter & CSV export
│   ├── practical-12/               # Practical 12: Event CRUD & validated poster upload
│   ├── practical-13/               # Practical 13: Asynchronous Fetch CRUD without reload
│   ├── practical-14/               # Practical 14: Admin dashboard metrics & system audit logs
│   └── practical-15/               # Practical 15: Integration, testing matrix & deployment
├── index.php                       # Home landing page with hero banner and feature overview
├── about.php                       # About page with curriculum & syllabus mapping table
├── events.php                      # Campus events catalog with Fetch API, live search & pagination
├── faq.php                         # Interactive accordion FAQ with live keyword search
├── contact.php                     # Contact form with PHP sanitization & dual JSON/CSV file writing
├── feedback.php                    # Academic course feedback rating system
├── register.php                    # Registration form with Regex checks and BCRYPT database insert
├── login.php                       # Secure authentication with session regeneration & remember-me
├── logout.php                      # Secure session termination
├── dashboard.php                   # Authenticated student dashboard with registered events list
├── profile.php                     # Student profile viewing and updating interface
└── admin.php                       # Administrator control center: student CRUD, event posters, audit trail
```

---

## 🚀 Quick Setup & Installation Guide

### Prerequisites
- **XAMPP / WAMP / LAMP** with PHP 8.0+ and MySQL / MariaDB.
- Modern web browser (Chrome, Edge, Firefox).

### Setup Instructions
1. **Copy Project Folder:**  
   Copy the `AllP` folder into your web server root:  
   `C:\xampp\htdocs\studenthub\`
2. **Start Services:**  
   Launch XAMPP Control Panel and start **Apache** and **MySQL**.
3. **Database Import:**  
   - Visit `http://localhost/phpmyadmin/` in your browser.
   - Create a new database named `studenthub`.
   - Go to the **Import** tab, browse to `database/studenthub.sql`, and click **Go**.
4. **Access StudentHub:**  
   Open your browser and navigate to:  
   `http://localhost/studenthub/`

---

## 🔑 Pre-Seeded Credentials for Lab Evaluation & Viva

| Role | Username | Password | Purpose |
|---|---|---|---|
| **System Administrator** | `admin` | `Password123!` | Full access to Admin Panel, student CRUD, event poster upload, and audit logs |
| **Enrolled Student** | `divyakumar` | `Password123!` | Access to Student Dashboard, event passes, and personal profile management |
| **Enrolled Student 2** | `priya_patel` | `Password123!` | Additional student account for multi-user testing |

---

## 📋 Course Outcomes (CO) & Practical Mapping

| Practical | Module Title | Core Concept | CO Mapping |
|---|---|---|---|
| **P01** | Project Initiation & Sitemap | Requirements, Wireframe, GitHub | **CO1** |
| **P02** | Semantic HTML5 Pages | Semantic Elements, Accessibility, Skip Links | **CO1, CO2** |
| **P03** | Responsive UI Design | CSS Grid, Flexbox, Mobile-First Design | **CO2** |
| **P04** | JavaScript DOM & Events | DOM Manipulation, localStorage Theme | **CO3, CO4** |
| **P05** | Form Validation | Regex Validation, Password Strength Meter | **CO1, CO3** |
| **P06** | External JSON & Fetch API | Dynamic Fetch, Search, Filter, Pagination | **CO3, CO4** |
| **P07** | Server-Side Processing | PHP POST, CSV/JSON File Writing | **CO1, CO5** |
| **P08** | MySQL Schema & Connectivity | 3NF Schema, Foreign Keys, MySQLi Connect | **CO1, CO5** |
| **P09** | Secure Registration | Duplicate Check, BCRYPT Password Hashing | **CO1, CO3, CO5** |
| **P10** | Session Auth & RBAC | Session Regeneration, 15m Timeout, Tokens | **CO1, CO5** |
| **P11** | Student Management CRUD | Search, Filter, Soft Delete, CSV Export | **CO1, CO4, CO5** |
| **P12** | Event CRUD & Poster Upload | MIME & Size Validation, Upload Safety | **CO1, CO4, CO5** |
| **P13** | PHP JSON API & AJAX | RESTful Endpoints, Fetch without Reload | **CO3, CO4, CO5** |
| **P14** | Admin Dashboard & Audit Log | Metric Summaries, Traceable Audit Logs | **CO1, CO4, CO5** |
| **P15** | Final Integration & Deployment | End-to-End Verification, Deployment Guide | **CO2, CO4, CO5** |

---

## 🛡️ Security Features Implemented
- **SQL Injection Prevention:** 100% prepared statements (`$stmt->prepare()`, `$stmt->bind_param()`).
- **Cross-Site Scripting (XSS) Prevention:** Output sanitized using `htmlspecialchars()` and input sanitized using `filter_input()`.
- **Session Security:** `session_regenerate_id(true)` prevents session fixation; `httponly` and `samesite=Lax` cookies block script access and cross-site requests.
- **Session Inactivity Timeout:** 15-minute automatic timeout (`auth_guard.php`).
- **File Upload Protection:** Strict MIME type checking (`finfo`), extension whitelisting, and file renaming prevent arbitrary script execution.
- **Auditing & Traceability:** IP addresses and timestamps recorded in `audit_logs` for all critical administrative actions.

---

## 👨‍🏫 Faculty & Course Endorsement
- **Faculty of Technology and Engineering (FTE)**
- **Departments:** CSPIT (CE, IT, CSE, AIML) & DEPSTAR (CE, CSE, IT)
- **Course Faculty Team:** Dr. Martin Parmar, Dr. Mrugendra Rahevar, Ms. Srushti Gajjar, Ms. Vaibhavi Patel, Mr. Ravi Patel, Mr. Dhaval Patel, Dr. Jalpesh Vasa, Ms. Priyanka Padhiyar, Mr. Jay Patel, Mr. Vaibhav Matalia, Ms. Pooja Singh Chaudhary, Dr. Nikita Bhatt (Head-CE, CSPIT).


## Updated setup and test notes
- Use the PHP application pages (`index.php`, `login.php`, `register.php`, `events.php`, `dashboard.php`, `profile.php`, and `admin.php`) through a PHP-enabled web server. The `.html` files are static practical/demo copies and do not provide the same server-side authentication/database behavior.
- Enable PHP extensions `mysqli` and `fileinfo` in XAMPP/php.ini, restart Apache, start MySQL, and import `database/studenthub.sql` in phpMyAdmin.
- Demo accounts after importing the SQL file: `admin` / `Password123!`, `divyakumar` / `Password123!`, `priya_patel` / `Password123!`.
- Event registration now requires a logged-in student account, writes to `registrations`, prevents duplicate registrations and full-event registration, and appears on the student dashboard.
- The events API supports GET, POST, PUT and DELETE. Keep the database running for persistent API operations.
- Contact/feedback file-based submissions require the `data/` directory to be writable by the web-server process.
