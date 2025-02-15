/**
 * Search in a list of objects using a kNN logique
 */
export class kNNSearch {

    /**
     * List of words to be searched
     * @type {Array}
     */
    #vector = [];

    /**
     * Maximum number of neighbors to be displayed (first rows found)
     * @type {Integer}
     */
    #k = 0;

    /**
     * Input string for search
     * @param {String} query
     */
    setVector(query) {
        this.#vector = query ? query.trim().split(" ").map(word => word.toLowerCase()) : [];
    }

    /**
     * Set maximum number of neighbors
     * @param {*} value
     */
    setK(value) {
        this.#k = parseInt(value);
    }

    /**
     * Default metric between a word and a cell value
     * @param {String} word
     * @param {String} data
     * @returns {Number}
     */
    #metric(word, data) {
        let index = String(data).toLowerCase().indexOf(word);
        if (index > -1) {
            return 1 - 1/(1 + index);
        }

        return 1;
    }

    /**
     * Calculate distance between vector an a cell value
     * @param {*} value
     * @returns {Number}
     */
    #cellDistance(value) {
        let sum = 0;
        this.#vector.forEach((word) => {
            sum += this.#metric(word, value);
        });

        return sum;
    }

    /**
     * Calculate distance between vector and a row values
     * @param {Object} row
     * @param {Array} columns
     * @returns {Number}
     */
    #rowDistance(row, columns) {
        let sum = 0;
        columns.forEach((column) => {
            sum += this.#cellDistance(row[column.field]);
        });

        return sum;
    }

    /**
     * Perform search based on columns configurations
     * @param {Array} rows
     * @param {Array} columns
     * @returns {[string, unknown]|*}
     */
    applySearch(rows, columns) {

        // no need to perform a search for empty vector
        // by default all rows
        if (this.#vector.length === 0) {
            return rows;
        }

        // build table object with results grouped by distance
        let hashTable = {};
        let founded = 0;
        for (let row of rows) {
            let distance = this.#rowDistance(row, columns);

            // hack to optimize results - exclude rows with no distance detected
            if (distance % columns.length !== 0) {
                hashTable[distance] = hashTable[distance] ?? [];
                hashTable[distance].push(row);
                founded ++;
            }

            // stop after finding first k neighbors
            if (founded >= this.#k) {
                break;
            }
        }

        // list of results
        // sort asc by distance
        // concat groups od results rows
        return Object
                .entries(hashTable)
                .sort((a, b) => Number(a[0]) - Number(b[0]))
                .reduce((rows, a) => rows.concat(a[1]), []);
    }
}