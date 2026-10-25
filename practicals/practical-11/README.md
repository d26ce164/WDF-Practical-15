# Practical 11: Student Management CRUD with Search, Filter, and MySQL Statements

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO4, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Create a student management module to add, view, edit, delete, search, and filter student records. All database operations must use MySQLi prepared statements and display clear success/failure feedback messages.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are SELECT, INSERT, UPDATE, and DELETE operations working?
Yes, within `admin.php`:
- **CREATE (INSERT):** Added via modal form into `students` table.
- **READ (SELECT):** Displayed in responsive tabular format with department badge tags.
- **UPDATE:** Profile and student details updateable via prepared queries.
- **DELETE:** Soft deletion via `UPDATE students SET status = 'deleted' WHERE id = ?`.

### Q2: Is search by name/email/course implemented?
Yes, search query filters across multiple columns dynamically:
```sql
SELECT * FROM students 
WHERE status != 'deleted' 
  AND (first_name LIKE ? OR last_name LIKE ? OR student_id LIKE ? OR email LIKE ?)
  AND department = ?
ORDER BY id DESC LIMIT 50;
```

### Q3: Are delete operations confirmed before execution?
Yes, guarded by JavaScript confirmation: `onsubmit="return confirm('Confirm deleting student ...?');"` preventing accidental data deletion.

### Q4: Extension: CSV Export?
Yes, accessible via "Export Students CSV" button which streams RFC 4180 compliant CSV output directly to the browser.

---

## 3. Seed Dataset
Database includes 20 pre-seeded student records across Information Technology, Computer Engineering, Computer Science, and AI & ML departments.

---

## 4. Viva Questions
- **Q: What is the advantage of Soft Delete over Hard Delete?**  
  *Soft delete retains historical audit integrity, preserves referential relationships with event registrations, and allows data recovery in case of error.*
- **Q: Why are prepared statements immune to SQL Injection?**  
  *The SQL query template and the parameter data are transmitted to the database engine in separate channels. The engine compiles the query structure first, so user parameters cannot alter the query syntax.*
