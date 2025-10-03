import moment from "moment";

export default class DateTransformer {

    /**
     * Datetime ws format to a readable value
     * @param {date} value 
     * @returns {string}
     */
    static transform(value) {
        console.log(value);
        return value ? moment(value).format("DD/MM/YYYY") : "-";
    }

    /**
     * Datetime to ws allowed format
     * @param {date} value 
     * @returns {string}
     */
    static reverseTransform(value) {
        return value ? moment(value).format("YYYY-MM-DD") : "";
    }

}