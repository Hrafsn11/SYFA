import "./bootstrap";
import Alpine from "alpinejs";

// Prevent initializing Alpine if another copy (vendor/template) already added it
if (!window.Alpine) {
	window.Alpine = Alpine;
	Alpine.start();
} else {
	// If Alpine exists but not yet started, try to start it safely
	try {
		if (typeof window.Alpine.start === 'function' && !window.Alpine._initialized) {
			window.Alpine.start();
		}
	} catch (e) {
		// ignore
	}
}