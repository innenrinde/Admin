import { NotificationService } from "./NotificationService";

class HttpRequestService {

    /**
     * Process a http ajax response
     * @param {Object} data response
     * @param {Function} callbackSuccess run if success
     * @param {Function} callbackError run if error
     */
    parseResponse(data, callbackSuccess = null, callbackError = null) {

        let isSuccess = data?.data?.success;
        let message = data?.data?.message;

        if (isSuccess === undefined) {
            throw new Error("HttpRequestService .data.success is empty");
        }

        if (message === undefined) {
            throw new Error("HttpRequestService .data.message is empty");
        }

        if (isSuccess) {
            this.okMessage(message);
            typeof callbackSuccess === "function" ? callbackSuccess() : "";
        } else {
            this.errorMessage(message);
            typeof callbackError === "function" ? callbackError() : "";
        }
    }

    /**
     * Display a success message
     * @param {String} message
     */
    okMessage(message) {
        NotificationService.message({ title: "Success", message, type: "success" });
    }

    /**
     * Display an error message
     * @param {String} message
     */
    errorMessage(message) {
        NotificationService.message({ title: "Error", message, type: "error" });
    }
}

let instance = new HttpRequestService();
Object.freeze(instance);

export { instance as HttpRequestService };