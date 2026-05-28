// HerCraft Hub – Sell page JavaScript

// ── Character counters ──
document.querySelector('[name="title"]')?.addEventListener('input', function() {
  document.getElementById('titleCount').textContent = this.value.length;
});

document.querySelector('[name="description"]')?.addEventListener('input', function() {
  document.getElementById('descCount').textContent = this.value.length;
});

// ── Image preview before upload ──
document.getElementById('imageInput')?.addEventListener('change', function() {
  const file    = this.files[0];
  const preview = document.getElementById('imagePreview');
  const img     = document.getElementById('previewImg');

  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      preview.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  } else {
    preview.classList.add('d-none');
  }
});