import renderComponent from "../lib/renderComponent";
import XMessage from "../controllers/components/XMessage.vue";
import XConfirm from "../controllers/components/XConfirm.vue";

class NotificationService {

    #appContext = null;

    #messagesContainer = null;

    setAppContext(value) {
        this.#appContext = value;
    }

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
            appContext: this.#appContext
        });
    }

    /**
     * Crate a confirmation dialogue
     * @param {string} title
     * @param {string} message
     * @param {callback} okAction
     * @param {callback} closeAction
     */
    confirm({ title, message, okAction, closeAction }) {
        let container = document.createElement("div");
        container.id = String(Math.random());
        document.body.appendChild(container);

        let renderer = renderComponent({
            el: container,
            component: XConfirm,
            props: {
                title,
                message,
                onOk: () => {
                    close();
                    okAction ? okAction() : "";
                },
                onClose: () => {
                    close();
                    closeAction ? closeAction() : "";
                }
            },
            appContext: null
        });

        const close = () => {
            renderer();
            document.body.removeChild(container);
        }
    }

}

let instance = new NotificationService();
Object.freeze(instance);

export { instance as NotificationService };