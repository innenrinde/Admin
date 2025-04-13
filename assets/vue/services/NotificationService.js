import renderComponent from "../lib/renderComponent";
import XMessage from "../controllers/components/XMessage.vue";

class NotificationService {

    /**
     * Open a simple notification
     * @param {String} title
     * @param {String} message
     * @param {String} type 'error' or 'success'
     */
    message({ title, message, type }) {
        const container = document.createElement("div");
        container.id = String(Math.random());
        document.body.appendChild(container);
        let renderer = renderComponent({
            el: container,
            component: XMessage,
            props: {
                title,
                message,
                type,
                onClose: () => {
                    renderer();
                    document.body.removeChild(container);
                }
            },
            appContext: null
        });
    }

}

let instance = new NotificationService();
Object.freeze(instance);

export { instance as NotificationService };