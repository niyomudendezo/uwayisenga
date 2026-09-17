/* AgruKrwanda — Main JavaScript */

const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';

// ─── SIDEBAR TOGGLE ───────────────────────────────────────────────────────
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  const sidebar = document.getElementById('sidebar');
  if (window.innerWidth <= 768) {
    sidebar?.classList.toggle('show');
  } else {
    sidebar?.classList.toggle('collapsed');
  }
});

// ─── NOTIFICATIONS ────────────────────────────────────────────────────────
function loadNotifications() {
  fetch('/agrukrwanda/api/notifications')
    .then(r => r.json())
    .then(data => {
      const badge = document.getElementById('notifBadge');
      const list  = document.getElementById('notifList');
      if (!badge || !list) return;

      if (data.unread > 0) {
        badge.textContent = data.unread > 9 ? '9+' : data.unread;
        badge.style.display = 'block';
      } else {
        badge.style.display = 'none';
      }

      if (data.notifications.length === 0) {
        list.innerHTML = '<div class="text-center py-3 text-muted small">No notifications</div>';
        return;
      }

      list.innerHTML = data.notifications.map(n => `
        <div class="notif-item ${n.is_read == 0 ? 'unread' : ''}">
          <div class="fw-semibold">${escHtml(n.title)}</div>
          <div class="text-muted">${escHtml(n.message)}</div>
          <div class="text-muted mt-1" style="font-size:.7rem">${timeAgo(n.created_at)}</div>
        </div>
      `).join('');
    })
    .catch(() => {});
}

document.getElementById('markAllRead')?.addEventListener('click', e => {
  e.preventDefault();
  fetch('/agrukrwanda/api/notifications/read', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
  }).then(() => loadNotifications());
});

// Load notifications on page load and every 60s
if (document.getElementById('notifBtn')) {
  loadNotifications();
  setInterval(loadNotifications, 60000);
}

// ─── UTILITIES ────────────────────────────────────────────────────────────
function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str || ''));
  return d.innerHTML;
}

function timeAgo(datetime) {
  const diff = Math.floor((Date.now() - new Date(datetime)) / 1000);
  if (diff < 60)     return 'Just now';
  if (diff < 3600)   return Math.floor(diff/60) + ' min ago';
  if (diff < 86400)  return Math.floor(diff/3600) + ' hr ago';
  return Math.floor(diff/86400) + ' days ago';
}

function getCsrfToken() {
  return document.querySelector('input[name="_token"]')?.value || '';
}

// ─── FORM VALIDATION ──────────────────────────────────────────────────────
document.querySelectorAll('form[data-validate]').forEach(form => {
  form.addEventListener('submit', e => {
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });
});

// ─── CONFIRM DIALOGS ──────────────────────────────────────────────────────
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', e => {
    if (!confirm(el.dataset.confirm)) e.preventDefault();
  });
});

// ─── AUTO-DISMISS ALERTS ──────────────────────────────────────────────────
setTimeout(() => {
  document.querySelectorAll('.alert.alert-success, .alert.alert-info').forEach(el => {
    el.style.transition = 'opacity .5s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  });
}, 4000);

// ─── DATATABLES INIT ──────────────────────────────────────────────────────
document.querySelectorAll('.datatable').forEach(table => {
  $(table).DataTable({
    pageLength: 15,
    responsive: true,
    language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries' }
  });
});

// ─── COOPERATIVE MEMBER SEARCH ──────────────────────────────────────────
function initCooperativeMemberSearch() {
  const searchInput = document.getElementById('coopMemberSearch');
  const membersTable = document.getElementById('coopMembersTable');
  if (!searchInput || !membersTable) return;

  const memberRows = [...membersTable.querySelectorAll('tbody tr')]
    .filter(row => !row.querySelector('.member-empty'));
  const noResults = document.getElementById('memberNoResults');
  const visibleCount = document.getElementById('memberVisibleCount');
  const countLabel = document.getElementById('memberCountLabel');

  const filterMembers = () => {
    const query = searchInput.value.trim().toLocaleLowerCase();
    let matches = 0;

    memberRows.forEach(row => {
      const searchableText = row.innerText.toLocaleLowerCase();
      const isMatch = query === '' || searchableText.includes(query);
      row.style.display = isMatch ? '' : 'none';
      row.setAttribute('aria-hidden', isMatch ? 'false' : 'true');
      if (isMatch) matches++;
    });

    if (visibleCount) visibleCount.textContent = matches;
    if (countLabel) countLabel.textContent = matches === 1 ? 'member' : 'members';
    noResults?.classList.toggle('show', memberRows.length > 0 && matches === 0);
    membersTable.style.display = memberRows.length > 0 && matches === 0 ? 'none' : '';
  };

  searchInput.addEventListener('input', filterMembers);
  searchInput.addEventListener('search', filterMembers);
  searchInput.addEventListener('keyup', filterMembers);
  filterMembers();
}

initCooperativeMemberSearch();
