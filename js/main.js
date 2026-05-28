// HerCraft Hub – main JavaScript

// ── Password toggle (show/hide) ──
document.getElementById('togglePass')?.addEventListener('click', function() {
  const pwd = document.getElementById('password');
  pwd.type = pwd.type === 'password' ? 'text' : 'password';
  this.textContent = pwd.type === 'password' ? '👁' : '🙈';
});

// ── Password strength indicator ──
document.getElementById('password')?.addEventListener('input', function() {
  const val = this.value;
  const bar  = document.getElementById('strength-bar');
  const text = document.getElementById('strength-text');
  if (!bar) return;

  let strength = 0;
  if (val.length >= 6)              strength++;
  if (/[A-Z]/.test(val))            strength++;
  if (/[0-9]/.test(val))            strength++;
  if (/[^A-Za-z0-9]/.test(val))     strength++;

  const levels = [
    { w: '25%', bg: '#e74c3c', label: 'Weak' },
    { w: '50%', bg: '#e67e22', label: 'Fair' },
    { w: '75%', bg: '#f1c40f', label: 'Good' },
    { w: '100%',bg: '#2ecc71', label: 'Strong ✓' },
  ];
  const lvl = levels[strength - 1] || { w: '0%', bg: 'transparent', label: '' };
  bar.style.width      = lvl.w;
  bar.style.background = lvl.bg;
  text.textContent     = lvl.label;
});

// ── Password match checker ──
document.getElementById('confirm_password')?.addEventListener('input', function() {
  const pwd  = document.getElementById('password').value;
  const msg  = document.getElementById('match-msg');
  if (!msg) return;
  if (this.value === pwd) {
    msg.textContent  = '✓ Passwords match';
    msg.style.color  = '#2ecc71';
  } else {
    msg.textContent  = '✗ Passwords do not match';
    msg.style.color  = '#e74c3c';
  }
});