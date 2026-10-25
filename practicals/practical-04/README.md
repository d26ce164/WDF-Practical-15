# Practical 04: JavaScript DOM Manipulation, Event Handling, and UI Interactivity

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO3, CO4  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Add dynamic UI components such as collapsible FAQ, modal popup, image/content slider, notification banner, hamburger menu, and light/dark theme switcher using JavaScript.

---

## 2. Key Questions & Conceptual Answers

### Q1: How are DOM elements selected and modified?
Elements are selected using standard DOM query methods:
- `document.getElementById('elementId')` for unique IDs.
- `document.querySelectorAll('.class')` for element groups.
- Modifications are made via properties like `.classList.toggle()`, `.setAttribute('data-theme', ...)`, and `.innerHTML` / `.textContent`.

### Q2: Are event listeners attached correctly?
Yes, using `addEventListener('click', ...)` and `addEventListener('input', ...)` wrapped inside `document.addEventListener('DOMContentLoaded', ...)` to guarantee elements exist before listeners are bound.

### Q3: Is `localStorage` used for remembering theme choice?
Yes:
```javascript
// On page load:
const currentTheme = localStorage.getItem('studenthub_theme') || 'light';
document.documentElement.setAttribute('data-theme', currentTheme);

// On user toggle:
localStorage.setItem('studenthub_theme', target);
```
When a student refreshes or navigates across pages, their dark/light preference persists automatically.

### Q4: Does interactivity improve usability without breaking accessibility?
Yes:
- ARIA states like `aria-expanded="true/false"` are toggled on accordions.
- Focus is trapped or managed when modals are launched.
- Keyboard users can trigger buttons with `Enter` and `Space`.

---

## 3. Implemented Components
1. **Light / Dark Mode Switcher:** Saved in `localStorage`.
2. **Mobile Hamburger Menu:** Toggles `.show` CSS class on navigation drawer.
3. **Collapsible FAQ Accordion:** Single-open accordion behaviour with rotation animations.
4. **Modal Dialogs:** Event registration modal and Admin forms with backdrop dismiss.
5. **Toast Notifications:** Dynamic non-blocking feedback popups (`showToast()`).

---

## 4. Viva Questions
- **Q: What is the difference between `localStorage` and `sessionStorage`?**  
  *`localStorage` persists data with no expiration time even after browser restart; `sessionStorage` clears data when the specific browser tab is closed.*
- **Q: What is event delegation?**  
  *Attaching a single event listener to a parent element to handle events on current or future child elements via event bubbling.*
