/**
 * Ashrafco Admin Portal - General Analytics Dashboard Interactions & Charts
 *
 * Handles Chart.js lifecycle, dynamic date-range filtering via AJAX,
 * popover management, and reactive KPI updates.
 *
 * @package Ashrafco
 * @since 1.2.0
 */

(function() {
	'use strict';

	var salesChartInstance = null;
	var statusChartInstance = null;

	document.addEventListener('DOMContentLoaded', function() {
		// Only run if analytics dashboard is present
		var overviewTab = document.getElementById('portal-tab-overview');
		if (!overviewTab || !window.AshrafcoAnalyticsInitial) {
			return;
		}

		initCharts(window.AshrafcoAnalyticsInitial);
		initDateFilters();
		initCustomDatePopover();
	});

	/**
	 * Initialize Chart.js instances
	 */
	function initCharts(initialData) {
		if (typeof Chart === 'undefined') {
			console.warn('Chart.js library is not loaded.');
			return;
		}

		// Set default Chart.js styling tokens
		Chart.defaults.font.family = "'Cairo', system-ui, -apple-system, sans-serif";
		Chart.defaults.color = '#64748b';

		// 1. Sales & Orders Over Time Combined Chart
		var salesCanvas = document.getElementById('ashrafcoSalesOrdersChart');
		if (salesCanvas) {
			var salesCtx = salesCanvas.getContext('2d');
			salesChartInstance = new Chart(salesCtx, {
				data: {
					labels: initialData.chart_timeline.labels,
					datasets: [
						{
							type: 'bar',
							label: 'المبيعات (' + (initialData.currency_code || 'جنيه') + ')',
							data: initialData.chart_timeline.sales,
							backgroundColor: 'rgba(251, 113, 133, 0.75)',
							hoverBackgroundColor: '#e11d48',
							borderRadius: 4,
							borderSkipped: false,
							barPercentage: 0.55,
							categoryPercentage: 0.7,
							yAxisID: 'y'
						},
						{
							type: 'line',
							label: 'عدد الطلبات',
							data: initialData.chart_timeline.orders,
							borderColor: '#0f172a',
							backgroundColor: '#0f172a',
							borderWidth: 2.5,
							pointBackgroundColor: '#0f172a',
							pointBorderColor: '#ffffff',
							pointBorderWidth: 2,
							pointRadius: function(context) {
								return (context.parsed && context.parsed.y > 0) ? 4.5 : 1.5;
							},
							pointHoverRadius: 6,
							tension: 0.35,
							fill: false,
							yAxisID: 'y1'
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					interaction: {
						mode: 'index',
						intersect: false
					},
					plugins: {
						legend: {
							display: false
						},
						tooltip: {
							rtl: true,
							textDirection: 'rtl',
							titleFont: { family: "'Cairo', sans-serif", weight: 'bold', size: 12 },
							bodyFont: { family: "'Cairo', sans-serif", size: 11 },
							padding: 10,
							cornerRadius: 10,
							backgroundColor: 'rgba(15, 23, 42, 0.9)',
							boxPadding: 4,
							callbacks: {
								label: function(context) {
									var datasetLabel = context.dataset.label || '';
									var val = context.parsed.y;
									if (context.datasetIndex === 0) {
										var formatted = new Intl.NumberFormat('en-US').format(val);
										return datasetLabel + ': EGP ' + formatted;
									} else {
										return datasetLabel + ': ' + val + ' طلب';
									}
								}
							}
						}
					},
					scales: {
						x: {
							grid: {
								display: false
							},
							ticks: {
								font: { family: "'Cairo', sans-serif", size: 10 },
								color: '#64748b',
								maxRotation: 0,
								autoSkip: true,
								maxTicksLimit: 11
							}
						},
						y: {
							position: 'left',
							grid: {
								color: '#f1f5f9'
							},
							ticks: {
								font: { family: "'Cairo', sans-serif", size: 10 },
								color: '#64748b',
								callback: function(val) {
									if (val >= 1000) {
										return 'EGP ' + (val / 1000) + 'K';
									}
									return 'EGP ' + val;
								}
							}
						},
						y1: {
							position: 'right',
							grid: {
								display: false
							},
							ticks: {
								font: { family: "'Cairo', sans-serif", size: 10 },
								color: '#64748b',
								precision: 0
							}
						}
					}
				}
			});
		}

		// 2. Order Status Distribution Donut Chart
		var statusCanvas = document.getElementById('ashrafcoStatusDonutChart');
		if (statusCanvas) {
			var statusCtx = statusCanvas.getContext('2d');
			var donutLabels = initialData.chart_status.labels.length ? initialData.chart_status.labels : ['لا توجد طلبات'];
			var donutData = initialData.chart_status.data.length ? initialData.chart_status.data : [1];
			var donutColors = initialData.chart_status.colors.length ? initialData.chart_status.colors : ['#e2e8f0'];

			statusChartInstance = new Chart(statusCtx, {
				type: 'doughnut',
				data: {
					labels: donutLabels,
					datasets: [{
						data: donutData,
						backgroundColor: donutColors,
						borderWidth: 3,
						borderColor: '#ffffff',
						hoverOffset: 4
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					cutout: '72%',
					plugins: {
						legend: {
							display: false
						},
						tooltip: {
							rtl: true,
							textDirection: 'rtl',
							titleFont: { family: "'Cairo', sans-serif", weight: 'bold', size: 12 },
							bodyFont: { family: "'Cairo', sans-serif", size: 11 },
							padding: 8,
							cornerRadius: 8,
							callbacks: {
								label: function(context) {
									if (!initialData.chart_status.data.length) {
										return 'لا توجد طلبات مسجلة';
									}
									var label = context.label || '';
									var count = context.parsed;
									var total = initialData.chart_status.total_count || 1;
									var pct = ((count / total) * 100).toFixed(1);
									return label + ': ' + count + ' طلب (' + pct + '%)';
								}
							}
						}
					}
				}
			});
		}
	}

	/**
	 * Setup quick date period buttons
	 */
	function initDateFilters() {
		var periodButtons = document.querySelectorAll('.portal-period-btn');
		periodButtons.forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				var period = this.getAttribute('data-period');
				if (period === 'custom') {
					toggleCustomPopover(true);
					return;
				}
				toggleCustomPopover(false);
				applyAnalyticsFilter(period, '', '');
			});
		});
	}

	/**
	 * Setup custom date popover
	 */
	function initCustomDatePopover() {
		var rangeBtn = document.getElementById('portal-date-range-btn');
		var popover = document.getElementById('custom-date-popover');
		var closeBtn = document.getElementById('close-popover-btn');
		var cancelBtn = document.getElementById('cancel-custom-date-btn');
		var applyBtn = document.getElementById('apply-custom-date-btn');

		if (rangeBtn) {
			rangeBtn.addEventListener('click', function(e) {
				e.preventDefault();
				toggleCustomPopover();
			});
		}

		if (closeBtn) {
			closeBtn.addEventListener('click', function(e) {
				e.preventDefault();
				toggleCustomPopover(false);
			});
		}

		if (cancelBtn) {
			cancelBtn.addEventListener('click', function(e) {
				e.preventDefault();
				toggleCustomPopover(false);
			});
		}

		if (applyBtn) {
			applyBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var startInput = document.getElementById('portal-start-date');
				var endInput = document.getElementById('portal-end-date');

				if (!startInput || !endInput) return;
				var startVal = startInput.value.trim();
				var endVal = endInput.value.trim();

				if (!startVal || !endVal) {
					alert('يرجى تحديد تاريخ البداية والنهاية.');
					return;
				}

				if (new Date(startVal) > new Date(endVal)) {
					alert('تاريخ البداية يجب أن يكون قبل أو يساوي تاريخ النهاية.');
					return;
				}

				toggleCustomPopover(false);
				applyAnalyticsFilter('custom', startVal, endVal);
			});
		}

		// Close popover when clicking outside
		document.addEventListener('click', function(e) {
			if (popover && !popover.classList.contains('hidden')) {
				if (!popover.contains(e.target) && (!rangeBtn || !rangeBtn.contains(e.target))) {
					var isCustomBtn = e.target.closest('[data-period="custom"]');
					if (!isCustomBtn) {
						toggleCustomPopover(false);
					}
				}
			}
		});
	}

	function toggleCustomPopover(forceState) {
		var popover = document.getElementById('custom-date-popover');
		if (!popover) return;

		if (typeof forceState === 'boolean') {
			if (forceState) {
				popover.classList.remove('hidden');
			} else {
				popover.classList.add('hidden');
			}
		} else {
			popover.classList.toggle('hidden');
		}
	}

	/**
	 * Perform AJAX Request & update dashboard state
	 */
	function applyAnalyticsFilter(period, startDate, endDate) {
		var config = window.AshrafcoAnalyticsConfig;
		if (!config || !config.ajaxUrl) return;

		var overviewTab = document.getElementById('portal-tab-overview');
		if (overviewTab) {
			overviewTab.classList.add('opacity-70', 'pointer-events-none', 'transition-opacity');
		}

		// Update Button Active Classes immediately
		var periodButtons = document.querySelectorAll('.portal-period-btn');
		periodButtons.forEach(function(btn) {
			if (btn.getAttribute('data-period') === period) {
				btn.className = 'portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-xs bg-rose-600 border border-rose-600 text-white font-black';
			} else {
				btn.className = 'portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-2xs bg-white border border-slate-200 text-slate-700 hover:bg-slate-50';
			}
		});

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_analytics_data');
		formData.append('nonce', config.nonce);
		formData.append('period', period);
		if (startDate) formData.append('start_date', startDate);
		if (endDate) formData.append('end_date', endDate);

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: formData
		})
		.then(function(res) {
			return res.json();
		})
		.then(function(res) {
			if (overviewTab) {
				overviewTab.classList.remove('opacity-70', 'pointer-events-none');
			}

			if (res.success && res.data) {
				renderAnalyticsData(res.data, period, startDate, endDate);
			} else {
				alert(res.data && res.data.message ? res.data.message : 'تعذر تحميل الإحصائيات.');
			}
		})
		.catch(function(err) {
			console.error('Analytics AJAX Error:', err);
			if (overviewTab) {
				overviewTab.classList.remove('opacity-70', 'pointer-events-none');
			}
			alert('حدث خطأ أثناء جلب بيانات الإحصائيات.');
		});
	}

	/**
	 * Render updated analytics payload to DOM
	 */
	function renderAnalyticsData(data, period, startDate, endDate) {
		// 1. Update Date Range Label
		var labelEl = document.getElementById('portal-date-range-label');
		if (labelEl && data.range && data.range.label) {
			labelEl.textContent = data.range.label;
		}

		// 2. Update KPI 1: Net Sales
		var netSalesVal = document.getElementById('kpi-net-sales-value');
		var netSalesTrend = document.getElementById('kpi-net-sales-trend');
		if (netSalesVal && data.kpis && data.kpis.net_revenue) {
			netSalesVal.textContent = data.kpis.net_revenue.formatted;
		}
		if (netSalesTrend && data.kpis && data.kpis.net_revenue && data.kpis.net_revenue.trend) {
			var t1 = data.kpis.net_revenue.trend;
			var arrow1 = t1.direction === 'up' ? '▲' : (t1.direction === 'down' ? '▼' : '');
			netSalesTrend.textContent = t1.formatted + ' ' + arrow1;
			netSalesTrend.className = 'text-xs font-bold inline-flex items-center gap-0.5 ' + t1.badge_css;
		}

		// 3. Update KPI 2: Total Orders
		var totalOrdersVal = document.getElementById('kpi-total-orders-value');
		var totalOrdersTrend = document.getElementById('kpi-total-orders-trend');
		if (totalOrdersVal && data.kpis && data.kpis.total_orders) {
			totalOrdersVal.textContent = data.kpis.total_orders.raw;
		}
		if (totalOrdersTrend && data.kpis && data.kpis.total_orders && data.kpis.total_orders.trend) {
			var t2 = data.kpis.total_orders.trend;
			var arrow2 = t2.direction === 'up' ? '▲' : (t2.direction === 'down' ? '▼' : '');
			totalOrdersTrend.textContent = t2.formatted + ' ' + arrow2;
			totalOrdersTrend.className = 'text-xs font-bold inline-flex items-center gap-0.5 ' + t2.badge_css;
		}

		// 4. Update KPI 3: Pending Orders
		var pendingOrdersVal = document.getElementById('kpi-pending-orders-value');
		var pendingOrdersTrend = document.getElementById('kpi-pending-orders-trend');
		if (pendingOrdersVal && data.kpis && data.kpis.pending_orders) {
			pendingOrdersVal.textContent = data.kpis.pending_orders.raw;
		}
		if (pendingOrdersTrend && data.kpis && data.kpis.pending_orders && data.kpis.pending_orders.trend) {
			var t3 = data.kpis.pending_orders.trend;
			var arrow3 = t3.direction === 'up' ? '▲' : (t3.direction === 'down' ? '▼' : '');
			pendingOrdersTrend.textContent = t3.formatted + ' ' + arrow3;
			pendingOrdersTrend.className = 'text-xs font-bold inline-flex items-center gap-0.5 text-amber-600';
		}

		// 5. Update KPI 4: Stock Alerts
		var stockAlertsVal = document.getElementById('kpi-stock-alerts-value');
		var stockAlertsTrend = document.getElementById('kpi-stock-alerts-trend');
		if (stockAlertsVal && data.kpis && data.kpis.stock_alerts) {
			stockAlertsVal.textContent = data.kpis.stock_alerts.raw;
		}
		if (stockAlertsTrend && data.kpis && data.kpis.stock_alerts && data.kpis.stock_alerts.trend) {
			var t4 = data.kpis.stock_alerts.trend;
			var arrow4 = t4.direction === 'up' ? '▲' : '';
			stockAlertsTrend.textContent = t4.formatted + ' ' + arrow4;
			stockAlertsTrend.className = 'text-xs font-bold inline-flex items-center gap-0.5 ' + t4.badge_css;
		}

		// 6. Update Sales & Orders Chart
		if (salesChartInstance && data.chart_timeline) {
			salesChartInstance.data.labels = data.chart_timeline.labels;
			salesChartInstance.data.datasets[0].data = data.chart_timeline.sales;
			salesChartInstance.data.datasets[1].data = data.chart_timeline.orders;
			salesChartInstance.update();
		}

		// 7. Update Status Donut Chart & Center Count
		var donutTotalCount = document.getElementById('donut-total-count');
		if (donutTotalCount && data.chart_status) {
			donutTotalCount.textContent = data.chart_status.total_count;
		}

		if (statusChartInstance && data.chart_status) {
			var hasOrders = data.chart_status.data && data.chart_status.data.length > 0;
			statusChartInstance.data.labels = hasOrders ? data.chart_status.labels : ['لا توجد طلبات'];
			statusChartInstance.data.datasets[0].data = hasOrders ? data.chart_status.data : [1];
			statusChartInstance.data.datasets[0].backgroundColor = hasOrders ? data.chart_status.colors : ['#e2e8f0'];
			statusChartInstance.update();
		}

		// 8. Update Status Breakdown Table
		var statusListContainer = document.getElementById('status-distribution-list');
		if (statusListContainer && data.chart_status && data.chart_status.breakdown) {
			var htmlList = '';
			data.chart_status.breakdown.forEach(function(item) {
				htmlList += '<div class="flex items-center justify-between gap-2 py-1.5 border-b border-slate-50 last:border-0">' +
					'<div class="flex items-center gap-2">' +
						'<span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: ' + item.color + '"></span>' +
						'<span class="text-xs font-bold text-slate-700">' + item.label + '</span>' +
					'</div>' +
					'<div class="flex items-center gap-3 font-mono text-xs">' +
						'<span class="font-extrabold text-slate-900">' + item.count + '</span>' +
						'<span class="text-slate-400 text-[11px] w-12 text-left" dir="ltr">' + item.percentage + '%</span>' +
					'</div>' +
				'</div>';
			});
			statusListContainer.innerHTML = htmlList;
		}

		// 9. Update Top Selling Products Table
		var topSellingContainer = document.getElementById('top-selling-table-body');
		if (topSellingContainer) {
			if (data.top_selling && data.top_selling.length > 0) {
				var topHtml = '';
				data.top_selling.forEach(function(item) {
					topHtml += '<tr class="hover:bg-slate-50/70 transition-colors">' +
						'<td class="py-3 px-3 align-middle font-bold text-slate-400 font-mono text-center w-8">' + item.rank + '</td>' +
						'<td class="py-3 px-3 align-middle">' +
							'<div class="flex items-center gap-2.5">' +
								'<img src="' + item.thumb_url + '" alt="' + item.name + '" class="w-10 h-10 rounded-xl object-cover border border-slate-100 flex-shrink-0 bg-slate-50">' +
								'<a href="' + item.edit_url + '" class="text-xs font-bold text-slate-800 hover:text-rose-600 transition-colors line-clamp-1 max-w-[200px] sm:max-w-xs">' +
									item.name +
								'</a>' +
							'</div>' +
						'</td>' +
						'<td class="py-3 px-3 align-middle font-mono text-xs text-slate-400">' + item.sku + '</td>' +
						'<td class="py-3 px-3 align-middle font-black text-slate-900 font-mono text-left">' + item.units_sold + '</td>' +
					'</tr>';
				});
				topSellingContainer.innerHTML = topHtml;
			} else {
				topSellingContainer.innerHTML = '<tr><td colspan="4" class="py-6 text-center text-xs font-bold text-slate-400">لا توجد مبيعات مسجلة للمنتجات خلال الفترة المحددة.</td></tr>';
			}
		}

		// 10. Update Browser URL History State
		if (window.history && window.history.pushState) {
			var newUrl = new URL(window.location.href);
			newUrl.searchParams.set('tab', 'overview');
			newUrl.searchParams.set('period', period);
			if (startDate && endDate) {
				newUrl.searchParams.set('start_date', startDate);
				newUrl.searchParams.set('end_date', endDate);
			} else {
				newUrl.searchParams.delete('start_date');
				newUrl.searchParams.delete('end_date');
			}
			window.history.pushState({ path: newUrl.toString() }, '', newUrl.toString());
		}
	}

})();
