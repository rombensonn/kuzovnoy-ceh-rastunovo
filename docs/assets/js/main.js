(function () {
    const form = document.querySelector('[data-lead-form]');
    const serviceSelect = document.querySelector('[data-service-select]');
    const statusNode = document.querySelector('[data-form-status]');
    const serviceButtons = document.querySelectorAll('[data-service-name]');
    const desktopMotion = window.matchMedia('(min-width: 761px)').matches &&
        !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (desktopMotion && 'IntersectionObserver' in window) {
        const revealItems = document.querySelectorAll([
            '.hero-copy',
            '.hero-frame',
            '.hero-panel',
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

    function setStatus(message, type) {
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
            if (serviceSelect && serviceName) {
                const option = Array.from(serviceSelect.options).find((item) => item.value === serviceName);
                if (option) {
                    serviceSelect.value = serviceName;
                } else {
                    serviceSelect.value = 'Другое';
                }
            }

            const target = document.getElementById('lead');
            if (target) {
                target.scrollIntoView({ behavior: desktopMotion ? 'smooth' : 'auto', block: 'start' });
            }

            const phone = form ? form.querySelector('input[name="phone"]') : null;
            if (phone) {
                window.setTimeout(() => phone.focus(), desktopMotion ? 420 : 0);
            }
        });
    });

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submit = form.querySelector('button[type="submit"]');
        const phone = form.querySelector('input[name="phone"]');
        const privacy = form.querySelector('input[name="privacy"]');

        if (phone && phone.value.replace(/\D/g, '').length < 10) {
            setStatus('Укажите телефон, чтобы мастер мог связаться с вами.', 'error');
            phone.focus();
            return;
        }

        if (privacy && !privacy.checked) {
            setStatus('Подтвердите согласие на обработку данных для обратной связи.', 'error');
            privacy.focus();
            return;
        }

        if (submit) {
            submit.disabled = true;
            submit.textContent = 'Отправляем...';
        }
        setStatus('Отправляем заявку...', '');

        if (document.body.dataset.staticPages === '1') {
            setStatus('На GitHub Pages форма работает как демонстрация без PHP. Для записи позвоните по номеру на сайте.', 'error');
            if (submit) {
                submit.disabled = false;
                submit.textContent = 'Отправить заявку';
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
            setStatus(data.message || 'Заявка отправлена. Скоро с вами свяжутся.', 'success');
        } catch (error) {
            setStatus(error.message || 'Заявка не отправилась. Позвоните по телефону в шапке сайта.', 'error');
        } finally {
            if (submit) {
                submit.disabled = false;
                submit.textContent = 'Отправить заявку';
            }
        }
    });
})();
