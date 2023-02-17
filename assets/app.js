/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'highlight.js/styles/stackoverflow-light.css';
import './styles/app.css';

require('./bootstrap');

import { __, _n, sprintf } from '@wordpress/i18n';
import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import router from './router.js';

import hljs from 'highlight.js/lib/core';
import hljsPhpLang from 'highlight.js/lib/languages/php';
import hljsVuePlugin from '@highlightjs/vue-plugin';
import vRipple from './directives/ripple/ripple.js';

hljs.registerLanguage('php', hljsPhpLang);

const pinia = createPinia();
const app = createApp(App);

app.config.globalProperties.__ = __;
app.config.globalProperties._n = _n;
app.config.globalProperties.sprintf = sprintf;
app.config.globalProperties.acssPurger = window.acssPurger;

app
    .use(pinia)
    .use(router)
    .use(hljsVuePlugin);

app.directive('ripple', vRipple);

app.mount('#acss-purger-app');