(function () {
    const forms = Array.from(document.querySelectorAll('[data-lead-form]'));
    const serviceSelects = Array.from(document.querySelectorAll('[data-service-select]'));
    const serviceButtons = document.querySelectorAll('[data-service-name]');
    const desktopMotion = window.matchMedia('(min-width: 761px)').matches &&
        !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (desktopMotion && 'IntersectionObserver' in window) {
        const revealItems = document.querySelectorAll([
            '.hero-copy',
            '.hero-lead-card',
            '.scenario-card',
            '.price-group',
            '.inspection-list article',
            '.process-list li',
            '.photo-card',
            '.review-card',
            '.faq-list details',
            '.lead-copy',
            '.lead-form',
            '.contacts-grid > *',
        ].join(','));

        revealItems.forEach((item) => item.classList.add('is-reveal'));

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -40px 0px' });

        revealItems.forEach((item) => observer.observe(item));
    }

    if (!desktopMotion) {
        const mobileCta = document.querySelector('.mobile-cta');
        if (mobileCta) {
            const toggleMobileCta = () => {
                mobileCta.classList.toggle('is-visible', window.scrollY > 520);
            };
            toggleMobileCta();
            window.addEventListener('scroll', toggleMobileCta, { passive: true });
        }
    }

    function setStatus(form, message, type) {
        const statusNode = form.querySelector('[data-form-status]');
        if (!statusNode) {
            return;
        }

        statusNode.textContent = message;
        statusNode.classList.remove('is-success', 'is-error');
        if (type) {
            statusNode.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    serviceButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const serviceName = button.getAttribute('data-service-name') || '';

            if (serviceName) {
                serviceSelects.forEach((select) => {
                    const option = Array.from(select.options).find((item) => item.value === serviceName);
                    select.value = option ? serviceName : 'Другое';
                });
            }

            const target = document.getElementById('lead');
            if (target) {
                target.scrollIntoView({ behavior: desktopMotion ? 'smooth' : 'auto', block: 'start' });
            }

            const mainForm = document.querySelector('.lead-form');
            const phone = mainForm ? mainForm.querySelector('input[name="phone"]') : null;
            if (phone) {
                window.setTimeout(() => phone.focus(), desktopMotion ? 420 : 0);
            }
        });
    });

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submit = form.querySelector('button[type="submit"]');
            const phone = form.querySelector('input[name="phone"]');
            const privacy = form.querySelector('input[name="privacy"]');
            const initialText = submit ? submit.textContent : '';

            if (phone && phone.value.replace(/\D/g, '').length < 10) {
                setStatus(form, 'Укажите телефон, чтобы мастер мог связаться с вами.', 'error');
                phone.focus();
                return;
            }

            if (privacy && privacy.type !== 'hidden' && !privacy.checked) {
                setStatus(form, 'Подтвердите согласие на обработку данных для обратной связи.', 'error');
                privacy.focus();
                return;
            }

            if (submit) {
                submit.disabled = true;
                submit.textContent = 'Отправляем...';
            }
            setStatus(form, 'Отправляем заявку...', '');

            if (document.body.dataset.staticPages === '1') {
                setStatus(form, 'На GitHub Pages форма работает как демонстрация без PHP. Для записи позвоните по номеру на сайте.', 'error');
                if (submit) {
                    submit.disabled = false;
                    submit.textContent = initialText;
                }
                return;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await response.json();

                if (!response.ok || !data.ok) {
                    throw new Error(data.message || 'Заявка не отправилась. Позвоните по телефону в шапке сайта.');
                }

                form.reset();
                setStatus(form, data.message || 'Заявка отправлена. Скоро с вами свяжутся.', 'success');
            } catch (error) {
                setStatus(form, error.message || 'Заявка не отправилась. Позвоните по телефону в шапке сайта.', 'error');
            } finally {
                if (submit) {
                    submit.disabled = false;
                    submit.textContent = initialText;
                }
            }
        });
    });
})();
