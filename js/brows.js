// HerCraft Hub – Browse page filters

function filterListings() {
  const search   = document.getElementById('searchInput').value.toLowerCase();
  const category = document.getElementById('categoryFilter').value;
  const maxPrice = parseInt(document.getElementById('priceRange').value);
  const sort     = document.getElementById('sortFilter').value;

  let items = Array.from(document.querySelectorAll('.listing-item'));
  let visible = 0;

  items.forEach(item => {
    const name  = item.dataset.name;
    const cat   = item.dataset.category;
    const price = parseInt(item.dataset.price);

    const matchSearch   = name.includes(search);
    const matchCategory = category === '' || cat === category;
    const matchPrice    = price <= maxPrice;

    if (matchSearch && matchCategory && matchPrice) {
      item.style.display = 'block';
      visible++;
    } else {
      item.style.display = 'none';
    }
  });

  // Sort visible items
  const grid = document.getElementById('listingsGrid');
  const visibleItems = items.filter(i => i.style.display !== 'none');

  visibleItems.sort((a, b) => {
    if (sort === 'price-low')  return a.dataset.price - b.dataset.price;
    if (sort === 'price-high') return b.dataset.price - a.dataset.price;
    return 0;
  });
  visibleItems.forEach(item => grid.appendChild(item));

  // Results count
  document.getElementById('resultsCount').textContent =
    visible === 0 ? 'No results' : `Showing ${visible} listing${visible !== 1 ? 's' : ''}`;

  // No results message
  document.getElementById('noResults').classList.toggle('d-none', visible > 0);
}

// Price range live label
document.getElementById('priceRange')?.addEventListener('input', function() {
  document.getElementById('priceLabel').textContent = 'R' + this.value;
  filterListings();
});

// Live search as you type
document.getElementById('searchInput')?.addEventListener('input', filterListings);
document.getElementById('categoryFilter')?.addEventListener('change', filterListings);
document.getElementById('sortFilter')?.addEventListener('change', filterListings);
document.getElementById('applyFilters')?.addEventListener('click', filterListings);

// Clear buttons
function clearAll() {
  document.getElementById('searchInput').value    = '';
  document.getElementById('categoryFilter').value = '';
  document.getElementById('priceRange').value     = '2000';
  document.getElementById('priceLabel').textContent = 'R2000';
  document.getElementById('sortFilter').value     = 'newest';
  filterListings();
}
document.getElementById('clearFilters')?.addEventListener('click',  clearAll);
document.getElementById('clearFilters2')?.addEventListener('click', clearAll);