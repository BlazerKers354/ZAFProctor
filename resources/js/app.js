import '../css/app.css';
import './bootstrap';

const initRevealAnimations = () => {
	const revealElements = document.querySelectorAll('.zaf-reveal');

	if (revealElements.length === 0) {
		return;
	}

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		revealElements.forEach((element) => element.classList.add('is-visible'));
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				entry.target.classList.add('is-visible');
				observer.unobserve(entry.target);
			});
		},
		{
			threshold: 0.16,
			rootMargin: '0px 0px -24px 0px',
		}
	);

	revealElements.forEach((element) => observer.observe(element));
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initRevealAnimations, { once: true });
} else {
	initRevealAnimations();
}
