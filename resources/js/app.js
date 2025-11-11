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

