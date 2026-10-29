/**
 * Ashrafco Admin Portal Interactive Application Script
 *
 * Extracted from template-admin-portal.php for modular maintainability.
 * Handles client-side tab switching, product media modals, dynamic category specs,
 * sale scheduling, stock toggles, and brand quick addition.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

		function switchPortalTab(tabId) {
			// If on sub-action or filter (e.g. new-product, edit-product, cat_id, cat_search), clicking tab navigates to clean list
			if (window.location.search.indexOf('action=') !== -1 || window.location.search.indexOf('cat_id=') !== -1 || window.location.search.indexOf('cat_search=') !== -1 || window.location.search.indexOf('cat_filter=') !== -1) {
				var portalUrl = (window.ASHRAFCO_PORTAL_CONFIG && window.ASHRAFCO_PORTAL_CONFIG.portalUrl) ? window.ASHRAFCO_PORTAL_CONFIG.portalUrl : window.location.pathname;
				window.location.href = portalUrl + '?tab=' + encodeURIComponent(tabId);
				return;
			}

			// Update Tab Content Visibility
			document.querySelectorAll('.tab-content').forEach(function (el) {
				el.classList.remove('active');
			});
			var portalUrl = (window.ASHRAFCO_PORTAL_CONFIG && window.ASHRAFCO_PORTAL_CONFIG.portalUrl) ? window.ASHRAFCO_PORTAL_CONFIG.portalUrl : window.location.pathname;
			var targetContent = document.getElementById('portal-tab-' + tabId);
			if (!targetContent) {
				window.location.href = portalUrl + '?tab=' + encodeURIComponent(tabId);
				return;
			}
			if (targetContent) {
				targetContent.classList.add('active');
			}

			// Update Tab Button Styles
			document.querySelectorAll('.portal-tab-btn').forEach(function (btn) {
				btn.classList.remove('border-primary', 'text-primary', 'bg-orange-50/50');
				btn.classList.add('border-transparent', 'text-slate-500');
			});

			var activeBtn = document.getElementById('tab-btn-' + tabId);
			if (activeBtn) {
				activeBtn.classList.remove('border-transparent', 'text-slate-500');
				activeBtn.classList.add('border-primary', 'text-primary', 'bg-orange-50/50');
			}

			// Keep URL query synchronized without page reload
			if (window.history && window.history.pushState) {
				var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
				window.history.pushState({ path: newUrl }, '', newUrl);
			}

			// Trigger window resize so Chart.js recalculates responsive dimensions if switching back to overview
			if (tabId === 'overview') {
				setTimeout(function () {
					window.dispatchEvent(new Event('resize'));
				}, 50);
			}

			// Reload users list when switching to users tab
			if (tabId === 'users' && window.AshrafcoUsersReload) {
				setTimeout(function () {
					window.AshrafcoUsersReload();
				}, 50);
			}

			// Reload promotions list when switching to promotions tab
			// Reload discounts list when switching to discounts tab
			if (tabId === 'shipping' && window.AshrafcoShippingReload) {
				setTimeout(function () {
					window.AshrafcoShippingReload();
				}, 50);
			}

			if (tabId === 'coupons' && window.AshrafcoCouponsReload) {
				setTimeout(function () {
					window.AshrafcoCouponsReload();
				}, 50);
			}

			if (tabId === 'discounts' && window.AshrafcoDiscountsReload) {
				setTimeout(function () {
					window.AshrafcoDiscountsReload();
				}, 50);
			}

			if (tabId === 'promotions' && window.AshrafcoPromotionsReload) {
				setTimeout(function () {
					window.AshrafcoPromotionsReload();
				}, 50);
			}
		}

		// =====================================================================
		// Category Management Interactions (Modal & Menus)
		// =====================================================================
		// =====================================================================
		function openCreateCategoryModal() {
			var modal = document.getElementById('category-crud-modal');
			if (!modal) return;

			var titleEl = document.getElementById('category-modal-title');
			if (titleEl) titleEl.textContent = 'إضافة تصنيف جديد';

			document.getElementById('modal-cat-action').value = 'create';
			document.getElementById('modal-cat-id').value = '0';
			document.getElementById('modal-cat-name').value = '';
			document.getElementById('modal-cat-parent').value = '0';
			document.getElementById('modal-cat-desc').value = '';
			document.getElementById('modal-cat-thumbnail-id').value = '0';
			document.getElementById('modal-cat-remove-thumb').value = '0';

			var preview = document.getElementById('modal-cat-preview');
			var placeholder = document.getElementById('modal-cat-preview-placeholder');
			var removeBtn = document.getElementById('modal-cat-remove-image-btn');
			if (preview) { preview.src = ''; preview.classList.add('hidden'); }
			if (placeholder) placeholder.classList.remove('hidden');
			if (removeBtn) removeBtn.classList.add('hidden');

			var submitBtn = document.getElementById('modal-cat-submit-btn');
			if (submitBtn) submitBtn.textContent = 'حفظ التصنيف';

			// Reset Category Specifications list
			categoryModalResetSpecs();

			modal.classList.remove('hidden');
		}

		function openEditCategoryModal(cat) {
			var modal = document.getElementById('category-crud-modal');
			if (!modal || !cat) return;

			var titleEl = document.getElementById('category-modal-title');
			if (titleEl) titleEl.textContent = 'تعديل التصنيف: ' + (cat.name || '');

			document.getElementById('modal-cat-action').value = 'edit';
			document.getElementById('modal-cat-id').value = cat.term_id || '0';
			document.getElementById('modal-cat-name').value = cat.name || '';
			document.getElementById('modal-cat-parent').value = cat.parent || '0';
			document.getElementById('modal-cat-desc').value = cat.description || '';
			document.getElementById('modal-cat-thumbnail-id').value = cat.thumbnail_id || '0';
			document.getElementById('modal-cat-remove-thumb').value = '0';

			var preview = document.getElementById('modal-cat-preview');
			var placeholder = document.getElementById('modal-cat-preview-placeholder');
			var removeBtn = document.getElementById('modal-cat-remove-image-btn');

			if (cat.thumbnail_url) {
				if (preview) { preview.src = cat.thumbnail_url; preview.classList.remove('hidden'); }
				if (placeholder) placeholder.classList.add('hidden');
				if (removeBtn) removeBtn.classList.remove('hidden');
			} else {
				if (preview) { preview.src = ''; preview.classList.add('hidden'); }
				if (placeholder) placeholder.classList.remove('hidden');
				if (removeBtn) removeBtn.classList.add('hidden');
			}

			var submitBtn = document.getElementById('modal-cat-submit-btn');
			if (submitBtn) submitBtn.textContent = 'تحديث بيانات التصنيف';

			// Load Category Specifications
			categoryModalLoadSpecs(cat.specs || []);

			modal.classList.remove('hidden');
		}

		function closeCategoryModal() {
			var modal = document.getElementById('category-crud-modal');
			if (modal) modal.classList.add('hidden');
		}

		// =====================================================================
		// Category Specifications Manager (Inside Category Modal)
		// =====================================================================
		function categoryModalResetSpecs() {
			var list = document.getElementById('modal-cat-specs-list');
			var empty = document.getElementById('modal-cat-specs-empty');
			var jsonInput = document.getElementById('modal-cat-specs-json');
			if (list) list.innerHTML = '';
			if (empty) empty.classList.remove('hidden');
			if (jsonInput) jsonInput.value = '[]';
		}

		function categoryModalLoadSpecs(specs) {
			categoryModalResetSpecs();
			if (Array.isArray(specs) && specs.length > 0) {
				specs.forEach(function (s) {
					categoryModalAddSpecRow(s);
				});
			}
		}

		function categoryModalAddSpecRow(data) {
			var list = document.getElementById('modal-cat-specs-list');
			var empty = document.getElementById('modal-cat-specs-empty');
			if (!list) return;
			if (empty) empty.classList.add('hidden');

			data = data || {};
			var specId = data.id || ('spec_' + Date.now() + '_' + Math.floor(Math.random() * 1000));
			var specName = data.name || '';
			var specType = data.type || 'text';
			var specUnit = data.unit || '';
			var specOpts = Array.isArray(data.options) ? data.options.join(', ') : (data.options || '');
			var specReq = data.required ? true : false;

			var row = document.createElement('div');
			row.className = 'cat-spec-row p-3 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2.5 transition-all text-right';
			row.dataset.id = specId;

			var safeName = String(specName).replace(/"/g, '&quot;');
			var safeUnit = String(specUnit).replace(/"/g, '&quot;');
			var safeOpts = String(specOpts).replace(/"/g, '&quot;');

			row.innerHTML =
				'<div class="flex items-center justify-between gap-2 border-b border-slate-200/60 pb-2">' +
				'<div class="flex items-center gap-2">' +
				'<span class="w-5 h-5 rounded-md bg-slate-200 text-slate-600 text-[11px] font-bold flex items-center justify-center cat-spec-index">1</span>' +
				'<span class="text-xs font-bold text-slate-800 cat-spec-title">' + (safeName || 'مواصفة جديدة') + '</span>' +
				'</div>' +
				'<div class="flex items-center gap-1">' +
				'<button type="button" onclick="categoryModalMoveSpecRow(this, -1)" class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-200 rounded transition-colors cursor-pointer" title="تحريك لأعلى">' +
				'<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>' +
				'</button>' +
				'<button type="button" onclick="categoryModalMoveSpecRow(this, 1)" class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-200 rounded transition-colors cursor-pointer" title="تحريك لأسفل">' +
				'<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>' +
				'</button>' +
				'<button type="button" onclick="categoryModalRemoveSpecRow(this)" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors cursor-pointer" title="حذف المواصفة">' +
				'<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>' +
				'</button>' +
				'</div>' +
				'</div>' +
				'<div class="grid grid-cols-1 sm:grid-cols-12 gap-2 text-right">' +
				'<div class="sm:col-span-5">' +
				'<label class="block text-[11px] font-bold text-slate-600 mb-1">اسم المواصفة <span class="text-rose-500">*</span></label>' +
				'<input type="text" value="' + safeName + '" placeholder="مثال: أقصى طول للقص" oninput="categoryModalUpdateRowTitle(this)" class="spec-input-name w-full text-xs font-bold py-1.5 px-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-primary text-slate-800">' +
				'</div>' +
				'<div class="sm:col-span-4">' +
				'<label class="block text-[11px] font-bold text-slate-600 mb-1">نوع الحقل</label>' +
				'<select onchange="categoryModalTypeChanged(this)" class="spec-input-type w-full text-xs font-bold py-1.5 px-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-primary text-slate-800">' +
				'<option value="text" ' + (specType === 'text' ? 'selected' : '') + '>نص (Text)</option>' +
				'<option value="number" ' + (specType === 'number' ? 'selected' : '') + '>رقم (Number)</option>' +
				'<option value="select" ' + (specType === 'select' ? 'selected' : '') + '>قائمة اختيارات (ComboBox)</option>' +
				'<option value="boolean" ' + (specType === 'boolean' ? 'selected' : '') + '>نعم / لا (Yes / No)</option>' +
				'</select>' +
				'</div>' +
				'<div class="sm:col-span-3">' +
				'<label class="block text-[11px] font-bold text-slate-600 mb-1">الوحدة (إن وجدت)</label>' +
				'<input type="text" value="' + safeUnit + '" placeholder="مثال: سم أو ملم" oninput="categoryModalSyncSpecsJson()" class="spec-input-unit w-full text-xs font-bold py-1.5 px-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-primary text-slate-800">' +
				'</div>' +
				'</div>' +
				'<div class="spec-options-wrap ' + (specType === 'select' ? '' : 'hidden') + '">' +
				'<label class="block text-[11px] font-bold text-slate-600 mb-1">خيارات القائمة <span class="text-slate-400 font-normal">(افصل بين الخيارات بفاصلة ،)</span></label>' +
				'<input type="text" value="' + safeOpts + '" placeholder="مثال: أداة يدوية, أداة كهربائية, أداة هوائية" oninput="categoryModalSyncSpecsJson()" class="spec-input-options w-full text-xs py-1.5 px-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-primary text-slate-800">' +
				'</div>' +
				'<div class="flex items-center gap-2 pt-1">' +
				'<label class="inline-flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer select-none">' +
				'<input type="checkbox" ' + (specReq ? 'checked' : '') + ' onchange="categoryModalSyncSpecsJson()" class="spec-input-required rounded text-primary focus:ring-primary">' +
				'<span>مواصفة مطلوبة (إلزامية في المنتج)</span>' +
				'</label>' +
				'</div>';

			list.appendChild(row);
			categoryModalUpdateIndexes();
			categoryModalSyncSpecsJson();
		}

		function categoryModalTypeChanged(select) {
			var row = select.closest('.cat-spec-row');
			if (!row) return;
			var optsWrap = row.querySelector('.spec-options-wrap');
			if (optsWrap) {
				if (select.value === 'select') {
					optsWrap.classList.remove('hidden');
				} else {
					optsWrap.classList.add('hidden');
				}
			}
			categoryModalSyncSpecsJson();
		}

		function categoryModalUpdateRowTitle(input) {
			var row = input.closest('.cat-spec-row');
			if (!row) return;
			var title = row.querySelector('.cat-spec-title');
			if (title) {
				title.textContent = input.value.trim() || 'مواصفة جديدة';
			}
			categoryModalSyncSpecsJson();
		}

		function categoryModalRemoveSpecRow(btn) {
			var row = btn.closest('.cat-spec-row');
			if (row) {
				row.remove();
				categoryModalUpdateIndexes();
				categoryModalSyncSpecsJson();
				var list = document.getElementById('modal-cat-specs-list');
				var empty = document.getElementById('modal-cat-specs-empty');
				if (list && list.children.length === 0 && empty) {
					empty.classList.remove('hidden');
				}
			}
		}

		function categoryModalMoveSpecRow(btn, dir) {
			var row = btn.closest('.cat-spec-row');
			if (!row) return;
			if (dir === -1 && row.previousElementSibling) {
				row.parentNode.insertBefore(row, row.previousElementSibling);
			} else if (dir === 1 && row.nextElementSibling) {
				row.parentNode.insertBefore(row.nextElementSibling, row);
			}
			categoryModalUpdateIndexes();
			categoryModalSyncSpecsJson();
		}

		function categoryModalUpdateIndexes() {
			var rows = document.querySelectorAll('#modal-cat-specs-list .cat-spec-row');
			rows.forEach(function (r, idx) {
				var idxEl = r.querySelector('.cat-spec-index');
				if (idxEl) idxEl.textContent = idx + 1;
			});
		}

		function categoryModalSyncSpecsJson() {
			var rows = document.querySelectorAll('#modal-cat-specs-list .cat-spec-row');
			var specs = [];
			rows.forEach(function (r, idx) {
				var nameEl = r.querySelector('.spec-input-name');
				var name = nameEl ? nameEl.value.trim() : '';
				if (!name) return;
				var typeEl = r.querySelector('.spec-input-type');
				var type = typeEl ? typeEl.value : 'text';
				var unitEl = r.querySelector('.spec-input-unit');
				var unit = unitEl ? unitEl.value.trim() : '';
				var optsEl = r.querySelector('.spec-input-options');
				var optionsStr = optsEl ? optsEl.value : '';
				var reqEl = r.querySelector('.spec-input-required');
				var required = (reqEl && reqEl.checked) ? 1 : 0;
				var id = r.dataset.id || ('spec_' + (idx + 1));

				var options = [];
				if (type === 'select' && optionsStr) {
					options = optionsStr.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
				}

				specs.push({
					id: id,
					name: name,
					type: type,
					unit: unit,
					options: options,
					required: required,
					order: idx + 1
				});
			});

			var jsonInput = document.getElementById('modal-cat-specs-json');
			if (jsonInput) {
				jsonInput.value = JSON.stringify(specs);
			}
		}

		function toggleCategoryMenu(e, menuId) {
			if (e) {
				e.preventDefault();
				e.stopPropagation();
			}
			var targetMenu = document.getElementById(menuId);
			var allMenus = document.querySelectorAll('.cat-overflow-menu');
			allMenus.forEach(function (m) {
				if (m !== targetMenu) m.classList.add('hidden');
			});
			if (targetMenu) {
				targetMenu.classList.toggle('hidden');
			}
		}

		// Close open category dropdown menus or modal on outside click
		window.addEventListener('click', function (e) {
			var allMenus = document.querySelectorAll('.cat-overflow-menu');
			allMenus.forEach(function (m) {
				m.classList.add('hidden');
			});

			var modal = document.getElementById('category-crud-modal');
			if (modal && !modal.classList.contains('hidden') && e.target === modal) {
				closeCategoryModal();
			}
		});

		// Product Form Scripts & Media Management
		document.addEventListener('DOMContentLoaded', function () {
			// =========================================================================
			// 1. PRODUCT IMAGES & GALLERY (Max 15 total images)
			// =========================================================================
			var mainImage = (window.ASHRAFCO_PORTAL_CONFIG && window.ASHRAFCO_PORTAL_CONFIG.mainImage) ? window.ASHRAFCO_PORTAL_CONFIG.mainImage : null;
			var galleryImages = (window.ASHRAFCO_PORTAL_CONFIG && window.ASHRAFCO_PORTAL_CONFIG.galleryImages) ? window.ASHRAFCO_PORTAL_CONFIG.galleryImages : [];
			var maxImagesLimit = 15;

			var previewImg = document.getElementById('portal-main-image-preview');
			var placeholder = document.getElementById('portal-main-placeholder');
			var badge = document.getElementById('portal-main-image-badge');
			var thumbnailsContainer = document.getElementById('portal-thumbnails-container');
			var counterBadge = document.getElementById('portal-images-counter-badge');
			var limitAlert = document.getElementById('portal-images-limit-alert');
			var mediaIdInput = document.getElementById('portal_media_image_id');
			var galleryIdsInput = document.getElementById('portal_gallery_image_ids');
			var removeFlag = document.getElementById('portal_remove_image_flag');

			function renderImagesUI() {
				if (!thumbnailsContainer) return;

				var totalCount = (mainImage ? 1 : 0) + galleryImages.length;

				// Update Main Preview
				if (mainImage && mainImage.url) {
					if (previewImg) {
						previewImg.src = mainImage.url;
						previewImg.classList.remove('hidden');
					}
					if (placeholder) placeholder.classList.add('hidden');
					if (badge) badge.classList.remove('hidden');
				} else {
					if (previewImg) {
						previewImg.src = '';
						previewImg.classList.add('hidden');
					}
					if (placeholder) placeholder.classList.remove('hidden');
					if (badge) badge.classList.add('hidden');
				}

				// Update Counter
				if (counterBadge) {
					counterBadge.textContent = totalCount + ' / ' + maxImagesLimit;
				}

				// Update Limit Alert
				if (limitAlert) {
					if (totalCount >= maxImagesLimit) {
						limitAlert.classList.remove('hidden');
					} else {
						limitAlert.classList.add('hidden');
					}
				}

				// Update Hidden Form Inputs
				if (mediaIdInput) {
					mediaIdInput.value = mainImage ? mainImage.id : '';
				}
				if (galleryIdsInput) {
					galleryIdsInput.value = galleryImages.map(function (item) { return item.id; }).join(',');
				}
				if (removeFlag) {
					removeFlag.value = (!mainImage && galleryImages.length === 0) ? '1' : '0';
				}

				// Re-render Thumbnails Container
				thumbnailsContainer.innerHTML = '';

				// A. Main Image Thumbnail (Red border & primary indicator)
				if (mainImage && mainImage.url) {
					var mainThumb = document.createElement('div');
					mainThumb.className = 'w-14 h-14 rounded-xl border-2 border-rose-600 overflow-hidden relative group cursor-pointer flex-shrink-0 ring-2 ring-rose-100 shadow-2xs';
					mainThumb.title = 'الصورة الرئيسية الحالية للمنتج';
					mainThumb.innerHTML = '<img src="' + mainImage.url + '" class="w-full h-full object-cover">' +
						'<button type="button" class="portal-thumb-del absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] opacity-0 group-hover:opacity-100 transition-opacity" title="إزالة الصورة">✕</button>';

					mainThumb.querySelector('.portal-thumb-del').addEventListener('click', function (e) {
						e.stopPropagation();
						if (galleryImages.length > 0) {
							// Promote first gallery image to main
							mainImage = galleryImages.shift();
						} else {
							mainImage = null;
						}
						renderImagesUI();
					});

					thumbnailsContainer.appendChild(mainThumb);
				}

				// B. Gallery Thumbnails
				galleryImages.forEach(function (item, idx) {
					var gThumb = document.createElement('div');
					gThumb.className = 'w-14 h-14 rounded-xl border border-slate-200 overflow-hidden relative group cursor-pointer flex-shrink-0 hover:border-slate-400 transition-all shadow-2xs';
					gThumb.title = 'اضغط لتعيينها كصورة رئيسية';
					gThumb.innerHTML = '<img src="' + item.url + '" class="w-full h-full object-cover">' +
						'<div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-[9px] text-white font-bold text-center px-1">تعيين كرئيسية</div>' +
						'<button type="button" class="portal-thumb-del absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] opacity-0 group-hover:opacity-100 transition-opacity z-10" title="إزالة الصورة">✕</button>';

					gThumb.addEventListener('click', function () {
						// Swap this gallery image with current main image
						var oldMain = mainImage;
						mainImage = item;
						galleryImages.splice(idx, 1);
						if (oldMain) {
							galleryImages.unshift(oldMain);
						}
						renderImagesUI();
					});

					gThumb.querySelector('.portal-thumb-del').addEventListener('click', function (e) {
						e.stopPropagation();
						galleryImages.splice(idx, 1);
						renderImagesUI();
					});

					thumbnailsContainer.appendChild(gThumb);
				});

				// C. Add Images Button (Square dashed button)
				var addBtn = document.createElement('button');
				addBtn.type = 'button';
				addBtn.id = 'portal-add-images-btn';
				if (totalCount >= maxImagesLimit) {
					addBtn.className = 'w-14 h-14 rounded-xl border-2 border-dashed border-slate-200 bg-slate-100 text-slate-400 flex flex-col items-center justify-center flex-shrink-0 cursor-not-allowed opacity-60';
					addBtn.disabled = true;
					addBtn.title = 'تم بلوغ الحد الأقصى (15 صورة)';
				} else {
					addBtn.className = 'w-14 h-14 rounded-xl border-2 border-dashed border-slate-300 hover:border-rose-400 hover:bg-rose-50/30 text-slate-400 hover:text-rose-600 flex flex-col items-center justify-center flex-shrink-0 transition-colors cursor-pointer text-center';
					addBtn.title = 'إضافة صور جديدة للمنتج';
				}
				addBtn.innerHTML = '<svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg><span class="text-[9px] font-bold">إضافة صور</span>';

				addBtn.addEventListener('click', function (e) {
					e.preventDefault();
					portalOpenMediaLibrary();
				});

				thumbnailsContainer.appendChild(addBtn);
			}

			function portalOpenMediaLibrary() {
				var currentTotal = (mainImage ? 1 : 0) + galleryImages.length;
				var remaining = maxImagesLimit - currentTotal;
				if (remaining <= 0) {
					alert('تنبيه: لقد بلغت الحد الأقصى المسموح به لصور المنتج (15 صورة شاملة الصورة الرئيسية).');
					return;
				}

				if (typeof wp !== 'undefined' && wp.media) {
					var frame = wp.media({
						title: 'اختر صور المنتج (المتبقي: ' + remaining + ' صورة كحد أقصى)',
						button: { text: 'استخدام الصور المحددة' },
						multiple: true,
						library: { type: 'image' }
					});

					frame.on('select', function () {
						var selection = frame.state().get('selection').toJSON();
						if (selection.length > remaining) {
							alert('تنبيه: قمت بتحديد ' + selection.length + ' صورة، ولكن الحد المتبقي المسموح به هو ' + remaining + ' فقط. سيتم أخذ أول ' + remaining + ' صورة.');
							selection = selection.slice(0, remaining);
						}

						selection.forEach(function (item) {
							if (!mainImage) {
								mainImage = { id: item.id, url: item.url };
							} else {
								var alreadyMain = (mainImage.id == item.id);
								var alreadyGallery = galleryImages.some(function (g) { return g.id == item.id; });
								if (!alreadyMain && !alreadyGallery) {
									var nowTotal = (mainImage ? 1 : 0) + galleryImages.length;
									if (nowTotal < maxImagesLimit) {
										galleryImages.push({ id: item.id, url: item.url });
									}
								}
							}
						});

						renderImagesUI();
					});

					frame.open();
				} else {
					// Fallback to direct file input if WP media not available
					var directInput = document.getElementById('portal_direct_image_input');
					if (directInput) directInput.click();
				}
			}

			// Initial Render of Images
			renderImagesUI();

			// Direct file upload change listener
			var directImgInput = document.getElementById('portal_direct_image_input');
			if (directImgInput) {
				directImgInput.addEventListener('change', function () {
					if (this.files && this.files[0]) {
						var reader = new FileReader();
						reader.onload = function (evt) {
							mainImage = { id: 0, url: evt.target.result };
							renderImagesUI();
						};
						reader.readAsDataURL(this.files[0]);
					}
				});
			}

			// =========================================================================
			// 2. SALE END DATE SCHEDULING CONTROLS
			// =========================================================================
			window.portalSetSaleExpiryMode = function (mode) {
				var noneBtn = document.getElementById('sale_expiry_btn_none');
				var scheduledBtn = document.getElementById('sale_expiry_btn_scheduled');
				var dateInput = document.getElementById('sale_end_date');
				var typeInput = document.getElementById('sale_expiry_type_input');

				if (typeInput) typeInput.value = mode;

				if (mode === 'none') {
					if (noneBtn) {
						noneBtn.className = 'px-2 py-0.5 rounded-md transition-all bg-white text-slate-900 shadow-2xs font-extrabold';
					}
					if (scheduledBtn) {
						scheduledBtn.className = 'px-2 py-0.5 rounded-md transition-all text-slate-500 hover:text-slate-700';
					}
					if (dateInput) {
						dateInput.value = '';
						dateInput.disabled = true;
					}
				} else {
					if (noneBtn) {
						noneBtn.className = 'px-2 py-0.5 rounded-md transition-all text-slate-500 hover:text-slate-700';
					}
					if (scheduledBtn) {
						scheduledBtn.className = 'px-2 py-0.5 rounded-md transition-all bg-white text-slate-900 shadow-2xs font-extrabold';
					}
					if (dateInput) {
						dateInput.disabled = false;
						dateInput.focus();
					}
				}
			};

			// =========================================================================
			// 3. STOCK STATUS & QUANTITY CONTROLS
			// =========================================================================
			window.portalToggleStockQuantity = function (isChecked) {
				var qtyWrapper = document.getElementById('stock_quantity_wrapper');
				if (qtyWrapper) {
					if (isChecked) {
						qtyWrapper.classList.remove('hidden');
					} else {
						qtyWrapper.classList.add('hidden');
					}
				}
			};

			window.portalSetStockStatus = function (val) {
				var input = document.getElementById('stock_status_input');
				if (input) input.value = val;

				var pills = document.querySelectorAll('#portal-stock-status-group .stock-status-pill');
				pills.forEach(function (pill) {
					var pillStatus = pill.getAttribute('data-status');
					var dot = pill.querySelector('span span');

					pill.classList.remove('bg-emerald-50', 'text-emerald-800', 'border-emerald-300',
						'bg-amber-50', 'text-amber-800', 'border-amber-300',
						'bg-rose-50', 'text-rose-800', 'border-rose-300',
						'bg-slate-100', 'text-slate-900', 'border-slate-300',
						'shadow-2xs', 'font-extrabold');
					pill.classList.add('bg-white', 'text-slate-600', 'border-slate-200');

					if (dot) {
						dot.className = 'w-1.5 h-1.5 rounded-full bg-transparent';
					}

					if (pillStatus === val) {
						pill.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
						pill.classList.add('shadow-2xs');

						if (val === 'instock') {
							pill.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
							if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-600';
						} else if (val === 'lowstock') {
							pill.classList.add('bg-amber-50', 'text-amber-800', 'border-amber-300');
							if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-amber-600';
						} else if (val === 'outofstock') {
							pill.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-300');
							if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-rose-600';
						} else if (val === 'onbackorder') {
							pill.classList.add('bg-slate-100', 'text-slate-900', 'border-slate-300', 'font-extrabold');
							if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-slate-700';
						}
					}
				});
			};

			// =========================================================================
			// 4. BASIC & CATEGORY SPECIFICATIONS LOGIC
			// =========================================================================

			// Brand Selection & Quick Add
			window.handleBrandSelectChange = function (select) {
				var customWrap = document.getElementById('product_brand_custom_wrapper');
				if (select.value === 'custom') {
					if (customWrap) customWrap.classList.remove('hidden');
					var input = document.getElementById('product_brand_custom');
					if (input) input.focus();
				} else {
					if (customWrap) customWrap.classList.add('hidden');
				}
			};

			window.toggleNewBrandInput = function () {
				var customWrap = document.getElementById('product_brand_custom_wrapper');
				if (!customWrap) return;
				if (customWrap.classList.contains('hidden')) {
					customWrap.classList.remove('hidden');
					var input = document.getElementById('product_brand_custom');
					if (input) input.focus();
				} else {
					customWrap.classList.add('hidden');
				}
			};

			window.cancelNewBrandInput = function () {
				var customWrap = document.getElementById('product_brand_custom_wrapper');
				var select = document.getElementById('product_brand_select');
				if (customWrap) customWrap.classList.add('hidden');
				if (select && select.value === 'custom') {
					select.value = '';
				}
			};

			window.saveQuickNewBrand = function () {
				var input = document.getElementById('product_brand_custom');
				var select = document.getElementById('product_brand_select');
				var customWrap = document.getElementById('product_brand_custom_wrapper');
				if (!input || !select) return;

				var brandName = input.value.trim();
				if (!brandName) {
					alert('يرجى إدخال اسم العلامة التجارية أولاً.');
					input.focus();
					return;
				}

				// Check if option already exists
				var existingOption = null;
				for (var i = 0; i < select.options.length; i++) {
					if (select.options[i].value === brandName || select.options[i].text === brandName) {
						existingOption = select.options[i];
						break;
					}
				}

				if (existingOption) {
					select.value = existingOption.value;
				} else {
					var newOpt = document.createElement('option');
					newOpt.value = brandName;
					newOpt.text = brandName;
					newOpt.selected = true;
					// Insert right before custom option
					var customOpt = select.querySelector('option[value="custom"]');
					if (customOpt) {
						select.insertBefore(newOpt, customOpt);
					} else {
						select.appendChild(newOpt);
					}
				}

				if (customWrap) customWrap.classList.add('hidden');
				input.value = '';
			};

			// Warranty & Country Selection Handlers
			window.handleWarrantyChange = function (select) {
				var wrap = document.getElementById('product_warranty_custom_wrap');
				if (!wrap) return;
				if (select.value === 'ضمان مخصص') {
					wrap.classList.remove('hidden');
					var inp = document.getElementById('product_warranty_custom');
					if (inp) inp.focus();
				} else {
					wrap.classList.add('hidden');
				}
			};

			window.handleGenericCustomToggle = function (select, wrapId, inputId) {
				var wrap = document.getElementById(wrapId);
				if (!wrap) return;
				if (select.value === 'custom') {
					wrap.classList.remove('hidden');
					var inp = document.getElementById(inputId);
					if (inp) inp.focus();
				} else {
					wrap.classList.add('hidden');
				}
			};

			window.handleCountryChange = function (select) {
				var wrap = document.getElementById('product_country_custom_wrap');
				if (!wrap) return;
				if (select.value === 'أخرى') {
					wrap.classList.remove('hidden');
					var inp = document.getElementById('product_country_origin_custom');
					if (inp) inp.focus();
				} else {
					wrap.classList.add('hidden');
				}
			};

			// Dynamic Category Specifications Renderer
			window.portalRenderCategorySpecs = function (catId) {
				var container = document.getElementById('portal-category-specs-container');
				var nameDisplay = document.getElementById('portal-cat-name-display');
				if (!container) return;

				// 1. Preserve current inputs in window.ASHRAFCO_SAVED_SPEC_VALUES
				var activeInputs = container.querySelectorAll('[data-spec-id]');
				activeInputs.forEach(function (inp) {
					if (inp.dataset.specId) {
						window.ASHRAFCO_SAVED_SPEC_VALUES[inp.dataset.specId] = inp.value;
					}
				});

				catId = parseInt(catId, 10);
				if (!catId || isNaN(catId)) {
					if (nameDisplay) nameDisplay.textContent = '---';
					container.innerHTML = '<div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-xs font-medium flex items-center gap-2.5">' +
						'<svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' +
						'<span>يرجى اختيار التصنيف الرئيسي للمنتج لعرض مواصفات التصنيف المحددة له.</span>' +
						'</div>';
					return;
				}

				var catData = window.ASHRAFCO_CATEGORY_SPECS && window.ASHRAFCO_CATEGORY_SPECS[catId];
				var catName = catData ? catData.name : '';
				var specs = (catData && Array.isArray(catData.specs)) ? catData.specs : [];

				if (nameDisplay) {
					nameDisplay.textContent = catName || ('تصنيف #' + catId);
				}

				if (specs.length === 0) {
					container.innerHTML = '<div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 text-amber-900 text-xs font-medium flex items-center gap-2.5">' +
						'<svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' +
						'<span>لا توجد مواصفات معرفة لهذا التصنيف حالياً. يمكنك إضافة مواصفات لهذا التصنيف من شاشة إدارة التصنيفات.</span>' +
						'</div>';
					return;
				}

				// Build Responsive 2-column Grid matching reference screenshot
				var html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">';
				specs.forEach(function (spec) {
					var val = window.ASHRAFCO_SAVED_SPEC_VALUES[spec.id] !== undefined ? window.ASHRAFCO_SAVED_SPEC_VALUES[spec.id] : '';
					var reqStar = spec.required ? '<span class="text-rose-500">*</span>' : '';
					var safeSpecName = String(spec.name).replace(/"/g, '&quot;');
					var safeUnit = String(spec.unit || '').replace(/"/g, '&quot;');
					var safeVal = String(val).replace(/"/g, '&quot;');

					html += '<div class="space-y-1.5">';
					html += '<label class="block text-xs font-bold text-slate-700">' + spec.name + ' ' + reqStar + '</label>';

					if (spec.type === 'number') {
						html += '<div class="flex items-stretch rounded-xl border border-slate-200 focus-within:border-primary overflow-hidden bg-white shadow-2xs">';
						html += '<input type="number" step="any" data-spec-id="' + spec.id + '" name="cat_spec_values[' + spec.id + ']" value="' + safeVal + '" ' + (spec.required ? 'required' : '') + ' placeholder="0" class="w-full text-xs font-bold py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white focus:outline-none text-slate-800 text-right">';
						if (spec.unit) {
							html += '<span class="bg-slate-100/80 border-r border-slate-200 text-slate-600 text-xs font-bold px-3.5 py-2.5 flex items-center justify-center select-none min-w-[52px]">' + safeUnit + '</span>';
						}
						html += '</div>';
					} else if (spec.type === 'select') {
						var opts = Array.isArray(spec.options) ? spec.options : [];
						html += '<div class="relative">';
						html += '<select data-spec-id="' + spec.id + '" name="cat_spec_values[' + spec.id + ']" ' + (spec.required ? 'required' : '') + ' class="w-full text-xs font-bold py-2.5 pr-4 pl-9 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 appearance-none cursor-pointer">';
						html += '<option value="">اختر...</option>';
						opts.forEach(function (opt) {
							var isSel = (String(val).trim() === String(opt).trim()) ? 'selected' : '';
							html += '<option value="' + String(opt).replace(/"/g, '&quot;') + '" ' + isSel + '>' + opt + '</option>';
						});
						if (val && !opts.includes(val)) {
							html += '<option value="' + safeVal + '" selected>' + val + '</option>';
						}
						html += '</select>';
						html += '<span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">';
						html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>';
						html += '</span>';
						html += '</div>';
					} else if (spec.type === 'boolean') {
						html += '<div class="relative">';
						html += '<select data-spec-id="' + spec.id + '" name="cat_spec_values[' + spec.id + ']" ' + (spec.required ? 'required' : '') + ' class="w-full text-xs font-bold py-2.5 pr-4 pl-9 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 appearance-none cursor-pointer">';
						html += '<option value="">غير محدد</option>';
						html += '<option value="نعم" ' + (val === 'نعم' ? 'selected' : '') + '>نعم</option>';
						html += '<option value="لا" ' + (val === 'لا' ? 'selected' : '') + '>لا</option>';
						html += '</select>';
						html += '<span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">';
						html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>';
						html += '</span>';
						html += '</div>';
					} else {
						// text
						if (spec.unit) {
							html += '<div class="flex items-stretch rounded-xl border border-slate-200 focus-within:border-primary overflow-hidden bg-white shadow-2xs">';
							html += '<input type="text" data-spec-id="' + spec.id + '" name="cat_spec_values[' + spec.id + ']" value="' + safeVal + '" ' + (spec.required ? 'required' : '') + ' placeholder="..." class="w-full text-xs font-medium py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white focus:outline-none text-slate-800 text-right">';
							html += '<span class="bg-slate-100/80 border-r border-slate-200 text-slate-600 text-xs font-bold px-3.5 py-2.5 flex items-center justify-center select-none min-w-[52px]">' + safeUnit + '</span>';
							html += '</div>';
						} else {
							html += '<input type="text" data-spec-id="' + spec.id + '" name="cat_spec_values[' + spec.id + ']" value="' + safeVal + '" ' + (spec.required ? 'required' : '') + ' placeholder="..." class="w-full text-xs font-medium py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">';
						}
					}

					html += '<input type="hidden" name="cat_spec_meta[' + spec.id + '][name]" value="' + safeSpecName + '">';
					html += '<input type="hidden" name="cat_spec_meta[' + spec.id + '][unit]" value="' + safeUnit + '">';
					html += '</div>';
				});
				html += '</div>';

				container.innerHTML = html;
			};

			window.portalToggleWatchSpecificFields = function (catId) {
				catId = parseInt(catId, 10);
				var catData = window.ASHRAFCO_CATEGORY_SPECS && window.ASHRAFCO_CATEGORY_SPECS[catId];
				var isAccessory = catData && (catData.slug === 'watch-accessories' || (catData.name && (catData.name.indexOf('إكسسوار') !== -1 || catData.name.indexOf('اكسسوار') !== -1)));
				var watchOnlyFields = document.querySelectorAll('.watch-only-spec');
				watchOnlyFields.forEach(function (el) {
					if (isAccessory) {
						el.classList.add('hidden');
					} else {
						el.classList.remove('hidden');
					}
				});
			};

			// Bind category select change to dynamic category specifications renderer
			var catMainSelect = document.getElementById('product_category_main');
			if (catMainSelect) {
				catMainSelect.addEventListener('change', function () {
					window.portalRenderCategorySpecs(this.value);
					window.portalToggleWatchSpecificFields(this.value);
				});
				// Initial render on page load
				window.portalRenderCategorySpecs(catMainSelect.value);
				window.portalToggleWatchSpecificFields(catMainSelect.value);
			}



			// =========================================================================
			// 6. OPTIONAL PRODUCT FILE UPLOAD (PDF, ZIP, DOC, DOCX up to 20MB)
			// =========================================================================
			window.portalChooseProductFile = function () {
				var fileIdInput = document.getElementById('portal_media_file_id');
				var previewBox = document.getElementById('portal-file-preview-box');
				var placeholderBox = document.getElementById('portal-file-placeholder');
				var nameText = document.getElementById('portal-file-name-text');
				var removeFileFlag = document.getElementById('portal_remove_file_flag');

				if (typeof wp !== 'undefined' && wp.media) {
					var fileFrame = wp.media({
						title: 'اختر ملف الكتالوج أو دليل الاستخدام',
						button: { text: 'استخدام هذا الملف' },
						multiple: false
					});

					fileFrame.on('select', function () {
						var attachment = fileFrame.state().get('selection').first().toJSON();
						if (fileIdInput) fileIdInput.value = attachment.id;
						if (nameText) nameText.textContent = attachment.title || attachment.filename || 'ملف مرفق';
						if (previewBox) previewBox.classList.remove('hidden');
						if (placeholderBox) placeholderBox.classList.add('hidden');
						if (removeFileFlag) removeFileFlag.value = '0';
					});

					fileFrame.open();
				} else {
					var directInput = document.getElementById('portal_direct_file_input');
					if (directInput) directInput.click();
				}
			};

			window.portalRemoveProductFile = function () {
				var fileIdInput = document.getElementById('portal_media_file_id');
				var previewBox = document.getElementById('portal-file-preview-box');
				var placeholderBox = document.getElementById('portal-file-placeholder');
				var removeFileFlag = document.getElementById('portal_remove_file_flag');
				var directInput = document.getElementById('portal_direct_file_input');

				if (fileIdInput) fileIdInput.value = '';
				if (previewBox) previewBox.classList.add('hidden');
				if (placeholderBox) placeholderBox.classList.remove('hidden');
				if (removeFileFlag) removeFileFlag.value = '1';
				if (directInput) directInput.value = '';
			};

			var directFileInput = document.getElementById('portal_direct_file_input');
			if (directFileInput) {
				directFileInput.addEventListener('change', function () {
					if (this.files && this.files[0]) {
						var f = this.files[0];
						var previewBox = document.getElementById('portal-file-preview-box');
						var placeholderBox = document.getElementById('portal-file-placeholder');
						var nameText = document.getElementById('portal-file-name-text');
						var sizeText = document.getElementById('portal-file-size-text');
						var removeFileFlag = document.getElementById('portal_remove_file_flag');

						if (nameText) nameText.textContent = f.name;
						if (sizeText) sizeText.textContent = (f.size / (1024 * 1024)).toFixed(2) + ' MB';
						if (previewBox) previewBox.classList.remove('hidden');
						if (placeholderBox) placeholderBox.classList.add('hidden');
						if (removeFileFlag) removeFileFlag.value = '0';
					}
				});
			}

			// =========================================================================
			// 7. FORM SUBMISSION LOADING STATE & SAFETY CHECKS
			// =========================================================================
			var productForm = document.getElementById('ashrafco-product-form');
			var submitBtn = document.getElementById('portal-product-submit-btn');
			var submitBtnText = document.getElementById('portal-submit-btn-text');

			if (productForm) {
				productForm.addEventListener('submit', function (e) {

					// Validate 15 images limit on submit
					var totalImgs = (mainImage ? 1 : 0) + galleryImages.length;
					if (totalImgs > maxImagesLimit) {
						e.preventDefault();
						alert('خطأ: لا يمكن إرسال أكثر من 15 صورة للمنتج.');
						return false;
					}

					// Show loading state
					if (submitBtn) {
						submitBtn.disabled = true;
						submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
						if (submitBtnText) submitBtnText.textContent = 'جاري حفظ التغييرات...';
					}
				});
			}

			// Dynamic communication when unpublishing an already published product
			var originalStatusEl = document.getElementById('original_product_status');
			var originalStatus = originalStatusEl ? originalStatusEl.value : '';
			var isPublishedToggle = document.getElementById('is_published');
			var unpublishWarning = document.getElementById('unpublish-warning');

			if (isPublishedToggle && unpublishWarning) {
				isPublishedToggle.addEventListener('change', function () {
					if (originalStatus === 'publish' && !this.checked) {
						unpublishWarning.classList.remove('hidden');
						unpublishWarning.classList.add('flex');
					} else {
						unpublishWarning.classList.add('hidden');
						unpublishWarning.classList.remove('flex');
					}
				});
			}

			// =========================================================================
			// 8. CATEGORY MODAL IMAGE SELECTOR (Preserved for Category Operations)
			// =========================================================================
			var catMediaBtn = document.getElementById('modal-cat-select-image-btn');
			var catMediaIdInput = document.getElementById('modal-cat-thumbnail-id');
			var catPreviewImg = document.getElementById('modal-cat-preview');
			var catPlaceholder = document.getElementById('modal-cat-preview-placeholder');
			var catRemoveBtn = document.getElementById('modal-cat-remove-image-btn');
			var catRemoveFlag = document.getElementById('modal-cat-remove-thumb');

			if (catMediaBtn && typeof wp !== 'undefined' && wp.media) {
				var cat_file_frame;
				catMediaBtn.addEventListener('click', function (e) {
					e.preventDefault();
					if (cat_file_frame) {
						cat_file_frame.open();
						return;
					}
					cat_file_frame = wp.media({
						title: 'اختر صورة التصنيف',
						button: { text: 'استخدام هذه الصورة' },
						multiple: false
					});
					cat_file_frame.on('select', function () {
						var attachment = cat_file_frame.state().get('selection').first().toJSON();
						if (catMediaIdInput) catMediaIdInput.value = attachment.id;
						if (catPreviewImg) {
							catPreviewImg.src = attachment.url;
							catPreviewImg.classList.remove('hidden');
						}
						if (catPlaceholder) catPlaceholder.classList.add('hidden');
						if (catRemoveBtn) catRemoveBtn.classList.remove('hidden');
						if (catRemoveFlag) catRemoveFlag.value = '0';
					});
					cat_file_frame.open();
				});
			}

			if (catRemoveBtn) {
				catRemoveBtn.addEventListener('click', function (e) {
					e.preventDefault();
					if (catMediaIdInput) catMediaIdInput.value = '0';
					if (catPreviewImg) {
						catPreviewImg.src = '';
						catPreviewImg.classList.add('hidden');
					}
					if (catPlaceholder) catPlaceholder.classList.remove('hidden');
					if (catRemoveBtn) catRemoveBtn.classList.add('hidden');
					if (catRemoveFlag) catRemoveFlag.value = '1';
				});
			}
		
		// =========================================================================
		// 5. BRAND MANAGEMENT MASTER CONTROLLER
		// =========================================================================
		(function () {
			var brandsData = [];
			var activeBrandId = null;
			var activeBrandName = '';
			var searchDebounceTimer = null;

			function getAjaxUrl() {
				return (window.ASHRAFCO_PORTAL_CONFIG && window.ASHRAFCO_PORTAL_CONFIG.ajaxUrl) ? window.ASHRAFCO_PORTAL_CONFIG.ajaxUrl : '/wp-admin/admin-ajax.php';
			}

			function getNonce() {
				return (window.ASHRAFCO_PORTAL_CONFIG && window.ASHRAFCO_PORTAL_CONFIG.nonce) ? window.ASHRAFCO_PORTAL_CONFIG.nonce : '';
			}

			// Open Main Brands Modal
			window.openBrandsModal = function () {
				var modal = document.getElementById('brand-management-modal');
				if (!modal) return;

				window.backToBrandsList();

				var addInput = document.getElementById('brand-new-name-input');
				if (addInput) addInput.value = '';
				var feedback = document.getElementById('brand-add-feedback');
				if (feedback) { feedback.className = 'hidden mt-2 text-xs font-medium'; feedback.textContent = ''; }

				if (window.ASHRAFCO_PORTAL_BRANDS && Array.isArray(window.ASHRAFCO_PORTAL_BRANDS)) {
					brandsData = window.ASHRAFCO_PORTAL_BRANDS;
					updateHeaderBadge(brandsData.length);
					renderBrandsTable(brandsData);
				}
				fetchBrands();

				modal.classList.remove('hidden');
			};

			window.closeBrandsModal = function () {
				var modal = document.getElementById('brand-management-modal');
				if (modal) modal.classList.add('hidden');
			};

			function fetchBrands(callback) {
				var formData = new FormData();
				formData.append('action', 'ashrafco_get_brands');
				formData.append('security', getNonce());

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (res.success && res.data && res.data.brands) {
						brandsData = res.data.brands;
						window.ASHRAFCO_PORTAL_BRANDS = brandsData;
						updateHeaderBadge(brandsData.length);
						renderBrandsTable(brandsData);
						if (callback) callback();
					}
				})
				.catch(function (err) {
					console.error('Error fetching brands:', err);
				});
			}

			function updateHeaderBadge(count) {
				var badge = document.getElementById('portal-brands-header-badge');
				if (badge) badge.textContent = count;
				var counter = document.getElementById('brands-total-counter');
				if (counter) counter.textContent = count;
			}

			function renderBrandsTable(list) {
				var tbody = document.getElementById('brands-table-tbody');
				var empty = document.getElementById('brands-empty-state');
				if (!tbody) return;

				tbody.innerHTML = '';
				if (!list || list.length === 0) {
					if (empty) empty.classList.remove('hidden');
					return;
				}
				if (empty) empty.classList.add('hidden');

				list.forEach(function (brand) {
					var tr = document.createElement('tr');
					tr.className = 'hover:bg-slate-50/80 transition-colors';

					var count = brand.count || 0;
					var countBadgeClass = count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-slate-100 text-slate-500';

					tr.innerHTML = '<td class="py-3 px-4 font-bold text-slate-900">' +
						'<div class="flex items-center gap-2.5">' +
							'<span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0">' +
								'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
									'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />' +
								'</svg>' +
							'</span>' +
							'<div>' +
								'<span class="block text-xs font-bold text-slate-800">' + escapeHtml(brand.name) + '</span>' +
								'<span class="block text-[10px] text-slate-400 font-mono">' + escapeHtml(brand.slug || '') + '</span>' +
							'</div>' +
						'</div>' +
					'</td>' +
					'<td class="py-3 px-4 text-center">' +
						'<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold font-mono ' + countBadgeClass + '">' +
							count + ' ' + (count === 1 ? 'منتج' : 'منتجات') +
						'</span>' +
					'</td>' +
					'<td class="py-3 px-4 text-left">' +
						'<div class="inline-flex items-center gap-1.5">' +
							'<button type="button" onclick="openBrandProductsManager(' + brand.id + ', \'' + escapeJs(brand.name) + '\')" ' +
								'title="إدارة المنتجات المرتبطة" ' +
								'class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 transition-colors cursor-pointer">' +
								'<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
									'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />' +
								'</svg>' +
								'<span>المنتجات</span>' +
							'</button>' +
							'<button type="button" onclick="promptDeleteBrand(' + brand.id + ', \'' + escapeJs(brand.name) + '\', ' + count + ')" ' +
								'title="حذف الماركة" ' +
								'class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-rose-500 hover:text-white hover:bg-rose-600 border border-rose-200/80 transition-colors cursor-pointer">' +
								'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
									'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />' +
								'</svg>' +
							'</button>' +
						'</div>' +
					'</td>';
					tbody.appendChild(tr);
				});
			}

			window.filterBrandsList = function () {
				var input = document.getElementById('brand-filter-search');
				if (!input) return;
				var query = input.value.trim().toLowerCase();
				if (!query) {
					renderBrandsTable(brandsData);
					return;
				}
				var filtered = brandsData.filter(function (b) {
					return (b.name && b.name.toLowerCase().indexOf(query) !== -1) ||
						   (b.slug && b.slug.toLowerCase().indexOf(query) !== -1);
				});
				renderBrandsTable(filtered);
			};

			window.handleAddNewBrand = function () {
				var input = document.getElementById('brand-new-name-input');
				var btn = document.getElementById('brand-new-submit-btn');
				var feedback = document.getElementById('brand-add-feedback');
				if (!input) return;

				var name = input.value.trim();
				if (!name) {
					if (feedback) {
						feedback.className = 'mt-2 text-xs font-bold text-rose-600';
						feedback.textContent = 'يرجى إدخال اسم العلامة التجارية أولاً.';
					}
					input.focus();
					return;
				}

				if (btn) btn.disabled = true;
				if (feedback) {
					feedback.className = 'mt-2 text-xs font-bold text-slate-500';
					feedback.textContent = 'جارٍ إضافة العلامة التجارية...';
				}

				var formData = new FormData();
				formData.append('action', 'ashrafco_add_brand');
				formData.append('security', getNonce());
				formData.append('brand_name', name);

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (btn) btn.disabled = false;
					if (res.success) {
						input.value = '';
						if (feedback) {
							feedback.className = 'mt-2 text-xs font-bold text-emerald-600';
							feedback.textContent = 'تمت إضافة علامة (' + (res.data.name || name) + ') بنجاح!';
							setTimeout(function () { feedback.className = 'hidden mt-2 text-xs font-medium'; }, 3000);
						}
						if (res.data && res.data.brands) {
							brandsData = res.data.brands;
							window.ASHRAFCO_PORTAL_BRANDS = brandsData;
							updateHeaderBadge(brandsData.length);
							renderBrandsTable(brandsData);
						} else {
							fetchBrands();
						}
					} else {
						if (feedback) {
							feedback.className = 'mt-2 text-xs font-bold text-rose-600';
							feedback.textContent = res.data && res.data.message ? res.data.message : 'حدث خطأ أثناء الإضافة.';
						}
					}
				})
				.catch(function (err) {
					if (btn) btn.disabled = false;
					if (feedback) {
						feedback.className = 'mt-2 text-xs font-bold text-rose-600';
						feedback.textContent = 'فشل الاتصال بالخادم. يرجى إعادة المحاولة.';
					}
				});
			};

			window.openBrandProductsManager = function (brandId, brandName) {
				activeBrandId = brandId;
				activeBrandName = brandName;

				var viewList = document.getElementById('brands-view-list');
				var viewProducts = document.getElementById('brands-view-products');
				if (viewList) viewList.classList.add('hidden');
				if (viewProducts) viewProducts.classList.remove('hidden');

				var titleEl = document.getElementById('active-brand-name-display');
				if (titleEl) titleEl.textContent = brandName;

				var searchInput = document.getElementById('brand-product-search-input');
				if (searchInput) searchInput.value = '';
				var searchResults = document.getElementById('brand-product-search-results');
				if (searchResults) { searchResults.innerHTML = ''; searchResults.classList.add('hidden'); }

				loadBrandProducts(brandId);
			};

			window.backToBrandsList = function () {
				var viewList = document.getElementById('brands-view-list');
				var viewProducts = document.getElementById('brands-view-products');
				if (viewList) viewList.classList.remove('hidden');
				if (viewProducts) viewProducts.classList.add('hidden');
				activeBrandId = null;
				activeBrandName = '';
				renderBrandsTable(brandsData);
			};

			function loadBrandProducts(brandId) {
				var container = document.getElementById('assigned-products-container');
				var empty = document.getElementById('assigned-products-empty');
				var badge = document.getElementById('active-brand-count-badge');
				var counter = document.getElementById('assigned-products-counter');
				if (!container) return;

				container.innerHTML = '<div class="p-6 text-center text-xs text-slate-400">جارٍ تحميل منتجات الماركة...</div>';
				if (empty) empty.classList.add('hidden');

				var formData = new FormData();
				formData.append('action', 'ashrafco_get_brand_products');
				formData.append('security', getNonce());
				formData.append('brand_id', brandId);

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (res.success && res.data) {
						var products = res.data.products || [];
						var count = res.data.count || 0;
						if (badge) badge.textContent = count;
						if (counter) counter.textContent = count;

						var b = brandsData.find(function (item) { return item.id === brandId; });
						if (b) b.count = count;

						renderAssignedProducts(products);
					}
				})
				.catch(function (err) {
					container.innerHTML = '<div class="p-6 text-center text-xs text-rose-500">حدث خطأ أثناء تحميل المنتجات.</div>';
				});
			}

			function renderAssignedProducts(products) {
				var container = document.getElementById('assigned-products-container');
				var empty = document.getElementById('assigned-products-empty');
				if (!container) return;

				container.innerHTML = '';
				if (!products || products.length === 0) {
					if (empty) empty.classList.remove('hidden');
					return;
				}
				if (empty) empty.classList.add('hidden');

				products.forEach(function (product) {
					var row = document.createElement('div');
					row.className = 'p-3 sm:p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors';

					var thumb = product.thumb_url ? '<img src="' + product.thumb_url + '" class="w-10 h-10 object-cover rounded-xl border border-slate-200" alt="">' : '<div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg></div>';

					row.innerHTML = '<div class="flex items-center gap-3 min-w-0">' +
						thumb +
						'<div class="min-w-0">' +
							'<span class="block text-xs font-bold text-slate-900 truncate">' + escapeHtml(product.name) + '</span>' +
							'<div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400">' +
								'<span>SKU: <strong class="text-slate-600 font-mono">' + escapeHtml(product.sku) + '</strong></span>' +
								(product.price ? '<span>•</span><span>' + product.price + '</span>' : '') +
							'</div>' +
						'</div>' +
					'</div>' +
					'<button type="button" onclick="handleUnassignProductFromBrand(' + product.id + ')" ' +
						'class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[11px] font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-100 transition-colors cursor-pointer flex-shrink-0">' +
						'<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
							'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />' +
						'</svg>' +
						'<span>إلغاء التعيين</span>' +
					'</button>';
					container.appendChild(row);
				});
			}

			window.debounceBrandProductSearch = function () {
				if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
				searchDebounceTimer = setTimeout(function () {
					performBrandProductSearch();
				}, 300);
			};

			function performBrandProductSearch() {
				var input = document.getElementById('brand-product-search-input');
				var resultsBox = document.getElementById('brand-product-search-results');
				if (!input || !resultsBox || !activeBrandId) return;

				var query = input.value.trim();
				if (query.length < 2) {
					resultsBox.innerHTML = '';
					resultsBox.classList.add('hidden');
					return;
				}

				resultsBox.innerHTML = '<div class="p-3 text-center text-xs text-slate-400">جارٍ البحث في المنتجات...</div>';
				resultsBox.classList.remove('hidden');

				var formData = new FormData();
				formData.append('action', 'ashrafco_search_products_for_brand');
				formData.append('security', getNonce());
				formData.append('brand_id', activeBrandId);
				formData.append('search', query);

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (res.success && res.data && res.data.results) {
						renderSearchResults(res.data.results);
					} else {
						resultsBox.innerHTML = '<div class="p-3 text-center text-xs text-slate-400">لا توجد نتائج مطابقة.</div>';
					}
				})
				.catch(function (err) {
					resultsBox.innerHTML = '<div class="p-3 text-center text-xs text-rose-500">حدث خطأ أثناء البحث.</div>';
				});
			}

			function renderSearchResults(results) {
				var resultsBox = document.getElementById('brand-product-search-results');
				if (!resultsBox) return;

				resultsBox.innerHTML = '';
				if (results.length === 0) {
					resultsBox.innerHTML = '<div class="p-3 text-center text-xs text-slate-400">لم يتم العثور على أي منتجات مطابقة.</div>';
					return;
				}

				results.forEach(function (prod) {
					var item = document.createElement('div');
					item.className = 'p-2.5 flex items-center justify-between gap-2.5 hover:bg-slate-50 transition-colors';

					var thumb = prod.thumb_url ? '<img src="' + prod.thumb_url + '" class="w-8 h-8 object-cover rounded-lg border border-slate-200" alt="">' : '<div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 text-[10px]">P</div>';

					var actionBtn = prod.is_assigned
						? '<span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 flex-shrink-0">✓ مرتبطة بهذه الماركة</span>'
						: '<button type="button" onclick="handleAssignProductToBrand(' + prod.id + ')" class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-bold text-primary bg-primary/10 hover:bg-primary hover:text-white transition-colors cursor-pointer flex-shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg><span>تعيين للماركة</span></button>';

					item.innerHTML = '<div class="flex items-center gap-2.5 min-w-0">' +
						thumb +
						'<div class="min-w-0">' +
							'<span class="block text-xs font-bold text-slate-800 truncate">' + escapeHtml(prod.name) + '</span>' +
							'<span class="block text-[10px] text-slate-400 font-mono">SKU: ' + escapeHtml(prod.sku) + '</span>' +
						'</div>' +
					'</div>' +
					actionBtn;
					resultsBox.appendChild(item);
				});
			}

			window.handleAssignProductToBrand = function (productId) {
				if (!activeBrandId) return;

				var formData = new FormData();
				formData.append('action', 'ashrafco_assign_brand_products');
				formData.append('security', getNonce());
				formData.append('brand_id', activeBrandId);
				formData.append('assign_action', 'add');
				formData.append('product_ids[]', productId);

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (res.success) {
						loadBrandProducts(activeBrandId);
						performBrandProductSearch();
						if (res.data && res.data.brands) {
							brandsData = res.data.brands;
							window.ASHRAFCO_PORTAL_BRANDS = brandsData;
							updateHeaderBadge(brandsData.length);
						}
					}
				});
			};

			window.handleUnassignProductFromBrand = function (productId) {
				if (!activeBrandId) return;

				var formData = new FormData();
				formData.append('action', 'ashrafco_assign_brand_products');
				formData.append('security', getNonce());
				formData.append('brand_id', activeBrandId);
				formData.append('assign_action', 'remove');
				formData.append('product_ids[]', productId);

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (res.success) {
						loadBrandProducts(activeBrandId);
						var searchResults = document.getElementById('brand-product-search-results');
						if (searchResults && !searchResults.classList.contains('hidden')) {
							performBrandProductSearch();
						}
						if (res.data && res.data.brands) {
							brandsData = res.data.brands;
							window.ASHRAFCO_PORTAL_BRANDS = brandsData;
							updateHeaderBadge(brandsData.length);
						}
					}
				});
			};

			window.promptDeleteBrand = function (brandId, brandName, productsCount) {
				var modal = document.getElementById('brand-delete-confirm-modal');
				if (!modal) return;

				document.getElementById('delete-target-brand-id').value = brandId;
				document.getElementById('delete-target-brand-count').value = productsCount;
				document.getElementById('delete-brand-title').textContent = brandName;

				var feedback = document.getElementById('delete-brand-feedback');
				if (feedback) { feedback.className = 'hidden text-xs font-medium p-2.5 rounded-xl'; feedback.textContent = ''; }

				var btn = document.getElementById('confirm-delete-brand-btn');
				if (btn) btn.disabled = false;
				var btnText = document.getElementById('delete-btn-text');
				if (btnText) btnText.textContent = 'تأكيد الحذف';

				var withProds = document.getElementById('delete-mode-with-products');
				var zeroProds = document.getElementById('delete-mode-zero-products');

				if (productsCount > 0) {
					if (withProds) withProds.classList.remove('hidden');
					if (zeroProds) zeroProds.classList.add('hidden');
					var numEl = document.getElementById('delete-brand-products-count');
					if (numEl) numEl.textContent = productsCount;
					document.querySelectorAll('.delete-brand-num-placeholder').forEach(function (el) {
						el.textContent = productsCount;
					});
					var defaultRadio = document.querySelector('input[name="brand_delete_option"][value="unlink"]');
					if (defaultRadio) defaultRadio.checked = true;
				} else {
					if (withProds) withProds.classList.add('hidden');
					if (zeroProds) zeroProds.classList.remove('hidden');
				}

				modal.classList.remove('hidden');
			};

			window.closeDeleteBrandModal = function () {
				var modal = document.getElementById('brand-delete-confirm-modal');
				if (modal) modal.classList.add('hidden');
			};

			window.executeBrandDeletion = function () {
				var brandId = parseInt(document.getElementById('delete-target-brand-id').value, 10);
				var productsCount = parseInt(document.getElementById('delete-target-brand-count').value, 10);
				var btn = document.getElementById('confirm-delete-brand-btn');
				var btnText = document.getElementById('delete-btn-text');
				var feedback = document.getElementById('delete-brand-feedback');

				if (!brandId) return;

				var deleteProducts = false;
				if (productsCount > 0) {
					var selectedOption = document.querySelector('input[name="brand_delete_option"]:checked');
					deleteProducts = selectedOption && selectedOption.value === 'delete_all';
				}

				if (btn) btn.disabled = true;
				if (btnText) btnText.textContent = 'جارٍ الحذف...';
				if (feedback) {
					feedback.className = 'text-xs font-bold text-slate-500 bg-slate-100 p-2.5 rounded-xl';
					feedback.textContent = deleteProducts ? 'جارٍ حذف الماركة وجميع منتجاتها نهائياً...' : 'جارٍ حذف الماركة وفك ارتباط منتجاتها...';
					feedback.classList.remove('hidden');
				}

				var formData = new FormData();
				formData.append('action', 'ashrafco_delete_brand');
				formData.append('security', getNonce());
				formData.append('brand_id', brandId);
				formData.append('delete_products', deleteProducts ? '1' : '0');

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData,
				})
				.then(function (res) { return res.json(); })
				.then(function (res) {
					if (res.success) {
						if (feedback) {
							feedback.className = 'text-xs font-bold text-emerald-700 bg-emerald-50 p-2.5 rounded-xl border border-emerald-100';
							feedback.textContent = res.data.message || 'تم حذف الماركة بنجاح.';
						}
						setTimeout(function () {
							closeDeleteBrandModal();
							if (res.data && res.data.brands) {
								brandsData = res.data.brands;
								window.ASHRAFCO_PORTAL_BRANDS = brandsData;
								updateHeaderBadge(brandsData.length);
								renderBrandsTable(brandsData);
							} else {
								fetchBrands();
							}
						}, 800);
					} else {
						if (btn) btn.disabled = false;
						if (btnText) btnText.textContent = 'تأكيد الحذف';
						if (feedback) {
							feedback.className = 'text-xs font-bold text-rose-600 bg-rose-50 p-2.5 rounded-xl border border-rose-100';
							feedback.textContent = res.data && res.data.message ? res.data.message : 'فشل الحذف.';
						}
					}
				})
				.catch(function (err) {
					if (btn) btn.disabled = false;
					if (btnText) btnText.textContent = 'تأكيد الحذف';
					if (feedback) {
						feedback.className = 'text-xs font-bold text-rose-600 bg-rose-50 p-2.5 rounded-xl border border-rose-100';
						feedback.textContent = 'حدث خطأ في الاتصال بالخادم.';
					}
				});
			};

			function escapeHtml(str) {
				if (!str) return '';
				return String(str)
					.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');
			}

			function escapeJs(str) {
				if (!str) return '';
				return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
			}
		})();

});