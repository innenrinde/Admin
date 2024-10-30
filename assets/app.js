import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import ElementPlus from 'element-plus';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import 'element-plus/dist/index.css';

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

// assets/app.js
document.addEventListener('vue:before-mount', (event) => {
    const {
        componentName, // The Vue component's name
        component, // The resolved Vue component
        props, // The props that will be injected to the component
        app, // The Vue application instance
    } = event.detail;

    app.use(ElementPlus);

    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
});