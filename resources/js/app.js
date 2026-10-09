import './bootstrap';

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
	const input = document.getElementById(toggle.dataset.passwordToggle);

	if (!input) return;

	toggle.addEventListener('click', () => {
		const isVisible = input.type === 'text';

		input.type = isVisible ? 'password' : 'text';
		toggle.setAttribute('aria-pressed', String(!isVisible));
		toggle.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
		toggle.setAttribute('title', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
	});
});
