/**
 * Calendar Interactiv Evenimente - Brezoaele.ro (v2)
 */

document.addEventListener('DOMContentLoaded', function () {
	const calendarEl = document.getElementById('brz-calendar-grid');
	if (!calendarEl) return;

	const monthTitleEl = document.getElementById('brz-calendar-month-title');
	const prevBtn = document.getElementById('brz-cal-prev-month');
	const nextBtn = document.getElementById('brz-cal-next-month');
	const todayBtn = document.getElementById('brz-cal-today');
	const catFilters = document.querySelectorAll('.brz-cat-filter-btn');
	const dayModal = document.getElementById('brz-day-modal');
	const modalTitle = document.getElementById('brz-modal-date-title');
	const modalContent = document.getElementById('brz-modal-events-list');
	const closeModalBtn = document.getElementById('brz-modal-close');

	let currentDate = new Date();
	let currentYear = currentDate.getFullYear();
	let currentMonth = currentDate.getMonth() + 1; // 1 - 12
	let activeCategory = '';
	let monthlyEvents = [];

	const monthNames = [
		'Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie',
		'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'
	];

	function initCalendar() {
		loadEventsAndRender();

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				currentMonth--;
				if (currentMonth < 1) {
					currentMonth = 12;
					currentYear--;
				}
				loadEventsAndRender();
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				currentMonth++;
				if (currentMonth > 12) {
					currentMonth = 1;
					currentYear++;
				}
				loadEventsAndRender();
			});
		}

		if (todayBtn) {
			todayBtn.addEventListener('click', function () {
				const now = new Date();
				currentYear = now.getFullYear();
				currentMonth = now.getMonth() + 1;
				loadEventsAndRender();
			});
		}

		catFilters.forEach(btn => {
			btn.addEventListener('click', function () {
				catFilters.forEach(b => b.classList.remove('active'));
				this.classList.add('active');
				activeCategory = this.getAttribute('data-cat') || '';
				loadEventsAndRender();
			});
		});

		if (closeModalBtn) {
			closeModalBtn.addEventListener('click', function () {
				closeModal();
			});
		}

		window.addEventListener('click', function (e) {
			if (e.target === dayModal) {
				closeModal();
			}
		});
	}

	function loadEventsAndRender() {
		if (monthTitleEl) {
			monthTitleEl.textContent = `${monthNames[currentMonth - 1]} ${currentYear}`;
		}

		// Show loading indicator in calendar grid
		calendarEl.style.opacity = '0.5';

		const ajaxUrl = (typeof brezoaeleCalendar !== 'undefined' && brezoaeleCalendar.ajaxurl)
			? brezoaeleCalendar.ajaxurl
			: '/wp-admin/admin-ajax.php';

		const url = `${ajaxUrl}?action=brezoaele_get_calendar_events&year=${currentYear}&month=${currentMonth}&cat=${encodeURIComponent(activeCategory)}`;

		fetch(url)
			.then(response => response.json())
			.then(data => {
				calendarEl.style.opacity = '1';
				if (data.success) {
					monthlyEvents = data.data || [];
				} else {
					monthlyEvents = [];
				}
				renderGrid();
			})
			.catch(err => {
				console.error('Eroare încărcare evenimente:', err);
				calendarEl.style.opacity = '1';
				monthlyEvents = [];
				renderGrid();
			});
	}

	function renderGrid() {
		calendarEl.innerHTML = '';

		// Zilele săptămânii: 1 = Luni, 7 = Duminică
		const firstDayOfMonth = new Date(currentYear, currentMonth - 1, 1);
		let startingDay = firstDayOfMonth.getDay(); // 0 = Duminică, 1 = Luni
		if (startingDay === 0) startingDay = 7; // Convertim Duminica în index 7

		const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

		const today = new Date();
		const isCurrentMonth = (today.getFullYear() === currentYear && (today.getMonth() + 1) === currentMonth);
		const todayDay = today.getDate();

		// 1. Padding celule goale pentru începutul lunii
		for (let i = 1; i < startingDay; i++) {
			const emptyCell = document.createElement('div');
			emptyCell.className = 'brz-cal-cell brz-cal-cell-empty';
			calendarEl.appendChild(emptyCell);
		}

		// 2. Celulele zilelor din lună
		for (let day = 1; day <= daysInMonth; day++) {
			const cell = document.createElement('div');
			cell.className = 'brz-cal-cell';
			if (isCurrentMonth && day === todayDay) {
				cell.classList.add('brz-cal-today');
			}

			const dateHeader = document.createElement('div');
			dateHeader.className = 'brz-cal-date-num';
			dateHeader.textContent = day;
			cell.appendChild(dateHeader);

			// Găsim evenimentele din această zi
			const dayEvents = monthlyEvents.filter(ev => ev.day === day);

			if (dayEvents.length > 0) {
				cell.classList.add('has-events');

				const eventsContainer = document.createElement('div');
				eventsContainer.className = 'brz-cal-events-list';

				// Afișăm până la 3 pills
				const maxDisplay = 3;
				dayEvents.slice(0, maxDisplay).forEach(ev => {
					const pill = document.createElement('div');
					pill.className = `brz-event-pill cat-${ev.cat_slug}`;
					
					let timeLabel = '';
					if (ev.toata_ziua) {
						timeLabel = '⏳ ';
					} else if (ev.ora_start) {
						timeLabel = ev.ora_start + ' ';
					}
					
					pill.innerHTML = `<span class="pill-time">${timeLabel}</span><span class="pill-title">${escapeHtml(ev.title)}</span>`;
					pill.addEventListener('click', function (e) {
						e.stopPropagation();
						window.location.href = ev.permalink;
					});
					eventsContainer.appendChild(pill);
				});

				if (dayEvents.length > maxDisplay) {
					const moreBadge = document.createElement('div');
					moreBadge.className = 'brz-event-more';
					moreBadge.textContent = `+ încă ${dayEvents.length - maxDisplay}`;
					eventsContainer.appendChild(moreBadge);
				}

				cell.appendChild(eventsContainer);

				// Click pe întreaga celulă deschide modalul pentru ziua respectivă
				cell.addEventListener('click', function () {
					openDayModal(day, dayEvents);
				});
			}

			calendarEl.appendChild(cell);
		}
	}

	function openDayModal(day, events) {
		if (!dayModal || !modalContent) return;

		const formattedDate = `${day} ${monthNames[currentMonth - 1]} ${currentYear}`;
		modalTitle.textContent = `Evenimente pe ${formattedDate}`;

		let html = '';
		events.forEach(ev => {
			let timeBadge = '';
			if (ev.toata_ziua) {
				timeBadge = '<span class="brz-badge-time">⏳ Toată ziua</span>';
			} else if (ev.ora_start) {
				timeBadge = `<span class="brz-badge-time">⏰ ${ev.ora_start} ${ev.ora_sfarsit ? '- ' + ev.ora_sfarsit : ''}</span>`;
			}

			let repeatBadge = '';
			if (ev.repetitiv_anual) {
				repeatBadge = '<span class="brz-badge-repeat">🔁 Se repetă anual</span>';
			}

			let locStr = ev.locatie ? `<div class="brz-modal-ev-loc">📍 ${escapeHtml(ev.locatie)}</div>` : '';

			html += `
				<div class="brz-modal-ev-item">
					<div class="brz-modal-ev-header">
						<span class="brz-modal-ev-cat">${escapeHtml(ev.cat_name)}</span>
						<div class="brz-modal-badges">${timeBadge} ${repeatBadge}</div>
					</div>
					<h4 class="brz-modal-ev-title"><a href="${ev.permalink}">${escapeHtml(ev.title)}</a></h4>
					${locStr}
					<p class="brz-modal-ev-desc">${escapeHtml(ev.excerpt || '')}</p>
					<a href="${ev.permalink}" class="brz-modal-ev-btn">Vezi Detalii Eveniment &rarr;</a>
				</div>
			`;
		});

		modalContent.innerHTML = html;
		dayModal.style.display = 'flex';
	}

	function closeModal() {
		if (dayModal) dayModal.style.display = 'none';
	}

	function escapeHtml(str) {
		return String(str || '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	initCalendar();
});
