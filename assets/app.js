import { registerVueControllerComponents } from '@symfony/ux-vue';
import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";
import moment from "moment";
import Form from "./vue/controllers/Form.vue";

/* import the fontawesome core */
import { library } from "@fortawesome/fontawesome-svg-core";

/* import font awesome icon component */
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

/* import specific icons */
import {
    faRepeat,
    faXmark,
    faChevronUp,
    faChevronDown,
    faMagnifyingGlass,
    faChevronLeft,
    faChevronRight,
    faCircleExclamation,
    faCircleCheck,
    faList,
    faPlus,
    faUser,
    faUsers,
    faChartColumn,
    faPowerOff,
    faForward,
    faForwardStep,
    faBackward,
    faBackwardStep,
    faChartSimple,
    faArrowUpRightFromSquare,
} from '@fortawesome/free-solid-svg-icons';
import { faTrashCan, faPenToSquare, faImage } from '@fortawesome/free-regular-svg-icons';
import { NotificationService } from "./vue/services/NotificationService";

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

// assets/app.js
document.addEventListener('vue:before-mount', (event) => {
    const {
        componentName, // The Vue component's name
        component, // The resolved Vue component
        props, // The props that will be injected to the component
        app, // The Vue application instance
    } = event.detail;

    app.component("Form", Form);

    app.directive("focus", {
        // When the bound element is mounted into the DOM...
        mounted(el, binding) {
            if (binding.value) {
                el.focus();
            }
        }
    });

    /* add font awesome icons to the library */
    library.add(faTrashCan);
    library.add(faRepeat);
    library.add(faXmark);
    library.add(faChevronDown);
    library.add(faChevronUp);
    library.add(faChevronLeft);
    library.add(faChevronRight);
    library.add(faPenToSquare);
    library.add(faCircleCheck);
    library.add(faList);
    library.add(faPlus);
    library.add(faUser);
    library.add(faUsers);
    library.add(faChartColumn);
    library.add(faPowerOff);
    library.add(faMagnifyingGlass);
    library.add(faCircleExclamation);
    library.add(faForward);
    library.add(faForwardStep);
    library.add(faBackward);
    library.add(faBackwardStep);
    library.add(faImage);
    library.add(faChartSimple);
    library.add(faArrowUpRightFromSquare);

    app.component('font-awesome-icon', FontAwesomeIcon);

    app.config.globalProperties.$moment = moment;

    NotificationService.setAppContext(app._context);
});