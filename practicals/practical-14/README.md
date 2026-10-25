# Practical 14: Admin Dashboard with Role-Based Access Control, Dynamic Menu, and Audit Log

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO4, CO5  
**Total Hours:** 6 Hours  

---

## 1. Problem Definition
Develop an admin dashboard that displays user statistics, student count, event count, and latest registrations. Restrict access to admin users only, show dynamic menu options based on role, and track actions in audit logs.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are dashboard values loaded dynamically from the database?
Yes. Metric summary cards execute aggregate SQL queries:
- `SELECT COUNT(*) AS c FROM students WHERE status != 'deleted'`
- `SELECT COUNT(*) AS c FROM events`
- `SELECT COUNT(*) AS c FROM registrations`
- `SELECT COUNT(*) AS c FROM users`

### Q2: Is access restricted to admin users?
Yes. Enforced via `require_admin()` in `includes/auth_guard.php`. Regular students or guests attempting to load `admin.php` are immediately rejected.

### Q3: Are role-based menus working?
Yes. The navigation header inspects `$_SESSION['role']`:
- **Admins:** See an highlighted orange "Admin Panel" link.
- **Students:** See "Dashboard" and personalized "Profile" links.
- **Guests:** See only "Login" and "Register" buttons.

### Q4: Are important admin actions recorded in `audit_logs`?
Yes. Every critical action (student added, student deleted, event created, user logged in, profile updated) triggers `log_audit_action($conn, $userId, $action, $details)`. The audit trail captures:
- Timestamp
- User ID / Username
- Action code
- Action details
- Client IP Address (`$_SERVER['REMOTE_ADDR']`)

---

## 3. Database Table: `audit_logs`
```sql
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 4. Viva Questions
- **Q: What is the purpose of an Audit Log in enterprise web systems?**  
  *Ensures accountability, provides non-repudiation, assists forensics during security incidents, and complies with data protection regulations.*
- **Q: How does SQL `COUNT(*)` differ from `COUNT(column_name)`?**  
  *`COUNT(*)` counts all rows returned by the query including NULL values, whereas `COUNT(column_name)` counts only rows where the specified column is not NULL.*
