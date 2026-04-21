/* ====================================================================
   Revio · Shared client logic
   - Theme toggle
   - Compare state (localStorage)
   - Product catalogue (used by compare.html)
   ==================================================================== */

const STORAGE_KEY = 'revio.compare';

const IMG = (seed, w = 800, h = 600) => `https://picsum.photos/seed/${seed}/${w}/${h}`;

const PRODUCTS = {
  'iphone-17-pro': {
    id: 'iphone-17-pro',
    brand: 'Apple', name: 'iPhone 17 Pro',
    image: IMG('iphone17pro'), price: '$1,099', priceNum: 1099, rating: 4.8,
    category: 'phones', tagline: 'Editor\'s choice',
    specs: {
      'Chipset': 'Apple A19 Pro',
      'RAM': '12 GB',
      'Storage': '128/256/512GB · 1TB',
      'Display': '6.3″ LTPO · 120Hz',
      'Resolution': '2622 × 1206',
      'Peak brightness': '3,000 nits',
      'Main camera': '48MP ƒ/1.7 · OIS',
      'Ultrawide': '48MP ƒ/2.0',
      'Telephoto': '12MP · 5× optical',
      'Front camera': '12MP TrueDepth',
      'Battery': '3,650 mAh',
      'Charging': '30W wired · 25W MagSafe',
      'Weight': '199 g',
      'IP rating': 'IP68',
      'Connectivity': '5G · Wi-Fi 7 · BT 6.0',
      'OS': 'iOS 19'
    },
    scores: { performance: 9.6, camera: 9.8, display: 9.4, battery: 8.2, value: 7.8 }
  },
  'iphone-17-pro-max': {
    id: 'iphone-17-pro-max',
    brand: 'Apple', name: 'iPhone 17 Pro Max',
    image: IMG('iphone17promax'), price: '$1,299', priceNum: 1299, rating: 4.7,
    category: 'phones',
    specs: {
      'Chipset': 'Apple A19 Pro',
      'RAM': '12 GB',
      'Storage': '256/512GB · 1TB',
      'Display': '6.9″ LTPO · 120Hz',
      'Resolution': '2868 × 1320',
      'Peak brightness': '3,000 nits',
      'Main camera': '48MP ƒ/1.7 · OIS',
      'Ultrawide': '48MP ƒ/2.0',
      'Telephoto': '12MP · 5× optical',
      'Front camera': '12MP TrueDepth',
      'Battery': '5,100 mAh',
      'Charging': '35W wired · 25W MagSafe',
      'Weight': '227 g',
      'IP rating': 'IP68',
      'Connectivity': '5G · Wi-Fi 7 · BT 6.0',
      'OS': 'iOS 19'
    },
    scores: { performance: 9.6, camera: 9.8, display: 9.5, battery: 9.4, value: 7.5 }
  },
  's26-ultra': {
    id: 's26-ultra',
    brand: 'Samsung', name: 'Galaxy S26 Ultra',
    image: IMG('s26ultra'), price: '$1,299', priceNum: 1299, rating: 4.7,
    category: 'phones',
    specs: {
      'Chipset': 'Snapdragon 8 Gen 5',
      'RAM': '12 GB',
      'Storage': '256/512GB · 1TB',
      'Display': '6.9″ AMOLED · 120Hz',
      'Resolution': '3120 × 1440',
      'Peak brightness': '2,600 nits',
      'Main camera': '200MP ƒ/1.7 · OIS',
      'Ultrawide': '50MP ƒ/1.9',
      'Telephoto': '50MP · 5× optical',
      'Front camera': '12MP',
      'Battery': '5,000 mAh',
      'Charging': '65W wired · 15W wireless',
      'Weight': '232 g',
      'IP rating': 'IP68',
      'Connectivity': '5G · Wi-Fi 7 · BT 5.4',
      'OS': 'Android 16 · One UI 8'
    },
    scores: { performance: 9.4, camera: 9.5, display: 9.7, battery: 9.1, value: 8.1 }
  },
  'pixel-10-pro': {
    id: 'pixel-10-pro',
    brand: 'Google', name: 'Pixel 10 Pro',
    image: IMG('pixel10pro'), price: '$999', priceNum: 999, rating: 4.6,
    category: 'phones',
    specs: {
      'Chipset': 'Tensor G5',
      'RAM': '16 GB',
      'Storage': '128/256/512GB · 1TB',
      'Display': '6.4″ LTPO · 120Hz',
      'Resolution': '2880 × 1344',
      'Peak brightness': '2,400 nits',
      'Main camera': '50MP ƒ/1.6 · OIS',
      'Ultrawide': '48MP ƒ/1.7',
      'Telephoto': '48MP · 5× optical',
      'Front camera': '42MP',
      'Battery': '4,700 mAh',
      'Charging': '30W wired · 23W wireless',
      'Weight': '207 g',
      'IP rating': 'IP68',
      'Connectivity': '5G · Wi-Fi 7 · BT 5.4',
      'OS': 'Android 16'
    },
    scores: { performance: 8.9, camera: 9.7, display: 9.3, battery: 9.0, value: 8.8 }
  },
  'macbook-air-m4': {
    id: 'macbook-air-m4',
    brand: 'Apple', name: 'MacBook Air M4',
    image: IMG('macbookm4'), price: '$1,049', priceNum: 1049, rating: 4.9,
    category: 'laptops',
    specs: {
      'Chipset': 'Apple M4 (10-core CPU / 10-core GPU)',
      'RAM': '16–32 GB unified',
      'Storage': '256 GB – 2 TB SSD',
      'Display': '13.6″ Liquid Retina',
      'Resolution': '2560 × 1664',
      'Battery': '18h video playback',
      'Weight': '1.24 kg',
      'Connectivity': 'Wi-Fi 6E · BT 5.3',
      'Ports': '2× Thunderbolt 4 · MagSafe',
      'OS': 'macOS 15'
    },
    scores: { performance: 9.2, display: 9.1, battery: 9.5, value: 9.0 }
  },
  'model-y': {
    id: 'model-y',
    brand: 'Tesla', name: 'Model Y Juniper',
    image: IMG('modely'), price: '$47,990', priceNum: 47990, rating: 4.5,
    category: 'cars',
    specs: {
      'Powertrain': 'Dual motor AWD',
      'Output': '384 HP',
      'Torque': '340 lb-ft',
      '0–60 mph': '4.1 s',
      'Range (EPA)': '330 mi',
      'Battery': '78 kWh',
      'Charging': '250 kW Supercharger',
      'Cargo': '76 ft³',
      'Seats': '5',
      'Screen': '15.4″'
    },
    scores: { range: 8.8, performance: 8.9, interior: 8.0, value: 8.5 }
  }
};

/* ---------- Theme toggle ---------- */
(function theme() {
  const root = document.documentElement;
  const stored = localStorage.getItem('theme');
  if (stored) root.setAttribute('data-theme', stored);
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#themeBtn');
    if (!btn) return;
    const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
  });
})();

/* ---------- Compare state ---------- */
function getCompare() {
  try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
  catch { return []; }
}
function setCompare(arr) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
  renderDock();
  renderCompareButtons();
}
function addToCompare(id) {
  const arr = getCompare();
  if (arr.includes(id)) return;
  if (arr.length >= 3) {
    flashDock('Max 3 items');
    return;
  }
  arr.push(id);
  setCompare(arr);
  flashDock('Added ' + (PRODUCTS[id]?.name || ''));
}
function removeFromCompare(id) {
  setCompare(getCompare().filter(x => x !== id));
}
function toggleCompare(id) {
  if (getCompare().includes(id)) removeFromCompare(id);
  else addToCompare(id);
}

/* ---------- Compare dock rendering ---------- */
function renderDock() {
  const dock = document.querySelector('.compare-dock');
  if (!dock) return;
  const arr = getCompare();

  const slotsContainer = dock.querySelector('.slots');
  if (slotsContainer) {
    slotsContainer.innerHTML = '';
    for (let i = 0; i < 3; i++) {
      const id = arr[i];
      const slot = document.createElement('div');
      slot.className = 'slot' + (id ? ' filled' : '');
      if (id && PRODUCTS[id]) {
        slot.dataset.id = id;
        slot.title = 'Click to remove ' + PRODUCTS[id].name;
        slot.innerHTML = `<img src="${PRODUCTS[id].image}" alt=""/><span class="slot-x" aria-hidden="true">×</span>`;
      } else {
        slot.innerHTML = '+';
      }
      slotsContainer.appendChild(slot);
    }
  }

  const label = dock.querySelector('.count-label');
  if (label) label.textContent = `${arr.length} item${arr.length === 1 ? '' : 's'} selected`;

  const cta = dock.querySelector('.dock-cta');
  if (cta) {
    cta.classList.toggle('disabled', arr.length < 2);
    cta.href = arr.length >= 2 ? 'compare.html' : '#';
  }
}

function flashDock(msg) {
  const dock = document.querySelector('.compare-dock');
  if (!dock) return;
  const label = dock.querySelector('.count-label');
  if (!label) return;
  const prev = label.textContent;
  label.textContent = msg;
  label.style.color = 'var(--accent-ink)';
  setTimeout(() => {
    label.style.color = '';
    renderDock();
  }, 1200);
}

function renderCompareButtons() {
  const arr = getCompare();
  document.querySelectorAll('[data-compare-id]').forEach(el => {
    const id = el.dataset.compareId;
    const isIn = arr.includes(id);
    el.classList.toggle('in-compare', isIn);
    const lbl = el.querySelector('.compare-label');
    if (lbl) lbl.textContent = isIn ? 'Added to compare' : 'Add to compare';
  });
}

/* ---------- Event delegation ---------- */
document.addEventListener('click', (e) => {
  const cmp = e.target.closest('[data-compare-id]');
  if (cmp) {
    e.preventDefault();
    e.stopPropagation();
    toggleCompare(cmp.dataset.compareId);
    return;
  }
  const slot = e.target.closest('.compare-dock .slot.filled');
  if (slot && slot.dataset.id) {
    e.preventDefault();
    removeFromCompare(slot.dataset.id);
    return;
  }
});

document.addEventListener('DOMContentLoaded', () => {
  renderDock();
  renderCompareButtons();
});
