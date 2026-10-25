<?php
$page_title = "Frequently Asked Questions";
require_once __DIR__ . '/includes/header.php';
?>

<section style="margin-bottom: 2rem;">
  <div style="text-align: center; max-width: 700px; margin: 0 auto 2.5rem;">
    <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Frequently Asked Questions</h1>
    <p style="color: var(--text-muted);">
      Find quick answers about StudentHub accounts, workshop registration, session policies, and technical support.
    </p>
    <div style="margin-top: 1rem;">
      <span class="brand-badge">Practical 4: Collapsible FAQ & DOM Interaction</span>
    </div>
  </div>

  <div style="max-width: 800px; margin: 0 auto;">
    <!-- Search FAQ -->
    <div class="form-group" style="margin-bottom: 1.5rem;">
      <input type="text" id="faqSearchInput" class="form-control" placeholder="Search questions by keyword..." oninput="filterFaqs()">
    </div>

    <!-- Accordion List -->
    <div id="faqAccordionList" style="display: flex; flex-direction: column; gap: 0.75rem;">
      <!-- Populated dynamically & statically for best SEO/accessibility -->
    </div>
  </div>
</section>

<script>
let faqsData = [];

document.addEventListener('DOMContentLoaded', async () => {
  try {
    const res = await fetch('data/faqs.json');
    faqsData = await res.json();
    renderFaqs(faqsData);
  } catch (e) {
    console.error('Failed to load FAQs:', e);
  }
});

function renderFaqs(list) {
  const container = document.getElementById('faqAccordionList');
  if (!list.length) {
    container.innerHTML = `<p class="alert alert-info">No questions match your query.</p>`;
    return;
  }

  container.innerHTML = list.map((item, index) => `
    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border-color);">
      <button class="accordion-header" style="width: 100%; text-align: left; padding: 1.1rem 1.25rem; background: var(--bg-card); border: none; font-size: 1rem; font-weight: 600; color: var(--text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer;" aria-expanded="false" onclick="toggleAccordion(this)">
        <span>${escapeHtml(item.question)}</span>
        <span class="icon" style="transition: transform 0.2s;">➕</span>
      </button>
      <div class="accordion-body" style="display: none; padding: 1rem 1.25rem; background-color: var(--bg-main); border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.93rem; line-height: 1.6;">
        <p>${escapeHtml(item.answer)}</p>
        <div style="margin-top: 0.5rem;"><span class="brand-badge" style="font-size: 0.7rem; background: var(--border-color); color: var(--text-muted);">${item.category}</span></div>
      </div>
    </div>
  `).join('');
}

function toggleAccordion(btn) {
  const body = btn.nextElementSibling;
  const icon = btn.querySelector('.icon');
  const isCurrentlyOpen = body.style.display === 'block';

  // Close all other open FAQs
  document.querySelectorAll('.accordion-body').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.accordion-header .icon').forEach(i => {
    i.textContent = '➕';
  });
  document.querySelectorAll('.accordion-header').forEach(h => h.setAttribute('aria-expanded', 'false'));

  if (!isCurrentlyOpen) {
    body.style.display = 'block';
    icon.textContent = '➖';
    btn.setAttribute('aria-expanded', 'true');
  }
}

function filterFaqs() {
  const q = document.getElementById('faqSearchInput').value.toLowerCase().trim();
  const filtered = faqsData.filter(f => f.question.toLowerCase().includes(q) || f.answer.toLowerCase().includes(q));
  renderFaqs(filtered);
}

function escapeHtml(string) {
  return String(string).replace(/[&<>"']/g, function(m) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
