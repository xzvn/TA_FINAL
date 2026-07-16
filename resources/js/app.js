

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


function applyApplicationTheme(theme) {
    const isDark = theme === 'dark';

    document.body.classList.toggle(
        'theme-dark',
        isDark
    );

    document.body.classList.toggle(
        'theme-light',
        !isDark
    );

    document.documentElement.classList.toggle(
        'dark',
        isDark
    );

    document.documentElement.style.colorScheme =
        isDark ? 'dark' : 'light';
}

document.addEventListener(
    'DOMContentLoaded',
    function () {
        const selectedTheme =
            document.querySelector(
                'input[name="theme"]:checked'
            );

        if (selectedTheme) {
            applyApplicationTheme(
                selectedTheme.value
            );
        }
    }
);

document.addEventListener(
    'change',
    function (event) {
        const target = event.target;

        if (
            target instanceof HTMLInputElement &&
            target.name === 'theme' &&
            target.checked
        ) {
            applyApplicationTheme(
                target.value
            );
        }
    }
);