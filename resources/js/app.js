const linksList = document.querySelector('#links-list');

if (linksList) {
    const status = document.querySelector('#reorder-status');
    const error = document.querySelector('#reorder-error');

    const updateMoveButtons = () => {
        const items = [...linksList.querySelectorAll('[data-link-item]')];

        items.forEach((item, index) => {
            item.querySelector('[data-move-up]')?.toggleAttribute('disabled', index === 0);
            item.querySelector('[data-move-down]')?.toggleAttribute('disabled', index === items.length - 1);
        });
    };

    document.querySelectorAll('[data-reorder-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('button');
            const item = form.closest('[data-link-item]');
            const direction = form.dataset.direction;

            if (!button || !item || !direction) {
                return;
            }

            button.disabled = true;
            linksList.setAttribute('aria-busy', 'true');
            error.hidden = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: new FormData(form),
                });

                if (!response.ok) {
                    throw new Error('Não foi possível reordenar os links.');
                }

                const result = await response.json();

                if (result.moved) {
                    if (direction === 'up') {
                        linksList.insertBefore(item, item.previousElementSibling);
                    } else {
                        linksList.insertBefore(item.nextElementSibling, item);
                    }

                    status.textContent = 'Ordem dos links atualizada.';
                }

                updateMoveButtons();
            } catch (exception) {
                error.textContent = 'Não foi possível atualizar a ordem. Tente novamente.';
                error.hidden = false;
                status.textContent = 'Não foi possível atualizar a ordem dos links.';
                updateMoveButtons();
            } finally {
                linksList.removeAttribute('aria-busy');
            }
        });
    });
}

const addLinkModal = document.querySelector('[data-modal]');

if (addLinkModal) {
    const modalOpener = document.querySelector('[data-modal-open]');
    const modalDialog = addLinkModal.querySelector('[data-modal-dialog]');
    const firstInput = addLinkModal.querySelector('[name="title"]');
    const imageInput = addLinkModal.querySelector('[data-image-input]');
    const imagePreview = addLinkModal.querySelector('#image-preview');
    const imagePreviewPlaceholder = addLinkModal.querySelector('#image-preview-placeholder');
    let lastFocusedElement = null;
    let previewUrl = null;

    const closeModal = () => {
        addLinkModal.hidden = true;
        addLinkModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        lastFocusedElement?.focus();
    };

    const openModal = () => {
        lastFocusedElement = document.activeElement;
        addLinkModal.hidden = false;
        addLinkModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        firstInput?.focus();
    };

    modalOpener?.addEventListener('click', openModal);

    addLinkModal.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    addLinkModal.addEventListener('click', (event) => {
        if (event.target === addLinkModal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (addLinkModal.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closeModal();

            return;
        }

        if (event.key !== 'Tab' || !modalDialog) {
            return;
        }

        const focusableElements = [...modalDialog.querySelectorAll(
            'button:not([disabled]), input:not([disabled]), a[href], select:not([disabled]), textarea:not([disabled])',
        )];
        const firstElement = focusableElements[0];
        const lastElement = focusableElements.at(-1);

        if (!firstElement || !lastElement) {
            return;
        }

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    });

    imageInput?.addEventListener('change', () => {
        const [image] = imageInput.files;

        if (!image || !imagePreview || !imagePreviewPlaceholder) {
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        previewUrl = URL.createObjectURL(image);
        imagePreview.src = previewUrl;
        imagePreview.hidden = false;
        imagePreviewPlaceholder.hidden = true;
    });

    if (window.openAddLinkModal) {
        openModal();
    }
}

const profileForm = document.querySelector('[data-profile-form]');

if (profileForm) {
    const status = profileForm.querySelector('[data-profile-status]');
    const generalError = profileForm.querySelector('[data-profile-error]');
    const imageInput = profileForm.querySelector('[data-profile-image-input]');
    const imagePreview = profileForm.querySelector('[data-profile-image-preview]');
    const imagePlaceholder = profileForm.querySelector('[data-profile-image-placeholder]');
    const submitButton = document.querySelector('[data-profile-submit]');
    let previewUrl = null;

    const clearFieldErrors = () => {
        profileForm.querySelectorAll('[data-profile-field-error]').forEach((error) => {
            error.textContent = '';
            error.hidden = true;
        });

        profileForm.querySelectorAll('[data-profile-field]').forEach((field) => {
            field.classList.remove('border-accent-red');
            field.removeAttribute('aria-invalid');
            field.removeAttribute('aria-describedby');
        });
    };

    const showFieldErrors = (errors) => {
        Object.entries(errors).forEach(([name, messages]) => {
            const field = profileForm.elements.namedItem(name);
            const error = profileForm.querySelector(`[data-profile-field-error="${name}"]`);

            if (!(field instanceof HTMLElement) || !error) {
                return;
            }

            const errorId = `${name}-profile-error`;
            field.classList.add('border-accent-red');
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', errorId);
            error.id = errorId;
            error.textContent = messages[0];
            error.hidden = false;
        });
    };

    const updateImagePreview = (url) => {
        if (!imagePreview || !imagePlaceholder) {
            return;
        }

        imagePreview.src = url;
        imagePreview.hidden = false;
        imagePlaceholder.hidden = true;
    };

    imageInput?.addEventListener('change', () => {
        const [image] = imageInput.files;

        if (!image) {
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        previewUrl = URL.createObjectURL(image);
        updateImagePreview(previewUrl);
    });

    profileForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFieldErrors();
        status.hidden = true;
        generalError.hidden = true;

        if (submitButton) {
            submitButton.disabled = true;
        }

        try {
            const response = await fetch(profileForm.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(profileForm),
            });
            const result = await response.json();

            if (response.status === 422) {
                showFieldErrors(result.errors ?? {});
                generalError.textContent = 'Revise os campos destacados e tente novamente.';
                generalError.hidden = false;

                return;
            }

            if (!response.ok) {
                throw new Error('Não foi possível atualizar o perfil.');
            }

            profileForm.elements.name.value = result.profile.name;
            profileForm.elements.email.value = result.profile.email;
            profileForm.elements.bio.value = result.profile.bio;

            if (result.profile.image_url) {
                updateImagePreview(result.profile.image_url);
            }

            status.textContent = result.message;
            status.hidden = false;
        } catch (exception) {
            generalError.textContent = 'Não foi possível atualizar o perfil. Tente novamente.';
            generalError.hidden = false;
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    });
}
