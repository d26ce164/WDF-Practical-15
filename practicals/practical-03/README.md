# Practical 03: Responsive UI Design using CSS Grid, Flexbox, and Bootstrap/Tailwind

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO2  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Design responsive layouts for Home, About, Registration, Dashboard, and Events pages using CSS Grid and Flexbox. Optional to use CSS framework such as Bootstrap or Tailwind. Ensure mobile-first responsiveness.

---

## 2. Key Questions & Conceptual Answers

### Q1: How does the layout adapt for mobile, tablet, and desktop screens?
We use a **Mobile-First** CSS methodology with fluid units, CSS Custom Properties (Variables), and Media Queries:
- **Mobile Screens (`< 768px`):**
  - Navigation switches to a collapsible drawer with a hamburger menu button.
  - Multi-column form fields and card grids collapse into a single vertical column (`grid-template-columns: 1fr`).
- **Desktop & Widescreen (`>= 768px`):**
  - Navigation menu expands horizontally across the header bar.
  - Cards arrange into multi-column CSS grids (`grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))`).

### Q2: Which layout technique is used and why (CSS Grid vs Flexbox)?
- **CSS Flexbox:** Applied for **1-dimensional** alignment tasks:
  - Header navbar alignment (`justify-content: space-between`).
  - Button groups and action toolbars.
  - Centering modals and dialog elements.
- **CSS Grid:** Applied for **2-dimensional** structured layouts:
  - Event card galleries and catalog views (`repeat(auto-fill, minmax(300px, 1fr))`).
  - Statistics metric dashboard rows.
  - Complex form split-columns.

### Q3: Are typography, spacing, colors, and contrast readable?
Yes, using an accessible color palette with `--primary-color: #0f4c81` and high-contrast background surfaces (`#f8fafc` light, `#0f172a` dark) meeting WCAG AA requirements (> 4.5:1 ratio).

### Q4: Is the framework used consistently without unnecessary inline CSS?
Centralized stylesheet located at `assets/css/main.css` organizes styles into typography, components, layouts, forms, and responsive breakpoints.

---

## 3. Responsive Breakpoints Reference
- **Mobile (`max-width: 768px`)**: Single column stack, hamburger menu toggled.
- **Tablet / Laptop (`769px - 1024px`)**: 2-column card grids.
- **Desktop (`> 1024px`)**: Full 3-column or 4-column fluid auto-fill grid layout.

---

## 4. Viva Questions
- **Q: What is the main difference between CSS Grid and Flexbox?**  
  *Flexbox is designed for 1-dimensional layouts (a row OR a column), whereas Grid is designed for 2-dimensional layouts (rows AND columns simultaneously).*
- **Q: What does `minmax(300px, 1fr)` do in CSS Grid?**  
  *Ensures each grid column is at least 300px wide, but stretches equally to fill remaining available space.*
