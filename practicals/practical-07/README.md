# Practical 07: PHP Form Processing with Server-Side Validation and CSV/JSON File Storage

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Process submitted registration/contact form data using PHP. Validate and sanitize inputs on the server side and store records in CSV or JSON file format. Display success/error messages.

---

## 2. Key Questions & Conceptual Answers

### Q1: Is the form submitted using POST?
Yes. We use `method="POST"` to transmit confidential student contact and inquiry data within the HTTP request body rather than exposing it in cleartext in the browser URL query string (as in `GET`).

### Q2: Are inputs validated and sanitized server side?
Yes, using PHP built-in functions:
- **Sanitization:** `filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS)` and `filter_input(..., FILTER_SANITIZE_EMAIL)`.
- **Validation:** `filter_var($email, FILTER_VALIDATE_EMAIL)` and string length checks.
- Server-side validation guarantees data integrity even if an attacker disables JavaScript in the browser.

### Q3: Is file writing handled safely?
Yes:
- **JSON File Storage:** Reads existing array via `json_decode(file_get_contents(...), true)`, appends the new record, and writes back safely with `file_put_contents(..., JSON_PRETTY_PRINT)`.
- **CSV File Storage:** Uses `fopen(..., 'a')` in append mode and writes formatted lines using `fputcsv($fp, $dataArray)` to automatically handle commas and escape quotes.

### Q4: Are success and error responses displayed clearly?
Yes, alerts (`alert-success` and `alert-danger`) inform the user with dynamic PHP feedback.

---

## 3. Storage Outputs Generated
- `data/contact_submissions.json`
- `data/contact_submissions.csv`

---

## 4. Viva Questions
- **Q: Why is server-side validation mandatory even when frontend validation exists?**  
  *Frontend validation can easily be bypassed by disabling JavaScript or using tools like Postman/cURL. Server-side validation is the true defensive barrier.*
- **Q: Why use `fputcsv()` instead of `fwrite()` with commas?**  
  *`fputcsv()` automatically handles escaping of strings containing quotes, commas, or line breaks according to RFC 4180.*
