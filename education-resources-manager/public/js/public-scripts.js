(function () {
	'use strict';

	if (typeof wp === 'undefined' || !wp.element || typeof ermData === 'undefined') {
		return;
	}

	const { createElement, useState, useEffect } = wp.element;
	const root = document.getElementById('erm-shortcode-app');

	if (!root) {
		return;
	}

	function ResourceCard({ item, onTrack }) {
		const targetUrl = item.url || item.link || '#';

		return createElement(
			'article',
			{ className: 'erm-card' },
			createElement('h3', null, item.title),
			createElement('p', null, createElement('strong', null, 'Tipo:'), ' ', item.type || '-'),
			createElement('p', null, createElement('strong', null, 'Nivel:'), ' ', item.level || '-'),
			createElement('p', null, createElement('strong', null, 'Duración:'), ' ', `${item.duration || 0} min`),
			createElement(
				'a',
				{
					className: 'erm-view-resource',
					href: targetUrl,
					target: '_blank',
					rel: 'noopener noreferrer',
					onClick: function () {
						onTrack(item.id);
					},
				},
				'Ver recurso'
			)
		);
	}

	function App() {
		const [filters, setFilters] = useState({ search: '', type: '', level: '', category: '' });
		const [items, setItems] = useState([]);
		const [loading, setLoading] = useState(false);
		const [error, setError] = useState('');
		const [currentPage, setCurrentPage] = useState(1);
		const [totalPages, setTotalPages] = useState(1);

		const categories = Array.isArray(ermData.categories) ? ermData.categories : [];

		function updateFilter(event) {
			const { name, value } = event.target;
			setFilters(function (prev) {
				return Object.assign({}, prev, { [name]: value });
			});
		}

		async function loadResources(page) {
			if (filters.search && filters.search.length > 0 && filters.search.length < 2) {
				setError('La búsqueda debe tener al menos 2 caracteres.');
				setItems([]);
				return;
			}

			setLoading(true);
			setError('');

			try {
				const query = new URLSearchParams({ page: String(page), per_page: '6' });
				Object.keys(filters).forEach(function (key) {
					const value = (filters[key] || '').trim();
					if (value !== '') {
						query.set(key, value);
					}
				});

				const response = await fetch(`${ermData.apiBase}/resources?${query.toString()}`, {
					headers: {
						'X-WP-Nonce': ermData.nonce,
					},
				});
				const data = await response.json();
				setItems(Array.isArray(data.items) ? data.items : []);
				setTotalPages(data.total_pages || 1);
				setCurrentPage(page);
			} catch (requestError) {
				setError('Ocurrió un error al cargar recursos.');
			} finally {
				setLoading(false);
			}
		}

		async function trackResource(resourceId) {
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
			} catch (trackingError) {
				// Keep UX flow uninterrupted.
			}
		}

		useEffect(
			function () {
				loadResources(1);
			},
			[]
		);

		const paginationButtons = [];
		for (let page = 1; page <= totalPages; page += 1) {
			paginationButtons.push(
				createElement(
					'button',
					{
						type: 'button',
						className: page === currentPage ? 'is-active' : '',
						disabled: page === currentPage || loading,
						onClick: function () {
							loadResources(page);
						},
						key: `page-${page}`,
					},
					String(page)
				)
			);
		}

		return createElement(
			'div',
			{ className: 'erm-shell' },
			createElement(
				'div',
				{ className: 'erm-panel erm-panel--filters' },
				createElement('h2', null, 'Explora recursos educativos'),
				createElement(
					'p',
					{ className: 'erm-subtitle' },
					'Filtra por tipo, nivel y categoría para encontrar el contenido ideal.'
				),
				createElement(
					'form',
					{
						className: 'erm-filters',
						onSubmit: function (event) {
							event.preventDefault();
							loadResources(1);
						},
					},
					createElement('input', {
						type: 'text',
						name: 'search',
						placeholder: 'Buscar recursos',
						value: filters.search,
						onChange: updateFilter,
						'aria-label': 'Buscar recursos',
					}),
					createElement(
						'select',
						{ name: 'type', value: filters.type, onChange: updateFilter },
						createElement('option', { value: '' }, 'Tipo de recurso'),
						createElement('option', { value: 'curso' }, 'Curso'),
						createElement('option', { value: 'tutorial' }, 'Tutorial'),
						createElement('option', { value: 'ebook' }, 'eBook'),
						createElement('option', { value: 'video' }, 'Video')
					),
					createElement(
						'select',
						{ name: 'level', value: filters.level, onChange: updateFilter },
						createElement('option', { value: '' }, 'Nivel de dificultad'),
						createElement('option', { value: 'principiante' }, 'Principiante'),
						createElement('option', { value: 'intermedio' }, 'Intermedio'),
						createElement('option', { value: 'avanzado' }, 'Avanzado')
					),
					createElement(
						'select',
						{ name: 'category', value: filters.category, onChange: updateFilter },
						createElement('option', { value: '' }, 'Categoría'),
						categories.map(function (category) {
							return createElement(
								'option',
								{ key: category.id, value: String(category.id) },
								category.name
							);
						})
					),
					createElement(
						'button',
						{ type: 'submit', disabled: loading },
						loading ? 'Aplicando…' : 'Aplicar filtros'
					)
				)
			),
			loading ? createElement('div', { className: 'erm-loading' }, 'Cargando recursos...') : null,
			error ? createElement('p', { className: 'erm-error' }, error) : null,
			createElement(
				'div',
				{ className: 'erm-results' },
				items.length
					? items.map(function (item) {
							return createElement(ResourceCard, { key: item.id, item: item, onTrack: trackResource });
						})
					: !loading && !error
						? createElement('p', { className: 'erm-empty' }, 'No se encontraron recursos.')
						: null
			),
			createElement('div', { className: 'erm-pagination' }, paginationButtons)
		);
	}

	wp.element.render(createElement(App), root);
})();
