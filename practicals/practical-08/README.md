# Practical 08: MySQL Schema Design, ER Model, PDO/MySQLi Connectivity, and Prepared Statements

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Design the database for StudentHub with students, events, and registrations. Create PHP database connection using MySQL statements.

---

## 2. Key Questions & Conceptual Answers

### Q1: Is the schema normalized properly?
Yes. The schema satisfies **Third Normal Form (3NF)**:
- **1NF:** All attributes are atomic (no repeating groups).
- **2NF:** All non-key attributes are fully functionally dependent on the primary key.
- **3NF:** No transitive dependencies exist. User credentials and authentication logic are decoupled into `users`, personal student profiles reside in `students`, event catalog in `events`, and many-to-many event attendance is normalized into `registrations`.

### Q2: Are primary keys and foreign keys correctly defined?
Yes:
- `users.id` (PK) -> `students.user_id` (FK with `ON DELETE SET NULL`)
- `users.id` (PK) -> `events.created_by` (FK with `ON DELETE SET NULL`)
- `students.id` (PK) + `events.id` (PK) -> `registrations.student_id` & `registrations.event_id` (FKs with `ON DELETE CASCADE`)
- Composite unique constraint: `UNIQUE KEY (student_id, event_id)` prevents a student from registering twice for the same event.

### Q3: Is the database connection handled securely?
Yes, in `includes/db.php`:
- Connection uses `new mysqli(...)` with defined constants.
- Sets charset to `utf8mb4` for complete Unicode support.
- Central error logging without leaking raw database credentials or stack traces to end users.

---

## 3. Entity-Relationship (ER) Architecture

```
+--------------------+           +--------------------+
|       USERS        |           |      STUDENTS      |
+--------------------+           +--------------------+
| id (PK)            | 1       1 | id (PK)            |
| username (UNIQUE)  |-----------| user_id (FK)       |
| email (UNIQUE)     |           | student_id (UNIQUE)|
| password (HASH)    |           | first_name         |
| role (ENUM)        |           | last_name          |
| full_name          |           | email, phone       |
+--------------------+           | department         |
         | 1                     +--------------------+
         |                                 | 1
         | M                               |
+--------------------+                     | M
|       EVENTS       |           +--------------------+
+--------------------+           |   REGISTRATIONS    |
| id (PK)            | 1       M |--------------------|
| title              |-----------| id (PK)            |
| category           |           | student_id (FK)    |
| description        |           | event_id (FK)      |
| event_date, time   |           | registered_at      |
| poster_image       |           +--------------------+
+--------------------+
```

---

## 4. Viva Questions
- **Q: What is a Foreign Key constraint?**  
  *A key used to link two tables together and enforce referential integrity between child and parent records.*
- **Q: Why use `utf8mb4` instead of standard `utf8` in MySQL?**  
  *MySQL's legacy `utf8` only supports up to 3 bytes per character, whereas `utf8mb4` supports full 4-byte Unicode characters, emojis, and international characters.*
