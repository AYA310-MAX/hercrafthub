// ── HerCraft Hub – Main JavaScript ──

// ── Password toggle ──
document.getElementById('togglePass')?.addEventListener('click', function() {
  const pwd = document.getElementById('password');
  pwd.type  = pwd.type === 'password' ? 'text' : 'password';
  this.textContent = pwd.type === 'password' ? '👁' : '🙈';
});

// ── Password strength indicator ──
document.getElementById('password')?.addEventListener('input', function() {
  const bar  = document.getElementById('strength-bar');
  const text = document.getElementById('strength-text');
  if (!bar) return;

  let strength = 0;
  if (this.value.length >= 6)               strength++;
  if (/[A-Z]/.test(this.value))             strength++;
  if (/[0-9]/.test(this.value))             strength++;
  if (/[^A-Za-z0-9]/.test(this.value))      strength++;

  const levels = [
    { w:'25%', bg:'#e74c3c', label:'Weak'     },
    { w:'50%', bg:'#e67e22', label:'Fair'      },
    { w:'75%', bg:'#f1c40f', label:'Good'      },
    { w:'100%',bg:'#2ecc71', label:'Strong ✓'  },
  ];
  const lvl = levels[strength - 1] || { w:'0%', bg:'transparent', label:'' };
  bar.style.width      = lvl.w;
  bar.style.background = lvl.bg;
  text.textContent     = lvl.label;
});

// ── Password match checker ──
document.getElementById('confirm_password')?.addEventListener('input', function() {
  const pwd = document.getElementById('password').value;
  const msg = document.getElementById('match-msg');
  if (!msg) return;
  msg.textContent = this.value === pwd ? '✓ Passwords match' : '✗ Passwords do not match';
  msg.style.color = this.value === pwd ? '#2ecc71' : '#e74c3c';
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
scrollBtn.innerHTML   = '↑';
scrollBtn.title       = 'Back to top';
scrollBtn.style.cssText = `
  position:fixed; bottom:28px; right:28px;
  width:44px; height:44px; border-radius:50%;
  background:linear-gradient(135deg,#6B2D8B,#E91E8C);
  color:white; font-size:1.2rem; border:none;
  cursor:pointer; display:none; z-index:999;
  box-shadow:0 4px 15px rgba(107,45,139,0.35);
  transition:opacity 0.3s;
`;
document.body.appendChild(scrollBtn);

window.addEventListener('scroll', () => {
  scrollBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
});
scrollBtn.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ── Active nav link highlight ──
const current = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-link').forEach(link => {
  if (link.getAttribute('href') === current) {
    link.style.color      = '#6B2D8B';
    link.style.fontWeight = '700';
  }
});