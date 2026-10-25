# Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO3, CO4, CO5  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Create simple PHP JSON API endpoints for events or students and consume them using Fetch API. Perform list, add, update, and delete operations asynchronously without full page reload.

---

## 2. Key Questions & Conceptual Answers

### Q1: Do PHP endpoints return valid JSON responses?
Yes. Located at `api/events.php`:
- Sets HTTP response header: `header('Content-Type: application/json; charset=utf-8');`.
- Encodes PHP associative arrays into clean JSON with `json_encode($data)`.

### Q2: Are GET, POST, and DELETE requests handled properly?
- **GET `api/events.php?search=hackathon`**: Returns filtered event records.
- **POST `api/events.php`**: Accepts JSON payload, validates, inserts new event, and returns HTTP 201 Created.
- **DELETE `api/events.php?id=5`**: Removes event and returns JSON status.

### Q3: Are loading, success, and error states shown on the frontend?
Yes, on `events.php`:
- `#loading-spinner` displays while awaiting network promise resolution.
- Success and confirmation messages display in dynamic modals and toast notifications (`showToast()`).
- Error messages display cleanly without breaking the surrounding layout.

### Q4: Is input validation performed before database operations?
Yes, backend checks for mandatory title and parameter types before executing MySQLi queries.

---

## 3. Sample API Requests & Responses

### Request: `GET /api/events.php?category=Workshop`
```json
[
  {
    "id": 2,
    "title": "Full Stack Web Bootcamp",
    "category": "Workshop",
    "description": "Hands-on training covering HTML5, CSS3, JavaScript ES6+, PHP...",
    "event_date": "2026-11-20",
    "event_time": "10:00:00",
    "venue": "Lab 301, CSPIT Building",
    "capacity": 60,
    "status": "upcoming"
  }
]
```

---

## 4. Viva Questions
- **Q: What is the benefit of Single-Page Application (SPA) / AJAX behavior?**  
  *Eliminates screen flicker, preserves user form and scroll state, saves network bandwidth, and delivers a desktop-like responsiveness.*
- **Q: What are CORS headers and why are they needed?**  
  *Cross-Origin Resource Sharing (CORS) headers tell the browser whether resources from a domain can be requested by frontend scripts running on a different domain.*
