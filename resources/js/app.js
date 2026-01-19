import './bootstrap';
// Vite handles the worker URL import
import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf.js';
import pdfWorker from 'pdfjs-dist/legacy/build/pdf.worker.js?url';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.pdfjsLib = pdfjsLib;
window.pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;

// Alpine.start();
