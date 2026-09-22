/**
 * Global Lightbox System for Brezoaele V2
 * Enables click-to-zoom and multi-image navigation across all posts, pages, custom post types, and galleries.
 */
document.addEventListener('DOMContentLoaded', function() {
	// Ensure Lightbox DOM structure exists
	let overlay = document.getElementById('brz_lightbox');
	if (!overlay) {
		overlay = document.createElement('div');
		overlay.id = 'brz_lightbox';
		overlay.className = 'brz-lightbox-overlay';
		overlay.setAttribute('aria-hidden', 'true');
		overlay.innerHTML = `
			<button type="button" class="brz-lightbox-close" id="brz_lb_close" aria-label="Închide">&times;</button>
			<button type="button" class="brz-lightbox-nav brz-lightbox-prev" id="brz_lb_prev" aria-label="Înapoi">&lsaquo;</button>
			<button type="button" class="brz-lightbox-nav brz-lightbox-next" id="brz_lb_next" aria-label="Înainte">&rsaquo;</button>
			<div class="brz-lightbox-content">
				<img id="brz_lb_img" class="brz-lightbox-img" src="" alt="">
				<div id="brz_lb_caption" class="brz-lightbox-caption"></div>
			</div>
		`;
		document.body.appendChild(overlay);
	}

	const lbImg = document.getElementById('brz_lb_img');
	const lbCaption = document.getElementById('brz_lb_caption');
	const btnClose = document.getElementById('brz_lb_close');
	const btnPrev = document.getElementById('brz_lb_prev');
	const btnNext = document.getElementById('brz_lb_next');

	let activeGalleryItems = [];
	let activeIndex = 0;

	// Global Event Delegation for image zoom
	document.body.addEventListener('click', function(e) {
		const targetImg = e.target.closest('img');
		if (!targetImg) return;

		// Ignore header, footer, avatar, logo, small icons
		if (targetImg.closest('.site-header, .site-footer, nav, .avatar, .emoji, .icon, .brz-baza-stat-icon')) return;

		// 1. Check if inside custom gallery slider wrapper (e.g. template-baza-sportiva.php)
		const item = targetImg.closest('.brz-gallery-item');
		if (item) {
			const wrapper = item.closest('.brz-section-gallery-wrapper');
			if (wrapper) {
				const rawJsonEl = wrapper.querySelector('.brz-gallery-raw-json');
				if (rawJsonEl) {
					try {
						activeGalleryItems = JSON.parse(rawJsonEl.textContent);
					} catch(err) {
						activeGalleryItems = [];
					}
					if (activeGalleryItems.length) {
						const idx = parseInt(item.dataset.idx, 10);
						activeIndex = !isNaN(idx) ? idx : 0;
						openLightbox();
						e.preventDefault();
						return;
					}
				}
			}
		}

		// 2. Check if inside standard content (articles, pages, business listings, Gutenberg galleries, etc.)
		const contentContainer = targetImg.closest('.entry-content, article, .card, main') || document.body;

		// Find all eligible content images in current scope
		const allImgs = Array.from(contentContainer.querySelectorAll('.entry-content img, .post-thumbnail img, .wp-block-image img, .wp-block-gallery img, figure img, .gallery img, .card img')).filter(function(img) {
			if (img.closest('.site-header, .site-footer, nav, .avatar, .emoji, .icon, .brz-baza-stat-icon')) return false;
			const w = img.naturalWidth || img.width || img.offsetWidth;
			const h = img.naturalHeight || img.height || img.offsetHeight;
			return (w > 80 && h > 80) || !img.complete;
		});

		if (!allImgs.length) return;

		if (!allImgs.includes(targetImg)) {
			// If target image was not picked up by selectors, add it
			allImgs.push(targetImg);
		}

		// Build gallery items array
		activeGalleryItems = allImgs.map(function(img) {
			const parentLink = img.closest('a');
			let fullUrl = img.src;
			if (parentLink && /\.(jpe?g|png|gif|webp|svg)(\?.*)?$/i.test(parentLink.href)) {
				fullUrl = parentLink.href;
			} else if (img.dataset.fullSrc) {
				fullUrl = img.dataset.fullSrc;
			}

			const figcaption = img.closest('figure') ? img.closest('figure').querySelector('figcaption') : null;
			const captionText = (figcaption ? figcaption.textContent.trim() : '') || img.alt || img.title || '';

			return {
				url: fullUrl,
				title: captionText,
				el: img
			};
		});

		// Find index of clicked image
		const clickedIdx = activeGalleryItems.findIndex(function(it) {
			return it.el === targetImg || it.url === targetImg.src;
		});
		activeIndex = clickedIdx >= 0 ? clickedIdx : 0;

		// Prevent default browser navigation if image is wrapped in a link
		if (targetImg.closest('a')) {
			e.preventDefault();
		}

		openLightbox();
	});

	function openLightbox() {
		updateLightboxContent();
		if (overlay) {
			overlay.classList.add('active');
			overlay.setAttribute('aria-hidden', 'false');
		}
		document.body.style.overflow = 'hidden';
	}

	function closeLightbox() {
		if (overlay) {
			overlay.classList.remove('active');
			overlay.setAttribute('aria-hidden', 'true');
		}
		document.body.style.overflow = '';
	}

	function updateLightboxContent() {
		const current = activeGalleryItems[activeIndex];
		if (!current) return;

		if (lbImg) {
			lbImg.src = current.url || '';
			lbImg.alt = current.title || '';
		}

		if (lbCaption) {
			if (current.title) {
				lbCaption.textContent = current.title;
				lbCaption.style.display = 'block';
			} else {
				lbCaption.style.display = 'none';
			}
		}

		if (btnPrev && btnNext) {
			if (activeGalleryItems.length > 1) {
				btnPrev.style.display = 'flex';
				btnNext.style.display = 'flex';
			} else {
				btnPrev.style.display = 'none';
				btnNext.style.display = 'none';
			}
		}
	}

	function prevImage() {
		if (activeGalleryItems.length <= 1) return;
		activeIndex = (activeIndex - 1 + activeGalleryItems.length) % activeGalleryItems.length;
		updateLightboxContent();
	}

	function nextImage() {
		if (activeGalleryItems.length <= 1) return;
		activeIndex = (activeIndex + 1) % activeGalleryItems.length;
		updateLightboxContent();
	}

	if (btnClose) btnClose.addEventListener('click', closeLightbox);
	if (btnPrev) btnPrev.addEventListener('click', function(e) { e.stopPropagation(); prevImage(); });
	if (btnNext) btnNext.addEventListener('click', function(e) { e.stopPropagation(); nextImage(); });

	if (overlay) {
		overlay.addEventListener('click', function(e) {
			if (e.target === overlay || e.target.classList.contains('brz-lightbox-content')) {
				closeLightbox();
			}
		});
	}

	document.addEventListener('keydown', function(e) {
		if (!overlay || !overlay.classList.contains('active')) return;
		if (e.key === 'Escape') closeLightbox();
		if (e.key === 'ArrowLeft') prevImage();
		if (e.key === 'ArrowRight') nextImage();
	});
});
