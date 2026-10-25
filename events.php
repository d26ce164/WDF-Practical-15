<?php
$page_title = "Campus Events & Registration";
require_once __DIR__ . '/includes/header.php';
?>

<section style="margin-bottom: 2rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
    <div>
      <h1 style="color: var(--primary-color); margin-bottom: 0.25rem;">Campus Events & Workshops</h1>
      <p style="color: var(--text-muted);">Explore technical fests, hackathons, seminars, and student competitions.</p>
    </div>
    <div>
      <span class="brand-badge">Campus Events</span>
    </div>
  </div>

  <!-- Search, Filter & Controls Bar -->
  <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: center;">
      <div>
        <label for="event-search" class="form-label">Search Events</label>
        <input type="text" id="event-search" class="form-control" placeholder="Search by title, keyword, or venue...">
      </div>
      <div>
        <label for="event-category" class="form-label">Filter Category</label>
        <select id="event-category" class="form-control">
          <option value="all">All Categories</option>
          <option value="Festival">Festival</option>
          <option value="Workshop">Workshop</option>
          <option value="Hackathon">Hackathon</option>
          <option value="Seminar">Seminar</option>
          <option value="Competition">Competition</option>
        </select>
      </div>
      <div>
        <label for="event-sort" class="form-label">Sort By</label>
        <select id="event-sort" class="form-control">
          <option value="date-asc">Date (Earliest First)</option>
          <option value="date-desc">Date (Latest First)</option>
          <option value="title-asc">Title (A-Z)</option>
          <option value="capacity-desc">Capacity (High to Low)</option>
        </select>
      </div>
      <div>
        <label for="items-per-page" class="form-label">Show per page</label>
        <select id="items-per-page" class="form-control">
          <option value="6">6 per page</option>
          <option value="9">9 per page</option>
          <option value="15">All</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Loading State -->
  <div id="loading-spinner" style="text-align: center; padding: 3rem 0; display: none;">
    <p style="font-size: 1.1rem; color: var(--text-muted);">⏳ Loading campus events data...</p>
  </div>

  <!-- Events Container Grid -->
  <div id="events-grid" class="card-grid"></div>

  <!-- Pagination Controls -->
  <div id="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;"></div>
</section>

<!-- Register Event Modal -->
<div id="eventModal" class="modal-backdrop">
  <div class="modal-content">
    <h3 id="modalEventTitle" style="color: var(--primary-color); margin-bottom: 0.5rem;">Event Title</h3>
    <p id="modalEventDetails" style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;"></p>
    <div id="modalRegistrationMessage" style="margin-bottom: 1rem;"></div>

    <form id="eventRegistrationForm" onsubmit="handleEventRegistration(event)">
      <input type="hidden" id="modalEventId" name="event_id">
      <div class="form-group">
        <label class="form-label" for="regStudentName">Full Name</label>
        <input type="text" id="regStudentName" class="form-control" required placeholder="Enter student full name">
      </div>
      <div class="form-group">
        <label class="form-label" for="regStudentEmail">University Email</label>
        <input type="email" id="regStudentEmail" class="form-control" required placeholder="id@charusat.edu.in">
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="button" class="btn btn-outline" onclick="closeModal('eventModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Confirm Registration</button>
      </div>
    </form>
  </div>
</div>

<script>
let allEvents = [];
let currentPage = 1;
let pageSize = 6;

document.addEventListener('DOMContentLoaded', () => {
  fetchEvents();

  document.getElementById('event-search').addEventListener('input', () => {
    currentPage = 1;
    applyFiltersAndRender();
  });
  document.getElementById('event-category').addEventListener('change', () => {
    currentPage = 1;
    applyFiltersAndRender();
  });
  document.getElementById('event-sort').addEventListener('change', () => {
    currentPage = 1;
    applyFiltersAndRender();
  });
  document.getElementById('items-per-page').addEventListener('change', (e) => {
    pageSize = parseInt(e.target.value);
    currentPage = 1;
    applyFiltersAndRender();
  });
});

async function fetchEvents() {
  const spinner = document.getElementById('loading-spinner');
  spinner.style.display = 'block';
  try {
    // Try fetching from API first, fallback to JSON
    let response;
    try {
      response = await fetch('api/events.php');
      if (!response.ok) throw new Error('API unavailable');
    } catch(err) {
      response = await fetch('data/events.json');
    }
    const data = await response.json();
    allEvents = Array.isArray(data) ? data : (data.events || []);
    // Cache in localStorage for offline demonstration (Practical 6 advanced)
    localStorage.setItem('cached_studenthub_events', JSON.stringify(allEvents));
  } catch (error) {
    console.warn('Network fetch error, loading from local cache if present:', error);
    const cached = localStorage.getItem('cached_studenthub_events');
    if (cached) {
      allEvents = JSON.parse(cached);
    } else {
      document.getElementById('events-grid').innerHTML = `<p class="alert alert-danger">Error loading events. Please ensure web server is running.</p>`;
    }
  } finally {
    spinner.style.display = 'none';
    applyFiltersAndRender();
  }
}

function applyFiltersAndRender() {
  const searchTerm = document.getElementById('event-search').value.toLowerCase().trim();
  const selectedCategory = document.getElementById('event-category').value;
  const sortCriteria = document.getElementById('event-sort').value;

  // Filter
  let filtered = allEvents.filter(ev => {
    const matchesSearch = ev.title.toLowerCase().includes(searchTerm) || 
                          (ev.venue && ev.venue.toLowerCase().includes(searchTerm)) ||
                          (ev.description && ev.description.toLowerCase().includes(searchTerm));
    const matchesCategory = selectedCategory === 'all' || ev.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  // Sort
  filtered.sort((a, b) => {
    if (sortCriteria === 'date-asc') return new Date(a.date || a.event_date) - new Date(b.date || b.event_date);
    if (sortCriteria === 'date-desc') return new Date(b.date || b.event_date) - new Date(a.date || a.event_date);
    if (sortCriteria === 'title-asc') return a.title.localeCompare(b.title);
    if (sortCriteria === 'capacity-desc') return (b.capacity || 0) - (a.capacity || 0);
    return 0;
  });

  // Pagination calculation
  const totalItems = filtered.length;
  const totalPages = Math.ceil(totalItems / pageSize) || 1;
  if (currentPage > totalPages) currentPage = totalPages;

  const startIndex = (currentPage - 1) * pageSize;
  const paginatedItems = filtered.slice(startIndex, startIndex + pageSize);

  renderEventsGrid(paginatedItems);
  renderPagination(totalPages);
}

function renderEventsGrid(events) {
  const container = document.getElementById('events-grid');
  if (events.length === 0) {
    container.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
      <p style="color: var(--text-muted); font-size: 1.1rem;">No events match your search or filter criteria.</p>
    </div>`;
    return;
  }

  container.innerHTML = events.map(ev => `
    <article class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
          <span class="brand-badge" style="background: var(--primary-color);">${ev.category || 'General'}</span>
          <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">📅 ${ev.date || ev.event_date}</span>
        </div>
        <h3 style="margin-bottom: 0.5rem; font-size: 1.15rem; color: var(--text-main);">${escapeHtml(ev.title)}</h3>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1rem; line-height: 1.5;">
          ${escapeHtml((ev.description || '').substring(0, 110))}...
        </p>
      </div>
      <div>
        <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1rem; border-top: 1px dashed var(--border-color); padding-top: 0.5rem;">
          <div>📍 <strong>Venue:</strong> ${escapeHtml(ev.venue || 'Campus Hall')}</div>
          <div>👥 <strong>Seats:</strong> ${ev.capacity || 'Open'} available</div>
        </div>
        <button class="btn btn-outline btn-sm" style="width: 100%;" onclick="openRegisterModalFromButton(this)" data-event-id="${Number(ev.id)}" data-event-title="${escapeHtml(ev.title || 'Event')}" data-event-date="${escapeHtml(ev.date || ev.event_date || '')}" data-event-venue="${escapeHtml(ev.venue || '')}">
          Register for Event
        </button>
      </div>
    </article>
  `).join('');
}

function renderPagination(totalPages) {
  const container = document.getElementById('pagination-controls');
  if (totalPages <= 1) {
    container.innerHTML = '';
    return;
  }

  let html = `<button class="btn btn-outline btn-sm" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Prev</button>`;
  for (let i = 1; i <= totalPages; i++) {
    html += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-outline'} btn-sm" onclick="changePage(${i})">${i}</button>`;
  }
  html += `<button class="btn btn-outline btn-sm" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>`;
  container.innerHTML = html;
}

function changePage(page) {
  currentPage = page;
  applyFiltersAndRender();
  window.scrollTo({ top: 300, behavior: 'smooth' });
}

function openRegisterModalFromButton(button) {
  openRegisterModal(Number(button.dataset.eventId), button.dataset.eventTitle || 'Event', button.dataset.eventDate || '', button.dataset.eventVenue || '');
}

function openRegisterModal(id, title, date, venue) {
  document.getElementById('modalEventId').value = id;
  document.getElementById('modalEventTitle').textContent = title;
  document.getElementById('modalEventDetails').textContent = `📅 Date: ${date} | 📍 Venue: ${venue}`;
  document.getElementById('modalRegistrationMessage').innerHTML = '';
  openModal('eventModal');
}

async function handleEventRegistration(e) {
  e.preventDefault();
  const msgBox = document.getElementById('modalRegistrationMessage');
  const submitButton = document.querySelector('#eventRegistrationForm button[type="submit"]');
  submitButton.disabled = true;
  msgBox.textContent = 'Saving your registration…';
  try {
    const response = await fetch('api/register_event.php', {
      method: 'POST', headers: {'Content-Type':'application/json'},
      body: JSON.stringify({event_id: Number(document.getElementById('modalEventId').value), name: document.getElementById('regStudentName').value.trim(), email: document.getElementById('regStudentEmail').value.trim()})
    });
    const result = await response.json();
    msgBox.innerHTML = `<div class="alert ${result.success ? 'alert-success' : 'alert-danger'}">${escapeHtml(result.message || 'Registration failed.')}</div>`;
    if (result.success) setTimeout(() => { closeModal('eventModal'); showToast('Registered successfully for event!', 'success'); document.getElementById('eventRegistrationForm').reset(); }, 1200);
  } catch (error) { msgBox.innerHTML = '<div class="alert alert-danger">Could not contact the server. Please try again.</div>'; }
  finally { submitButton.disabled = false; }
}

function escapeHtml(string) {
  if (!string) return '';
  return String(string).replace(/[&<>"']/g, function(m) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
