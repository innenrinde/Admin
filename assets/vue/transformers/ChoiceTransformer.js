
export default class ChoiceTransformer {

    /**
     * Choice ws format to a readable value
     * @param {object} data
     * @returns {object}
     */
    static transform(data) {
        return data;
    }

    /**
     * Choice selection to ws allowed format
     * @param {object} data
     * @returns {object}
     */
    static reverseTransform(data) {
        return data ? data.value : null;
    }

}