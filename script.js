const menuToggle = document.getElementById('menuToggle');
const nav = document.getElementById('nav');
const toast = document.getElementById('toast');
let toastTimer;

menuToggle.addEventListener('click', () => {
  const isOpen = nav.classList.toggle('open');
  menuToggle.setAttribute('aria-expanded', String(isOpen));
  menuToggle.textContent = isOpen ? '✕' : '☰';
});

nav.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    nav.classList.remove('open');
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.textContent = '☰';
  });
});

function showToast(message) {
  toast.textContent = message;
  toast.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
}

const categoryButtons = [...document.querySelectorAll('.category')];
const productCards = [...document.querySelectorAll('.product-card')];
const emptyState = document.getElementById('emptyState');
let activeCategory = 'Semua';
let activeSort = 'popular';

function updateProducts() {
  let visibleCards = productCards.filter(card => {
    const matches = activeCategory === 'Semua' || card.dataset.category === activeCategory;
    card.hidden = !matches;
    return matches;
  });
  visibleCards.sort((a, b) => activeSort === 'newest'
    ? Number(b.dataset.price) - Number(a.dataset.price)
    : Number(a.dataset.popular) - Number(b.dataset.popular));
  const grid = document.getElementById('productGrid');
  visibleCards.forEach(card => grid.appendChild(card));
  emptyState.hidden = visibleCards.length > 0;
}

categoryButtons.forEach(button => {
  button.addEventListener('click', () => {
    categoryButtons.forEach(item => item.classList.remove('active'));
    button.classList.add('active');
    activeCategory = button.dataset.category;
    updateProducts();
  });
});

document.querySelectorAll('.tab').forEach(button => {
  button.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(item => item.classList.remove('active'));
    button.classList.add('active');
    activeSort = button.dataset.sort;
    updateProducts();
  });
});

document.querySelectorAll('.favorite').forEach(button => {
  button.addEventListener('click', () => {
    const liked = button.classList.toggle('liked');
    button.textContent = liked ? '♥' : '♡';
    showToast(liked ? 'Kostum ditambahkan ke favorit ♡' : 'Kostum dihapus dari favorit');
  });
});

document.querySelectorAll('.add-button').forEach(button => {
  button.addEventListener('click', () => {
    const price = Number(button.dataset.price).toLocaleString('id-ID');
    showToast(`${button.dataset.name} — Rp${price}/hari. Hubungi pengelola untuk konfirmasi pemesanan.`);
  });
});

document.querySelectorAll('a[href="#kontak"]').forEach(link => {
  link.addEventListener('click', event => {
    if (link.getAttribute('href') === '#kontak' && link.textContent.trim() !== 'Hubungi tim kami') {
      event.preventDefault();
      showToast('Fitur akun dan pesanan perlu dihubungkan ke backend.');
    }
  });
});

updateProducts();
