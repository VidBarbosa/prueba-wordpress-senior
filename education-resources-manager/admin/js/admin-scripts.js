(function () {
	'use strict';

	if (typeof ermAdminData === 'undefined') {
		return;
	}

	const canvas = document.getElementById('erm-month-chart');
	if (!canvas) {
		return;
	}

	const stats = Array.isArray(ermAdminData.monthStats) ? ermAdminData.monthStats : [];
	if (!stats.length) {
		return;
	}

	const context = canvas.getContext('2d');
	const width = canvas.width;
	const height = canvas.height;
	const padding = 28;
	const chartHeight = height - padding * 2;
	const chartWidth = width - padding * 2;
	const maxValue = Math.max.apply(null, stats.map((item) => Number(item.total) || 0), 1);
	const barGap = 12;
	const barWidth = (chartWidth - barGap * (stats.length - 1)) / stats.length;

	context.clearRect(0, 0, width, height);
	context.fillStyle = '#ffffff';
	context.fillRect(0, 0, width, height);

	stats.forEach((item, index) => {
		const value = Number(item.total) || 0;
		const x = padding + index * (barWidth + barGap);
		const barHeight = maxValue > 0 ? (value / maxValue) * chartHeight : 0;
		const y = height - padding - barHeight;

		context.fillStyle = '#ced8ff';
		context.fillRect(x, y, barWidth, barHeight);

		context.fillStyle = '#3f4a6a';
		context.font = '11px sans-serif';
		context.fillText(String(item.ym || ''), x, height - 10);
		context.fillText(String(value), x + 2, y - 6);
	});
})();
