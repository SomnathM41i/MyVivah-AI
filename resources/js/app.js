import './bootstrap';

/**
 * MyVivahAI public site + auth shell behaviors.
 * Kept dependency-free (no Alpine/Livewire): tiny, progressive enhancements only.
 */
document.addEventListener('DOMContentLoaded', () => {
    // --- Mobile navigation toggles (data attribute driven) ---
    document.querySelectorAll('[data-mobile-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const target = document.getElementById(toggle.dataset.mobileToggle);
            if (!target) {
                return;
            }

            const hidden = target.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', String(!hidden));
            toggle.dataset.state = hidden ? 'closed' : 'open';
        });
    });

    // --- Forms: prevent double-submit by disabling the submit while pending ---
    document.querySelectorAll('form[data-submit-guard]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                const original = button.innerHTML;
                button.disabled = true;
                button.dataset.originalLabel = original;
                button.innerHTML =
                    '<span class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>Please wait…</span>';
            }
        });
    });

    // --- Auto-dismiss flash alerts ---
    document.querySelectorAll('[data-autohide]').forEach((alert) => {
        const ms = Number(alert.dataset.autohide || 6000);
        window.setTimeout(() => {
            alert.classList.add('opacity-0', 'translate-y-[-4px]');
            window.setTimeout(() => alert.remove(), 300);
        }, ms);
    });

    // --- Closeable alerts ---
    document.querySelectorAll('[data-dismiss]').forEach((dismiss) => {
        dismiss.addEventListener('click', () => {
            dismiss.closest('[data-alert]')?.remove();
        });
    });

    // --- Animate "fade-up" sections once visible ---
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-y-3');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.08 },
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el));
});