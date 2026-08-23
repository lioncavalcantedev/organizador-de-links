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
