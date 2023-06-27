window.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');

    const toggleButton = document.getElementById('toggle-button');
    const closeButton = document.getElementById('close-button');
    const settingsOverlay = document.getElementById('settings-overlay');

    toggleButton.addEventListener('click', function () {
        console.log('Toggle Button Clicked');
        if (settingsOverlay.style.width === '0%' || settingsOverlay.style.width === '') {
            settingsOverlay.style.visibility = 'visible';
            settingsOverlay.style.width = '100%';
        }
        else {
            settingsOverlay.style.width = '0%';
            setTimeout(() => {
                settingsOverlay.style.visibility = 'hidden';
            }, 600); // Timeout in ms should match the transition time in CSS
        }
    });

    closeButton.addEventListener('click', function () {
        console.log('Close Button Clicked');
        if (settingsOverlay.style.width === '100%') {
            settingsOverlay.style.width = '0%';
            setTimeout(() => {
                settingsOverlay.style.visibility = 'hidden';
            }, 600); // Timeout in ms should match the transition time in CSS
        }
    });
});
