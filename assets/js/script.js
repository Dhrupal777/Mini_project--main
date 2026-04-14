let bannerIndex = 0;
let bannerInterval;

function initBanner() {
    const track = document.querySelector('.banner-track');
    if (!track) return;
    const slides = track.children;
    if (slides.length === 0) return;
    const totalSlides = slides.length;
    const prevBtn = document.querySelector('.banner-btn.prev');
    const nextBtn = document.querySelector('.banner-btn.next');

    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        bannerIndex = index;
        track.style.transform = 'translateX(-' + (bannerIndex * 100) + '%)';
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            goToSlide(bannerIndex - 1);
            resetInterval();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            goToSlide(bannerIndex + 1);
            resetInterval();
        });
    }

    function startInterval() {
        bannerInterval = setInterval(function() {
            goToSlide(bannerIndex + 1);
        }, 4000);
    }

    function resetInterval() {
        clearInterval(bannerInterval);
        startInterval();
    }

    startInterval();
}

function showToast(message, type) {
    let existing = document.querySelector('.toast');
    if (existing) existing.remove();

    let toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(function() {
        toast.classList.add('show');
    }, 10);

    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 2500);
}

function addToCart(btn, productId) {
    btn.disabled = true;
    btn.textContent = 'Adding...';

    let formData = new FormData();
    formData.append('product_id', productId);

    fetch('add-to-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('Added to cart!', 'success');
            btn.textContent = 'Added ✓';
            let badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = data.cart_count;
            } else {
                let cartLink = document.querySelector('.cart-link');
                if (cartLink) {
                    let newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = data.cart_count;
                    cartLink.appendChild(newBadge);
                }
            }
            setTimeout(function() {
                btn.textContent = 'Add to Cart';
                btn.disabled = false;
            }, 1500);
        } else {
            showToast(data.message || 'Please login first', 'error');
            btn.textContent = 'Add to Cart';
            btn.disabled = false;
        }
    })
    .catch(function() {
        showToast('Something went wrong', 'error');
        btn.textContent = 'Add to Cart';
        btn.disabled = false;
    });
}

function updateCartQty(itemId, newQty) {
    if (newQty < 1) return;

    let formData = new FormData();
    formData.append('cart_id', itemId);
    formData.append('quantity', newQty);

    fetch('update-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            showToast(data.message || 'Error updating cart', 'error');
        }
    });
}

function removeCartItem(itemId) {
    if (!confirm('Remove this item from cart?')) return;

    let formData = new FormData();
    formData.append('cart_id', itemId);

    fetch('remove-from-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            showToast(data.message || 'Error removing item', 'error');
        }
    });
}

function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    window.location.href = 'delete-product.php?id=' + productId;
}

document.addEventListener('DOMContentLoaded', function() {
    initBanner();

    let flashTimers = document.querySelectorAll('.flash-timer');
    flashTimers.forEach(function(timer) {
        let seconds = parseInt(timer.getAttribute('data-seconds')) || 3600;
        setInterval(function() {
            seconds--;
            if (seconds < 0) seconds = 3600;
            let h = Math.floor(seconds / 3600);
            let m = Math.floor((seconds % 3600) / 60);
            let s = seconds % 60;
            let display = timer.querySelector('.timer-display');
            if (display) {
                display.textContent = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }
        }, 1000);
    });
});