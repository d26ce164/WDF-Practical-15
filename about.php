<?php
$page_title = "About StudentHub & Course Syllabus";
require_once __DIR__ . '/includes/header.php';
?>

<section style="margin-bottom: 2rem;">
  <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">About StudentHub</h1>
  <p style="font-size: 1.05rem; color: var(--text-muted); margin-bottom: 1.5rem;">
    StudentHub is a comprehensive semester project engineered under <strong>ITUE203: Web Development Frameworks</strong> at CHARUSAT, Faculty of Technology & Engineering.
  </p>

  <div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.25rem; color: var(--secondary-color); margin-bottom: 0.75rem;">Academic Context & Objectives</h2>
    <p style="margin-bottom: 1rem;">
      The objective of this portal is to transition students from raw HTML/CSS fundamentals into production-ready web application development with full-stack capabilities, security hardening, database integration, and modern asynchronous frontend paradigms.
    </p>
    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
      <div>
        <strong>Course Code:</strong> ITUE203<br>
        <strong>Semester:</strong> 3rd Semester, 2026-27 (Odd)
      </div>
      <div>
        <strong>Departments:</strong> CSPIT (CE/IT/CSE/AIML), DEPSTAR (CE/CSE/IT)<br>
        <strong>University:</strong> CHARUSAT, Changa
      </div>
    </div>
  </div>

  <h2 style="color: var(--primary-color); margin-bottom: 1rem;">15 Practical Modules Implemented</h2>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Practical No.</th>
          <th>Title</th>
          <th>Core Technology & Concept</th>
          <th>CO Mapping</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>01</td><td>Project Initiation, Sitemap, Wireframe</td><td>Requirements, Wireframes, Git/GitHub</td><td>CO1</td></tr>
        <tr><td>02</td><td>Semantic HTML5 Pages</td><td>Semantic Tags, Accessibility (WCAG), Skip-Links</td><td>CO1, CO2</td></tr>
        <tr><td>03</td><td>Responsive UI Design</td><td>CSS Grid, Flexbox, Mobile-first Responsive Breakpoints</td><td>CO2</td></tr>
        <tr><td>04</td><td>JavaScript DOM & Interactivity</td><td>DOM Manipulation, Event Handling, LocalStorage Theme</td><td>CO3, CO4</td></tr>
        <tr><td>05</td><td>Registration Form Validation</td><td>Regex Validation, Password Strength Meter, Inline Errors</td><td>CO1, CO3</td></tr>
        <tr><td>06</td><td>External JSON Rendering</td><td>Fetch API, Filtering, Sorting, Client-Side Pagination</td><td>CO3, CO4</td></tr>
        <tr><td>07</td><td>PHP Server-Side Processing</td><td>Form POST Sanitization, CSV & JSON File Storage</td><td>CO1, CO5</td></tr>
        <tr><td>08</td><td>MySQL Schema & DB Connectivity</td><td>Relational Schema Design, Foreign Keys, MySQLi Connect</td><td>CO1, CO5</td></tr>
        <tr><td>09</td><td>Secure Registration & Password Hashing</td><td>Duplicate Checks, BCRYPT Hashing, Prepared Statements</td><td>CO1, CO3, CO5</td></tr>
        <tr><td>10</td><td>Secure Authentication & RBAC</td><td>Session Management, 15m Timeout, Remember Tokens</td><td>CO1, CO5</td></tr>
        <tr><td>11</td><td>Student Management CRUD</td><td>Full CRUD, Live Search, Multi-Filter, Confirm Modal</td><td>CO1, CO4, CO5</td></tr>
        <tr><td>12</td><td>Event CRUD & Poster Upload</td><td>Validated File Uploads, MIME Checking, Directory Safety</td><td>CO1, CO4, CO5</td></tr>
        <tr><td>13</td><td>PHP JSON API & Asynchronous Fetch CRUD</td><td>RESTful JSON Endpoints, Zero Full-Page Reloads</td><td>CO3, CO4, CO5</td></tr>
        <tr><td>14</td><td>Admin Dashboard & Audit Logging</td><td>Aggregated Metrics, Activity Logs, Dynamic Role Menus</td><td>CO1, CO4, CO5</td></tr>
        <tr><td>15</td><td>Full Integration & Deployment</td><td>End-to-End System Integration, Testing & Documentation</td><td>CO2, CO4, CO5</td></tr>
      </tbody>
    </table>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
