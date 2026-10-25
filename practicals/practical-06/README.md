# Practical 06: Rendering External JSON Data using Fetch API, Search and Filter

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO3, CO4  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Display event lists, student profiles, notices, or FAQs from external JSON files using Fetch API. Implement dynamic rendering, search, filter, sorting, and pagination.

---

## 2. Key Questions & Conceptual Answers

### Q1: How is JSON fetched, parsed, and rendered?
Using the standard asynchronous `fetch()` API with `async/await`:
```javascript
const response = await fetch('data/events.json');
const data = await response.json();
renderEventsGrid(data);
```
The resulting JavaScript array is converted into HTML template literals using `.map()` and injected into the DOM via `.innerHTML`.

### Q2: Which array methods are used for search, filter, sort, and pagination?
- **Filter & Search:** `.filter()` checking substring inclusion via `.toLowerCase().includes()`.
- **Sort:** `.sort((a, b) => new Date(a.date) - new Date(b.date))` or `a.title.localeCompare(b.title)`.
- **Pagination:** `.slice(startIndex, startIndex + pageSize)` with `Math.ceil(total / pageSize)`.

### Q3: How are loading and error states handled?
- A loading spinner element (`#loading-spinner`) is shown during network latency and hidden upon completion (`finally` block).
- Network failures are caught via `try...catch` with graceful fallback to cached data in `localStorage`.

### Q4: How is modularity maintained in JavaScript files?
Functions are cleanly decoupled:
- `fetchEvents()`: Responsible only for data fetching and caching.
- `applyFiltersAndRender()`: Coordinates query states and pagination slicing.
- `renderEventsGrid()`: Pure rendering function generating HTML markup.
- `renderPagination()`: Generates dynamic page numeric buttons.

---

## 3. Seed Datasets Provided
- `data/events.json`: 15 comprehensive campus events.
- `data/students.json`: 15 verified student academic records.
- `data/faqs.json`: 15 campus support questions.

---

## 4. Viva Questions
- **Q: What is the Promise returned by `fetch()`?**  
  *It resolves to a `Response` object representing the response to the request. You must call `.json()` or `.text()` to read the body stream.*
- **Q: How does `Array.prototype.slice()` help in client-side pagination?**  
  *`slice(start, end)` extracts a shallow copy of a portion of an array without modifying the original array.*
