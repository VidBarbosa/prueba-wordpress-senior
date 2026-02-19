(function () {
	'use strict';

	const app = document.getElementById('erm-shortcode-app');
	if (!app || typeof ermData === 'undefined') {
		return;
	}

	const form = document.getElementById('erm-filters-form');
	const loading = document.getElementById('erm-loading');
	const results = document.getElementById('erm-results');
	const pagination = document.getElementById('erm-pagination');
	let currentPage = 1;

	function toggleLoading(state) {
		loading.hidden = !state;
	}

	function serializeFilters(page = 1) {
		const formData = new FormData(form);
		const query = new URLSearchParams({ page: String(page), per_page: '6' });

		for (const [key, value] of formData.entries()) {
			if (typeof value === 'string' && value.trim() !== '') {
				query.set(key, value.trim());
			}
		}

		return query;
	}

	function validateFilters() {
		const searchValue = form.querySelector('[name="search"]').value;
		if (searchValue.length > 0 && searchValue.length < 2) {
			alert('La búsqueda debe tener al menos 2 caracteres.');
			return false;
		}
		return true;
	}

	function renderItems(items) {
		if (!items.length) {
			results.innerHTML = '<p>No se encontraron recursos.</p>';
			return;
		}

		results.innerHTML = items
			.map((item) => `
				<article class="erm-card">
					<h3>${item.title}</h3>
					<p><strong>Tipo:</strong> ${item.type || '-'}</p>
					<p><strong>Nivel:</strong> ${item.level || '-'}</p>
					<p><strong>Duración:</strong> ${item.duration || 0} min</p>
					<a class="erm-view-resource" data-id="${item.id}" href="${item.url || item.link}" target="_blank" rel="noopener noreferrer">Ver recurso</a>
				</article>
			`)
			.join('');
	}

	function renderPagination(totalPages) {
		pagination.innerHTML = '';
		if (totalPages <= 1) {
			return;
		}

		for (let i = 1; i <= totalPages; i += 1) {
			const button = document.createElement('button');
			button.type = 'button';
			button.textContent = String(i);
			button.disabled = i === currentPage;
			button.addEventListener('click', () => {
				fetchResources(i);
			});
			pagination.appendChild(button);
		}
	}

	async function fetchResources(page = 1) {
		if (!validateFilters()) {
			return;
		}

		currentPage = page;
		toggleLoading(true);
		try {
			const query = serializeFilters(page);
			const response = await fetch(`${ermData.apiBase}/resources?${query.toString()}`, {
				headers: { 'X-WP-Nonce': ermData.nonce },
			});
			const data = await response.json();
			renderItems(data.items || []);
			renderPagination(data.total_pages || 1);
		} catch (error) {
			results.innerHTML = '<p>Ocurrió un error al cargar recursos.</p>';
		} finally {
			toggleLoading(false);
		}
	}

	results.addEventListener('click', async (event) => {
		const target = event.target.closest('.erm-view-resource');
		if (!target) {
			return;
		}

		const resourceId = target.getAttribute('data-id');
		if (!resourceId) {
			return;
		}

		try {
			await fetch(`${ermData.apiBase}/resources/${resourceId}/track`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': ermData.nonce,
				},
				body: JSON.stringify({ action_type: 'view' }),
			});
		} catch (error) {
			// No-op, UX should continue.
		}
	});

	form.addEventListener('submit', (event) => {
		event.preventDefault();
		fetchResources(1);
	});

	fetchResources(1);
})();
