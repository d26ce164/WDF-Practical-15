# Practical 10: Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO5  
**Total Hours:** 2 Hours  

---

## 1. Problem Definition
Develop secure login and logout functionality using PHP sessions. Verify password using `password_verify()`, regenerate session ID after login, restrict dashboard access, implement role-based redirection, session timeout, and optional secure remember-me token.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are sessions started and destroyed securely?
Yes:
- Sessions are initialized in `includes/auth_guard.php` with `httponly: true` (blocks JavaScript XSS access to session cookie) and `samesite: Lax` (mitigates CSRF).
- On logout (`logout.php`), `$_SESSION` is cleared, session cookie is expired in the past, and `session_destroy()` invalidates server state.

### Q2: Is `password_verify()` used correctly?
Yes. On login:
```php
if (password_verify($password, $user['password'])) {
    session_regenerate_id(true); // Mitigates Session Fixation attacks
    ...
}
```
`password_verify()` executes in constant time to prevent side-channel timing attacks.

### Q3: Is unauthorized dashboard access blocked?
Yes. Guards `require_login()` and `require_admin()` inspect `$_SESSION['user_id']` and `$_SESSION['role']`. If a regular student attempts navigating to `admin.php`, they are instantly bounced to `dashboard.php?msg=unauthorized_admin`.

### Q4: Is session timeout implemented?
Yes. In `includes/auth_guard.php`:
```php
$timeout_duration = 900; // 15 Minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php?msg=session_timeout");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
```

### Q5: Are admin and student users redirected to appropriate dashboards?
- Role `admin` -> redirected to `admin.php`
- Role `student` -> redirected to `dashboard.php`

### Q6: How is Remember-Me implemented securely?
Instead of storing raw user IDs or passwords in cookies, we generate a cryptographically random token (`bin2hex(random_bytes(32))`), store its SHA-256 hash in `remember_tokens` table, and transmit only the random token in an HTTP-only cookie for 30 days.

---

## 3. Viva Questions
- **Q: What is Session Fixation? How is it prevented?**  
  *Session Fixation is an attack where an attacker forces a victim to use a known session identifier. It is prevented by calling `session_regenerate_id(true)` immediately after successful authentication.*
- **Q: Why use `httponly` cookies?**  
  *Prevents client-side scripts (`document.cookie`) from reading the session cookie, neutralizing session theft via Cross-Site Scripting (XSS).*
