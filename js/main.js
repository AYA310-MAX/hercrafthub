// ── HerCraft Hub — Main JavaScript ──

// ── Dark mode ──
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

// ── Password toggle ──
document.getElementById('togglePass')?.addEventListener('click', function() {
  const pwd = document.getElementById('password');
  pwd.type  = pwd.type === 'password' ? 'text' : 'password';
  const icon = this.querySelector('i');
  if (icon) icon.className = pwd.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
});

// ── Password strength indicator ──
document.getElementById('password')?.addEventListener('input', function() {
  const bar  = document.getElementById('strength-bar');
  const text = document.getElementById('strength-text');
  if (!bar) return;

  let s = 0;
  if (this.value.length >= 6)           s++;
  if (/[A-Z]/.test(this.value))         s++;
  if (/[0-9]/.test(this.value))         s++;
  if (/[^A-Za-z0-9]/.test(this.value))  s++;

  const levels = [
    { w:'25%',  bg:'#8B2A2A', label:'Weak'   },
    { w:'50%',  bg:'#7A4A1A', label:'Fair'   },
    { w:'75%',  bg:'#4A6A2A', label:'Good'   },
    { w:'100%', bg:'#2E5A28', label:'Strong' },
  ];
  const lvl = levels[s - 1] || { w:'0%', bg:'transparent', label:'' };
  bar.style.width      = lvl.w;
  bar.style.background = lvl.bg;
  text.textContent     = lvl.label;
});

// ── Password match checker ──
document.getElementById('confirm_password')?.addEventListener('input', function() {
  const pwd = document.getElementById('password').value;
  const msg = document.getElementById('match-msg');
  if (!msg) return;
  msg.textContent = this.value === pwd ? 'Passwords match' : 'Passwords do not match';
  msg.style.color = this.value === pwd ? '#2E5A28' : '#8B2A2A';
});

// ── Flash message auto dismiss ──
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(a => {
    a.style.transition = 'opacity 0.5s';
    a.style.opacity    = '0';
    setTimeout(() => a.remove(), 500);
  });
}, 3500);

// ── Scroll to top button ──
const scrollBtn = document.createElement('button');
scrollBtn.innerHTML         = '<i class="ti ti-arrow-up"></i>';
scrollBtn.setAttribute('aria-label', 'Back to top');
scrollBtn.style.cssText     = `
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
  if (window.scrollY > 300) {
    scrollBtn.style.display        = 'flex';
    scrollBtn.style.alignItems     = 'center';
    scrollBtn.style.justifyContent = 'center';
  } else {
    scrollBtn.style.display = 'none';
  }
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

// ── Active nav link highlight ──
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-link').forEach(link => {
  if (link.getAttribute('href') === currentPage) {
    link.style.color      = 'var(--purple)';
    link.style.fontWeight = '600';
  }
});

// ── Card tilt effect on hover ──
document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('mousemove', function(e) {
    const rect   = this.getBoundingClientRect();
    const x      = e.clientX - rect.left;
    const y      = e.clientY - rect.top;
    const centerX = rect.width  / 2;
    const centerY = rect.height / 2;
    const rotateX = ((y - centerY) / centerY) * 4;
    const rotateY = ((x - centerX) / centerX) * 4;
    this.style.transform =
      `translateY(-6px) rotateX(${-rotateX}deg) rotateY(${rotateY}deg)`;
  });
  card.addEventListener('mouseleave', function() {
    this.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
  });
});