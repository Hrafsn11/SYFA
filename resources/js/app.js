import "./bootstrap";
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import flatpickr from "flatpickr";
import '../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables-all.js';

// Prevent initializing Alpine if another copy (vendor/template) already added it
// if (!window.Alpine) {
// 	window.Alpine = Alpine;
// 	Alpine.start();
// } else {
// 	// If Alpine exists but not yet started, try to start it safely
// 	try {
// 		if (typeof window.Alpine.start === 'function' && !window.Alpine._initialized) {
// 			window.Alpine.start();
// 		}
// 	} catch (e) {
// 		// ignore
// 	}
// }

Livewire.start();
// Initialize flatpickr for datepicker

const menuTextSelector = '.layout-menu .menu-link > div[data-i18n]';

function updateMenuMarqueeOverflow() {
	document.querySelectorAll('.menu-text-marquee').forEach((element) => {
		const span = element.querySelector('span');
		if (!span || element.clientWidth === 0) {
			return;
		}

		const isOverflow = span.scrollWidth > element.clientWidth;
		element.classList.toggle('is-overflow', isOverflow);
		if (isOverflow) {
			const duration = Math.max(6, Math.round(span.scrollWidth / 40));
			element.style.setProperty('--marquee-duration', `${duration}s`);
		} else {
			element.style.removeProperty('--marquee-duration');
		}
	});
}

function setupMenuMarquee() {
	document.querySelectorAll(menuTextSelector).forEach((element) => {
		if (element.classList.contains('menu-text-marquee')) {
			return;
		}

		const text = element.textContent.trim();
		element.textContent = '';
		element.classList.add('menu-text-marquee');

		const span = document.createElement('span');
		span.textContent = text;
		element.appendChild(span);
	});

	window.requestAnimationFrame(updateMenuMarqueeOverflow);
}

document.addEventListener('DOMContentLoaded', setupMenuMarquee);
document.addEventListener('livewire:navigated', setupMenuMarquee);
window.addEventListener('resize', updateMenuMarqueeOverflow);

