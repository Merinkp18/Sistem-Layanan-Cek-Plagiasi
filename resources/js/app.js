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

document.querySelectorAll('[data-submission-wizard]').forEach((form) => {
	const panels = [...form.querySelectorAll('[data-wizard-panel]')];
	const indicators = [...document.querySelectorAll('[data-step-indicator]')];
	const purposeSelect = form.querySelector('[data-purpose-select]');
	const skInput = form.querySelector('[data-sk-input]');
	let currentStep = Number(form.dataset.initialStep || 1);

	const showStep = (step) => {
		currentStep = step;
		panels.forEach((panel) => {
			const isActive = Number(panel.dataset.wizardPanel) === step;
			panel.hidden = !isActive;
			panel.classList.toggle('is-active', isActive);
		});
		indicators.forEach((indicator) => {
			const indicatorStep = Number(indicator.dataset.stepIndicator);
			indicator.classList.toggle('is-current', indicatorStep === step);
			indicator.classList.toggle('is-done', indicatorStep < step);
		});
	};

	const syncSkRequirement = () => {
		if (!purposeSelect || !skInput) return;

		const isThesis = purposeSelect.value === 'Skripsi';
		skInput.required = isThesis;
	};

	form.querySelectorAll('[data-wizard-next]').forEach((button) => {
		button.addEventListener('click', () => {
			const currentPanel = panels.find((panel) => Number(panel.dataset.wizardPanel) === currentStep);
			const fields = [...currentPanel.querySelectorAll('input, select, textarea')];
			const invalidField = fields.find((field) => !field.checkValidity());

			if (invalidField) {
				invalidField.reportValidity();
				return;
			}

			showStep(Math.min(currentStep + 1, panels.length));
		});
	});

	form.querySelectorAll('[data-wizard-back]').forEach((button) => {
		button.addEventListener('click', () => showStep(Math.max(currentStep - 1, 1)));
	});

	purposeSelect?.addEventListener('change', syncSkRequirement);
	syncSkRequirement();

	showStep(currentStep);
});

document.querySelectorAll('[data-file-dropzone]').forEach((dropzone) => {
	const input = dropzone.querySelector('input[type="file"]');
	const fileName = dropzone.querySelector('[data-selected-file]');

	if (!input || !fileName) return;

	const showSelectedFile = () => {
		fileName.textContent = input.files[0]?.name || 'Belum ada file dipilih';
		dropzone.classList.toggle('has-file', input.files.length > 0);
	};

	input.addEventListener('change', showSelectedFile);
	['dragenter', 'dragover'].forEach((eventName) => {
		dropzone.addEventListener(eventName, (event) => {
			event.preventDefault();
			dropzone.classList.add('is-dragging');
		});
	});

	['dragleave', 'drop'].forEach((eventName) => {
		dropzone.addEventListener(eventName, (event) => {
			event.preventDefault();
			dropzone.classList.remove('is-dragging');
		});
	});

	dropzone.addEventListener('drop', (event) => {
		if (!event.dataTransfer?.files.length) return;

		input.files = event.dataTransfer.files;
		input.dispatchEvent(new Event('change', { bubbles: true }));
	});
});
