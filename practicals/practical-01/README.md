# Practical 01: Project Initiation, Requirement Analysis, Sitemap, Wireframe, and GitHub Setup

**Course:** ITUE203 - Web Development Frameworks  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Faculty:** FTE / CSPIT / DEPSTAR  
**Semester:** 3rd Semester, 2026-27 (Odd)  
**CO Mapping:** CO1  

---

## 1. Problem Definition
Initiate the semester-long **StudentHub** portal by identifying the problem scope, user roles, key modules, navigation flow, and minimum 10 pages. Create a sitemap, low-fidelity wireframe, project folder structure, README file, and GitHub repository.

---

## 2. Key Questions & Conceptual Answers

### Q1: What is a URL and what are the parts of a URL?
A **Uniform Resource Locator (URL)** is the global address of documents and other resources on the World Wide Web.
A typical URL consists of:
1. **Scheme / Protocol:** `http://` or `https://` (specifies how data is transferred).
2. **Subdomain:** `www.` or `portal.`
3. **Domain Name / Host:** `charusat.edu.in` (identifies server IP via DNS).
4. **Port Number (Optional):** `:80` (HTTP) or `:443` (HTTPS) or `:8080` (local dev).
5. **Path:** `/studenthub/events.php` (directory and file location on server).
6. **Query String:** `?category=workshop&sort=date` (key-value parameters sent to server).
7. **Fragment / Anchor:** `#main-content` (internal bookmark for browser navigation).

### Q2: How is an HTML file processed in a web browser?
1. **Request & Response:** Browser requests HTML resource via HTTP/HTTPS; web server returns raw bytes.
2. **Tokenization & Parsing:** Browser converts raw bytes into characters, tokens, and nodes.
3. **DOM Construction:** Creates the **Document Object Model (DOM)** tree.
4. **CSSOM Construction:** Parses linked stylesheets to build the **CSS Object Model (CSSOM)** tree.
5. **Render Tree:** Combines DOM and CSSOM to compute visual geometry (Layout / Reflow).
6. **Painting:** Fills pixels on screen (rasterization) and handles GPU composition.

### Q3: How will page navigation flow be managed among all HTML pages?
Page navigation is managed centrally through a reusable header and footer component containing semantic `<nav>` elements, accessible hyperlinks with relative URI resolution, and active-state indicators based on current script name.

### Q4: How will GitHub commits be maintained after each practical?
We maintain a clean Git repository where each practical corresponds to a feature branch or dedicated commit following standard Conventional Commits:
- `feat(p01): initiate studenthub sitemap and wireframes`
- `feat(p02): add semantic html5 skeletons and accessibility skip links`
- `feat(p05): add regex form validation and password strength meter`
- `feat(p10): implement secure session auth and rbac`

---

## 3. Project Architecture & Minimum 10 Pages Sitemap

```
StudentHub Portal
├── Public Accessible Pages
│   ├── index.php (Home Page)
│   ├── about.php (About Portal & Syllabus)
│   ├── events.php (Campus Events & Workshops)
│   ├── faq.php (Knowledge Base & Collapsible FAQs)
│   ├── contact.php (Contact Form with CSV/JSON storage)
│   ├── feedback.php (Academic Feedback Form)
│   ├── register.php (Registration with Regex Validation)
│   └── login.php (Authentication & Session Regeneration)
├── Protected Student Pages
│   ├── dashboard.php (Personalized Student Dashboard)
│   └── profile.php (Student Profile Information)
└── Protected Admin Pages
    └── admin.php (Admin Control Center, Metrics, CRUD, File Uploads)
```

---

## 4. User Roles Defined
1. **Guest / Visitor:** Can browse events, read FAQs, contact support, submit feedback, and register.
2. **Student User:** Authenticated role; can access student dashboard, view enrolled events, download passes, and manage personal profile.
3. **Administrator:** Privileged role; access to system dashboard metrics, student CRUD management, event creation with poster uploads, and audit log inspection.

---

## 5. Low-Fidelity Wireframe Diagram

```
+-----------------------------------------------------------------------+
|  🎓 StudentHub [CHARUSAT]      Home  About  Events  FAQ  Contact  [Login]|
+-----------------------------------------------------------------------+
|                                                                       |
|   WELCOME TO STUDENTHUB - CHARUSAT CAMPUS PORTAL                      |
|   Centralized platform for students, event tracking & collaboration    |
|   [Join StudentHub]   [Explore Events]                                |
|                                                                       |
+-----------------------------------------------------------------------+
|  [ Metric: 1500+ Students ] [ Metric: 30+ Events ] [ Metric: 15 Practicals ]
+-----------------------------------------------------------------------+
|  Featured Events & Announcements (Card Grid)                          |
|  +--------------------+  +--------------------+  +------------------+ |
|  | Tech Fest 2026     |  | Web Bootcamp       |  | AI Hackathon     | |
|  | Date: 15 Nov       |  | Date: 20 Nov       |  | Date: 05 Dec     | |
|  | [Register Button]  |  | [Register Button]  |  | [Register Button]| |
|  +--------------------+  +--------------------+  +------------------+ |
+-----------------------------------------------------------------------+
|  Footer: CHARUSAT Campus, Changa - 388421 | (C) 2026 All Rights Reserved |
+-----------------------------------------------------------------------+
```

---

## 6. Viva Questions & Key Takeaways
- **Q: What is the difference between Git and GitHub?**  
  *Git is a distributed version control tool running locally; GitHub is a cloud-based hosting platform for Git repositories with collaboration features.*
- **Q: Why is folder structuring important in web projects?**  
  *Separates presentation (`assets/css`), logic (`includes/`), data (`data/`, `database/`), and endpoints (`api/`), improving maintainability and security.*
