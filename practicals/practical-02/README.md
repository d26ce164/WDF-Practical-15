# Practical 02: Semantic HTML5 Pages with Accessibility-Ready Structure

**Course:** ITUE203 - Web Development Frameworks  
**CO Mapping:** CO1, CO2  
**Total Hours:** 4 Hours  

---

## 1. Problem Definition
Develop static HTML5 skeletons for at least 10 StudentHub pages such as Home, About, Register, Login, Dashboard, Events, Profile, Contact, Admin, FAQ, and Feedback. Use semantic tags and accessibility-friendly structure.

---

## 2. Key Questions & Conceptual Answers

### Q1: Are semantic tags such as `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, and `<footer>` used properly?
Yes. Unlike non-semantic `<div>` and `<span>` tags which convey no meaning about their contents, semantic tags provide explicit machine-readable context to browsers, screen readers, and search engine crawlers:
- `<header>`: Encapsulates introductory branding, logo, and primary navigational links.
- `<nav>`: Wraps primary navigation menus with `aria-label="Primary Navigation"`.
- `<main id="main-content">`: Outlines the unique central core content of the document.
- `<section>`: Groups thematically related content (e.g. Hero Section, Feature Statistics).
- `<article>`: Self-contained composition such as campus event cards or blog entries.
- `<footer>`: Closing site metadata, course attribution, and legal disclaimers.

### Q2: Are form labels, headings, image alt text, and navigation links accessible?
- **Form Controls:** Every `<input>`, `<select>`, and `<textarea>` is explicitly associated with an `<label for="id">`.
- **Heading Hierarchy:** Strictly maintains structured `<h1>` (single primary page heading) followed by nested `<h2>` and `<h3>` tags without skipping levels.
- **Skip Link:** Includes `.skip-link` allowing keyboard users to bypass repetitive header navigation using `Tab` key.
- **Color Contrast:** Conforms to WCAG AA standard with high-contrast text and background colors in both light and dark themes.

### Q3: Is the page structure consistent across all pages?
Yes, common header and footer partials standardize the DOM hierarchy across all 10+ pages in the portal.

---

## 3. WCAG Accessibility Checklist Verified

- [x] Skip-to-content link present (`<a href="#main-content" class="skip-link">`)
- [x] Correct heading hierarchy (`h1` -> `h2` -> `h3`)
- [x] All form inputs possess associated `<label>` with matching `for` and `id`
- [x] ARIA attributes where needed (`aria-label`, `aria-expanded`)
- [x] Keyboard navigability across all links and buttons (`Tab` and `Shift+Tab`)
- [x] Semantic HTML5 layout tags instead of unsemantic `<div>` soup

---

## 4. Viva Questions
- **Q: Why is semantic HTML preferred over plain `<div>` tags?**  
  *Improves accessibility for screen readers, enhances SEO indexing, and provides clearer code readability.*
- **Q: What is the purpose of a skip-to-content link?**  
  *Allows users with motor disabilities and keyboard-only navigation to skip straight to the main page content without tabbing through every header navigation link.*
