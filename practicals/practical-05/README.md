# Practical 05: Registration Form with Frontend Validation and User-Friendly Error Handling

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO3  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Create a student registration form with HTML5 input types and JavaScript validation for name, email, mobile number, password, confirm password, course, year, gender, and terms acceptance. Use Regular Expressions for validations.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are correct input types and attributes used?
Yes, semantic HTML5 attributes and types are utilized:
- `type="text"`, `type="email"`, `type="tel"`, `type="password"`.
- Radio buttons for gender selection (`name="gender"`).
- Select dropdowns for course and academic year.
- Attributes: `required`, `placeholder`, `novalidate` (for custom JS validation override).

### Q2: Are validation errors displayed near the relevant fields?
Yes. Dedicated `<span id="...-error" class="error-text"></span>` elements are placed immediately beneath each input field and styled in red (`#ef4444`) with clear, constructive feedback messages.

### Q3: Is password strength checked?
Yes. As the student types (`oninput`), a dynamic strength evaluation function scores the password:
- Length >= 8 characters (+1)
- Contains uppercase letter `[A-Z]` (+1)
- Contains digit `[0-9]` (+1)
- Contains special character `[\W]` (+1)
The strength meter updates a colored horizontal progress bar from Red (Weak) to Orange (Moderate) to Green (Strong 🔒).

### Q4: Is the form accessible using keyboard and screen-reader-friendly labels?
Yes. Every field contains an explicit `<label for="...">` matching the input's `id`.

---

## 3. Regular Expressions Reference Table

| Field | Regular Expression Pattern | Description |
|---|---|---|
| **Username** | `^[a-zA-Z0-9_]{3,20}$` | 3 to 20 alphanumeric characters or underscores |
| **Email** | `^[^\s@]+@[^\s@]+\.[^\s@]+$` | Standard valid email address format |
| **Mobile Number** | `^[0-9]{10}$` | Exactly 10 numeric digits |
| **Password** | `^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$` | Min 8 chars, 1 uppercase, 1 number, 1 symbol |

---

## 4. Test Cases

| Test Case | Input | Expected Output | Status |
|---|---|---|---|
| Invalid Email | `student@` | "Please enter a valid email address." | PASSED |
| Invalid Phone | `98765` (5 digits) | "Phone number must be exactly 10 digits." | PASSED |
| Password Mismatch | Pw: `Pass123!`, Confirm: `Pass456!` | "Passwords do not match." | PASSED |
| Unchecked Terms | Checkbox unchecked | "You must accept the terms before continuing." | PASSED |
| Valid Input | All valid fields + Strong Pw | Form submits successfully without client errors | PASSED |
