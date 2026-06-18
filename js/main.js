const toggle = document.getElementById('themeToggle');
const html   = document.documentElement;

function applyTheme(theme) {
  html.setAttribute('data-theme', theme);
  if (toggle) toggle.classList.toggle('dark', theme === 'dark');
}

const saved = localStorage.getItem('hch-theme') || 'light';
applyTheme(saved);

toggle?.addEventListener('click', () => {
  const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  localStorage.setItem('hch-theme', next);
  applyTheme(next);
});

document.querySelectorAll('.btn-toggle-pass').forEach(btn => {
  btn.addEventListener('click', function() {
    const group = this.closest('.input-group');
    const pwd   = group?.querySelector('input[type="password"], input[type="text"]');
    if (!pwd) return;
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    const icon = this.querySelector('i');
    if (icon) icon.className = pwd.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
  });
});

document.getElementById('password')?.addEventListener('input', function() {
  const bar = document.getElementById('strength-bar');
  if (!bar) return;

  let s = 0;
  if (this.value.length >= 6)          s++;
  if (/[A-Z]/.test(this.value))        s++;
  if (/[0-9]/.test(this.value))        s++;
  if (/[^A-Za-z0-9]/.test(this.value)) s++;

  const levels = [
    { w: '25%',  bg: '#8B2A2A' },
    { w: '50%',  bg: '#7A4A1A' },
    { w: '75%',  bg: '#4A6A2A' },
    { w: '100%', bg: '#2E5A28' }
  ];
  const lvl = levels[s - 1] || { w: '0%', bg: 'transparent' };
  bar.style.width      = lvl.w;
  bar.style.background = lvl.bg;
});

document.getElementById('confirm_password')?.addEventListener('input', function() {
  const pwd = document.getElementById('password')?.value || '';
  const msg = document.getElementById('match-msg');
  if (!msg) return;
  if (this.value.length === 0) {
    msg.textContent = '';
    msg.className = 'text-muted';
    return;
  }
  const matches = this.value === pwd;
  msg.textContent = matches ? 'Passwords match' : 'Passwords do not match';
  msg.className = matches ? 'match-valid' : 'match-invalid';
});

setTimeout(() => {
  document.querySelectorAll('.alert').forEach(a => {
    a.style.transition = 'opacity 0.5s';
    a.style.opacity    = '0';
    setTimeout(() => a.remove(), 500);
  });
}, 3500);

const scrollBtn = document.createElement('button');
scrollBtn.innerHTML = '<i class="ti ti-arrow-up"></i>';
scrollBtn.setAttribute('aria-label', 'Back to top');
scrollBtn.style.cssText = `
  position: fixed; bottom: 28px; right: 28px;
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--purple); color: var(--cream);
  font-size: 1.1rem; border: none; cursor: pointer;
  display: none; align-items: center; justify-content: center;
  z-index: 999; box-shadow: var(--shadow);
  transition: var(--transition);
`;
document.body.appendChild(scrollBtn);

window.addEventListener('scroll', () => {
  scrollBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
});

scrollBtn.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

scrollBtn.addEventListener('mouseenter', () => {
  scrollBtn.style.background = 'var(--purple-mid)';
  scrollBtn.style.transform  = 'translateY(-3px)';
});

scrollBtn.addEventListener('mouseleave', () => {
  scrollBtn.style.background = 'var(--purple)';
  scrollBtn.style.transform  = 'translateY(0)';
});

const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-link').forEach(link => {
  if (link.getAttribute('href') === currentPage) {
    link.classList.add('active');
  }
});

document.querySelectorAll('.logout-trigger').forEach(link => {
  link.addEventListener('click', (e) => {
    if (link.getAttribute('href') === '#') {
      e.preventDefault();
    }
  });
});

document.getElementById('wishlistBtn')?.addEventListener('click', async function() {
  const productId = this.dataset.productId;
  if (!productId) return;

  const saved = this.classList.contains('saved');
  const action  = saved ? 'remove' : 'add';
  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('action', action);

  try {
    const response = await fetch('php/wishlist_action.php', {
      method: 'POST',
      body: formData
    });

    if (!response.ok) {
      window.location.href = 'login.php';
      return;
    }

    const icon  = this.querySelector('i');
    const label = this.querySelector('.wishlist-label');

    if (saved) {
      this.classList.remove('saved');
      if (icon) icon.className = 'ti ti-heart me-2';
      if (label) label.textContent = 'Save to Wishlist';
    } else {
      this.classList.add('saved');
      if (icon) icon.className = 'ti ti-heart-filled me-2';
      if (label) label.textContent = 'Saved to Wishlist';
    }
  } catch (error) {
    window.location.href = 'listing.php?id=' + productId;
  }
});
