import './bootstrap';
import Alpine from 'alpinejs';
import appState from './alpine/app-state';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('appState', appState);
});

Alpine.start();