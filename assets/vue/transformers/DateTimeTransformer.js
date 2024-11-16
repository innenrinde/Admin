import moment from "moment";

export default class DateTimeTransformer {

    /**
     * Datetime ws format to a readable value
     * @param {date} value 
     * @returns {string}
     */
    static transform(value) {
        return value ? moment(value).format("DD/MM/YYYY HH:mm:ss") : "-";
    }

    /**
     * Datetime to ws allowed format
     * @param {date} value 
     * @returns {string}
     */
    static reverseTransform(value) {
        return value ? moment(value).format("YYYY-MM-DD HH:mm:ss") : "";
    }

}