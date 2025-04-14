import renderComponent from "../lib/renderComponent";
import XMessage from "../controllers/components/XMessage.vue";

class NotificationService {

    #messagesContainer = null;

    /**
     * Open a simple notification
     * @param {String} title
     * @param {String} message
     * @param {String} type 'error' or 'success'
     */
    message({ title, message, type }) {
        if (!this.#messagesContainer) {
            this.#messagesContainer = document.createElement("div");
            this.#messagesContainer.classList.add("messages-list");
            this.#messagesContainer.id = String(Math.random());
            document.body.appendChild(this.#messagesContainer);
        }

        let container = document.createElement("div");
        container.id = String(Math.random());
        this.#messagesContainer.appendChild(container);

        let renderer = renderComponent({
            el: container,
            component: XMessage,
            props: {
                title,
                message,
                type,
                onClose: () => {
                    renderer();
                    this.#messagesContainer.removeChild(container);
                }
            },
            appContext: null
        });
    }

}

let instance = new NotificationService();
Object.freeze(instance);

export { instance as NotificationService };