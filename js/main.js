document.addEventListener('DOMContentLoaded', function() {
    initNavToggle();
    loadProductsWithUI();
});

let allProducts = [];

function initNavToggle() {
    const toggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (!toggle || !navLinks) return;

    toggle.addEventListener('click', () => {
        navLinks.classList.toggle('open');
        toggle.classList.toggle('open');
    });
}

async function loadProductsWithUI() {
    const statusEl = document.getElementById('products-status');
    const container = document.getElementById('products-container');
    const filterButtons = document.querySelectorAll('.filter-btn');

    if (!container) return;

    statusEl.textContent = 'Loading products...';
    container.innerHTML = '';

    try {
        const response = await fetch('process/get_products.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const products = await response.json();
        allProducts = Array.isArray(products) ? products : [];

        if (allProducts.length === 0) {
            statusEl.textContent = 'No products available yet.';
        } else {
            statusEl.textContent = '';
        }

        renderProducts('all');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const category = btn.getAttribute('data-category');
                renderProducts(category);
            });
        });
    } catch (error) {
        console.error('Error loading products:', error);
        statusEl.textContent = 'Failed to load products. Please try again later.';
    }
}

function renderProducts(category) {
    const container = document.getElementById('products-container');
    if (!container) return;

    container.innerHTML = '';

    const filtered = category === 'all'
        ? allProducts
        : allProducts.filter(p => p.category === category);

    if (filtered.length === 0) {
        container.innerHTML = '<p class="products-empty">No products found for this category.</p>';
        return;
    }

    filtered.forEach((product, index) => {
        const isNew = index === 0;
        const badge = isNew ? '<span class="product-badge">New</span>' : '';

        const productCard = `
            <div class="product-card" data-name="${product.name}" data-price="${product.price}" data-description="${product.description}" data-image="${product.image_path}">
                <div class="product-image">
                    ${badge}
                    <img src="${product.image_path}" alt="${product.name}">
                </div>
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <p class="product-desc">${product.description}</p>
                    <p class="product-price">UGX ${product.price}</p>
                </div>
            </div>
        `;
        container.innerHTML += productCard;
    });

    // Attach click handlers for zoom modal
    container.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', () => {
            const name = card.getAttribute('data-name');
            const price = card.getAttribute('data-price');
            const description = card.getAttribute('data-description');
            const image = card.getAttribute('data-image');
            openProductModal({ name, price, description, image });
        });
    });
}

function openProductModal(product) {
    const existing = document.querySelector('.product-modal-backdrop');
    if (existing) existing.remove();

    const backdrop = document.createElement('div');
    backdrop.className = 'product-modal-backdrop';

    backdrop.innerHTML = `
        <div class="product-modal-content">
            <button class="product-modal-close" aria-label="Close">&times;</button>
            <div class="product-modal-image-wrapper">
                <img src="${product.image}" alt="${product.name}" id="product-modal-image">
            </div>
            <div class="product-modal-info">
                <h3>${product.name}</h3>
                <p><strong>Price:</strong>UGX ${product.price}</p>
                <p>${product.description}</p>
            </div>
            <div class="product-modal-zoom-controls">
                <button class="product-modal-zoom-btn" data-zoom="out">-</button>
                <button class="product-modal-zoom-btn" data-zoom="in">+</button>
            </div>
        </div>
    `;

    document.body.appendChild(backdrop);

    const close = backdrop.querySelector('.product-modal-close');
    close.addEventListener('click', () => backdrop.remove());
    backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) {
            backdrop.remove();
        }
    });

    // Zoom controls
    const img = backdrop.querySelector('#product-modal-image');
    let scale = 1;

    backdrop.querySelectorAll('.product-modal-zoom-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.getAttribute('data-zoom');
            if (type === 'in') {
                scale = Math.min(scale + 0.2, 3);
            } else {
                scale = Math.max(scale - 0.2, 0.6);
            }
            img.style.transform = `scale(${scale})`;
        });
    });
}
