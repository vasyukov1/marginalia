import './bootstrap';

import Alpine from 'alpinejs';
import readingPicker from './reading-picker';

window.Alpine = Alpine;

Alpine.data('readingPicker', readingPicker);

Alpine.start();
