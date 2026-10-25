# Practical 12: Event Management CRUD with Poster Upload and File Validation

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO4, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Develop event management functionality for admin users. Admin can add, view, update, delete, and upload event posters with file type and file size validation.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are add, update, and delete functionalities implemented correctly?
Yes, within `admin.php` and `api/events.php`, allowing administrators to schedule, manage capacities, specify dates/times, and manage campus events.

### Q2: Is the UI linked with the database correctly?
Yes, new events inserted via the admin modal are stored into the `events` table with the authenticated administrator's ID linked as `created_by`.

### Q3: Are poster uploads validated for type and size?
Yes, multiple layers of validation protect the file upload:
1. **Frontend Constraint:** `accept="image/jpeg,image/png,image/webp"` on the `<input type="file">`.
2. **File Size Check:** `$_FILES['poster']['size'] <= 2 * 1024 * 1024` (strict 2MB limit).
3. **MIME Type Inspection:** Uses PHP `finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_tmp)` checking for `image/jpeg`, `image/png`, or `image/webp`. This prevents attackers from renaming `.php` or `.exe` files to `.jpg`.
4. **Filename Sanitization:** Uploaded files are renamed using cryptographically safe random timestamps:
   `event_` + `time()` + `_` + `rand(100, 999)` + `.` + `$ext`.

### Q4: Are success and failure messages displayed?
Yes, clear feedback is rendered via alerts and audit actions recorded in `audit_logs`.

---

## 3. Upload Security Checklist
- [x] Checks MIME type using `fileinfo` extension
- [x] Checks file extension whitelist
- [x] Enforces 2MB maximum file size limit
- [x] Generates random sanitized server filename
- [x] Stores uploads outside of code execution directories

---

## 4. Viva Questions
- **Q: Why is checking `$_FILES['poster']['type']` not sufficient for security?**  
  *`$_FILES['poster']['type']` is sent by the client browser in the HTTP request and can be easily spoofed using Postman or Burp Suite. Server-side `finfo` inspects magic bytes.*
- **Q: Why must uploaded files be renamed?**  
  *Prevents directory traversal attacks (`../../shell.php`), filename collisions, and execution of malicious payloads.*
