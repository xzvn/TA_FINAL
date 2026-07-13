@props([
'seconds' => 15,
])

<div data-auto-refresh data-seconds="{{ (int) $seconds }}"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const autoRefreshElement = document.querySelector('[data-auto-refresh]');

        if (!autoRefreshElement) {
            return;
        }

        const seconds = Number(autoRefreshElement.dataset.seconds || 15);

        if (!seconds || seconds < 5) {
            return;
        }

        function userSedangMengetik() {
            const active = document.activeElement;

            if (!active) {
                return false;
            }

            return ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName);
        }

        setInterval(function() {
            if (document.hidden) {
                return;
            }

            if (userSedangMengetik()) {
                return;
            }

            window.location.reload();
        }, seconds * 1000);
    });
</script>