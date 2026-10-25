# Practical 09: Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO3, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Implement secure user registration by inserting user details into MySQL using MySQLi. Validate input on frontend and backend, check duplicate email/username, hash passwords using `password_hash()`, and display appropriate messages.

---

## 2. Key Questions & Conceptual Answers

### Q1: Is `password_hash()` used before storing passwords?
Yes. Never store plain text passwords or obsolete MD5/SHA1 hashes.
We employ PHP's native:
```php
$hashed_pw = password_hash($password, PASSWORD_BCRYPT);
```
BCRYPT utilizes an adaptive cryptographic key derivation algorithm with automatic cryptographic salt generation and adjustable cost factor (default 10), resisting brute-force and rainbow table attacks.

### Q2: Is duplicate email/username checked?
Yes. Prior to database insertion:
```php
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    // Abort and warn user
}
```
This avoids database duplicate key collision errors and prevents account spoofing.

### Q3: Are frontend and backend validations consistent?
Yes. Both validate:
- Full name >= 3 characters.
- Alphanumeric username (3-20 chars).
- Valid email format.
- 10-digit mobile number.
- Strong password complexity (Min 8 chars, 1 uppercase, 1 digit, 1 symbol).
- Mandatory terms acceptance.

---

## 3. Test Cases Report

| Test Case ID | Test Scenario | Input Data | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| TC-01 | Valid Registration | Unique username, valid email, strong password | Inserted into `users` & `students` | User created with generated Student ID | PASSED |
| TC-02 | Duplicate Email | `admin@charusat.edu.in` | "An account with this email address already exists!" | Warning shown, no insert | PASSED |
| TC-03 | Weak Password | `test123` | Password strength validation failure | Blocked on both JS & PHP | PASSED |
| TC-04 | SQL Injection Attempt | Username: `' OR '1'='1` | Prepared statement escapes characters | Handled safely as literal string | PASSED |

---

## 4. Viva Questions
- **Q: Why shouldn't MD5 or SHA256 be used for passwords?**  
  *MD5 and SHA256 are general hashing algorithms designed to be fast. Modern GPUs can calculate billions of hashes per second, making them vulnerable to brute-force and rainbow tables. BCRYPT is intentionally slow and salted.*
- **Q: What does `password_hash()` return?**  
  *A 60-character string containing the algorithm ID, cost factor, 22-character salt, and cipher hash.*
