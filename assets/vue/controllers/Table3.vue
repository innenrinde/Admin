<template>
  <div class="container-table">
    <div class="header-bar">
      <div>
        Search by
        <input
          class="keywords"
          type="text"
          placeholder="type here a phrase and then Enter..."
          v-model="query"
          @keydown="onQueryEnter"
        />
      </div>
      <div>
        k =
        <input
          class="knn"
          type="text"
          placeholder="define k number"
          v-model="kNumber"
          @keydown="onKNumberEnter"
        />
      </div>
      <div>
        <input
          class="button"
          type="button"
          value="Apply"
          @click="clickApply"
        />
      </div>
    </div>

    <div class="table-section">
      <div class="table">
        <div
          class="header"
        >
          <div
            v-for="column in visibleColumns"
            :key="column"
            :class="{ [column.width]: column.width, 'align-center': column.type === 'boolean'}"
          >
            {{ column.title }}
          </div>
	        <div class="wp80">
						<!-- Operations-->
	        </div>
        </div>

        <div
          v-for="row in localRows"
          :key="row"
          class="content"
        >
          <div
            v-for="column in visibleColumns"
            :key="column"
          >
	          <span
		          v-if="column.type === 'checkbox'"
	            class="align-center"
	          >
							<font-awesome-icon :icon="['far', 'circle-check']" v-if="row[column.field]" />
							<span v-else>-</span>
	          </span>
	          <span v-else-if="column.type === 'datetime'">
		          {{ dateFormat(row[column.field]) }}
	          </span>
	          <span v-else>{{ row[column.field] }}</span>
          </div>

	        <div class="actions">
		        <span
			        @click="deleteRow(row)"
              title="Delete"
			        class="red"
		        >
			        <font-awesome-icon :icon="['fas', 'xmark']" />
		        </span>
		        <span
			        @click="editRow(row)"
			        title="Edit"
		          class="blue"
		        >
			        <font-awesome-icon :icon="['far', 'pen-to-square']" />
		        </span>
	        </div>

        </div>

      </div>
    </div>

  </div>

	<el-dialog
		v-model="deleteDialogVisible"
		title="Delete row"
		width="500"
		center
	>
		<span class="dialog-message">Do you want to delete the row #{{ selectedRow[pk().field] }} ?</span>
		<template #footer>
			<div class="dialog-footer">
				<el-button @click="deleteDialogVisible = false">No</el-button>
				<el-button type="primary" @click="confirmDeleteRow">
					Yes
				</el-button>
			</div>
		</template>
	</el-dialog>

	<el-dialog
		v-model="editDialogVisible"
		title="Edit row"
		width="500"
		center
	>
		<Form
			v-if="editDialogVisible"
			:columns="columns"
			:values="editForm"
			:has-close-button="true"
			@save="confirmEditRow"
			@close="closeEditRow"
		/>
	</el-dialog>

</template>

<script setup>
import { defineProps, defineModel, toRefs, reactive, ref, watch, onMounted } from "vue";
import { kNNSearch } from "../lib/kNNSearch";
import { HttpRequestService } from "../services/HttpRequestService";
import DateTimeTransformer from "../transformers/DateTimeTransformer";
import axios from "axios";

const props = defineProps({
  columns: Array,
  rows: Array,
  url: Object,
});

const query = defineModel("vector");
const kNumber = defineModel({ default: 100 });

const { columns, url } = toRefs(props);

const visibleColumns = columns.value.filter(column => !column.hidden);

let rows = [];
let localRows = reactive([]);

let selectedRow = null;
let deleteDialogVisible = ref(false);

let editForm = null;
let editDialogVisible = ref(false);

// watch(rows, async (data) => {
//   localRows = reactive([...data]);
// });

/**
 * Detect pk for given list of columns
 * @returns {any}
 */
const pk = () => {
	let pk = columns.value.find(column => column.isPk);

	if (!pk) {
		throw new Error("Can't find a PK column!");
	}

	return pk;
};

/**
 * Ask to delete a row
 * @param row
 */
const deleteRow = (row) => {
	selectedRow = row;
	deleteDialogVisible.value = true;
};

/**
 * Perform deletion
 */
const confirmDeleteRow = () => {
	axios
		.delete(url.value.delete, {
			headers: {
				Authorization: ""
			},
			data: selectedRow[pk().field]
		})
		.then(response => {
			HttpRequestService.parseResponse(response, () => {
				processDeletedRow(response.data);
			});
			deleteDialogVisible.value = false;
		});
};

/**
 * Delete row from local list
 * @param {Object} data
 */
const processDeletedRow = (data) => {
	let id = pk();

	if (!data[id.field]) {
		throw new Error("Can't find a PK field to be deleted!");
	}

	localRows = localRows.filter(row => row[id.field] !== data[id.field]);
};

/**
 * Ask to edit a row
 * @param row
 */
const editRow = (row) => {
	editForm = { ...row };
	editDialogVisible.value = true;
}

/**
 * Perform edit
 */
const confirmEditRow = (values) => {
	axios
		.post(url.value.post, values)
		.then(response => {
			HttpRequestService.parseResponse(response, () => {
				processEditedRow(response.data.content);
				editDialogVisible.value = false;
			});
		});
};

/**
 * Close edit form
 */
const closeEditRow = () => {
	editDialogVisible.value = false;
};

/**
 * Update row into local list
 * @param {Object} data
 */
const processEditedRow = (data) => {
	let id = pk();

	if (!data[id.field]) {
		throw new Error("Can't find a PK field to be updated!");
	}

	let row = localRows.find(row => row[id.field] === data[id.field]);

	columns.value.forEach(column => {
		if (data[column.field]) {
			row[column.field] = data[column.field];
		}
	});
};

/**
 * Starting local filter
 * @type {kNNSearch}
 */
let kNN = new kNNSearch();
kNN.setK(kNumber.value);

/**
 * Get records
 */
const getTableDataList = () => {
  axios
    .get(url.value.get)
    .then(response => {
      if (!response.data.content) {
        throw new Error(`${url.value.list} response => .data.content not found`);
      }
      rows = response.data.content;
			rows.forEach(row => localRows.push(row));
    });
}

/**
 * Hooking...
 */
onMounted(() => {
  getTableDataList();
});

/**
 * Get filtered rows
 */
const applyResults = () => {
  let results = kNN.applySearch(rows, columns.value);
  localRows.splice(0);
  results.forEach(row => localRows.push(row));
}

/**
 * On change keywords
 * @param event
 */
let onQueryEnter = (event) => {
  if (event.keyCode === 13) {
    kNN.setVector(query.value);
    applyResults();
  }
};

/**
 * On change k number
 * @param event
 */
let onKNumberEnter = (event) => {
  if (event.keyCode === 13) {
    kNN.setK(kNumber.value);
    applyResults();
  }
};

/**
 * Perform search by button click
 */
let clickApply = () => {
  kNN.setK(kNumber.value);
  kNN.setVector(query.value);
  applyResults();
}

/**
 * Custom date time format
 * @param {String} value
 * @returns {String}
 */
const dateFormat = (value) => {
	return value ? DateTimeTransformer.transform(value) : "-";
};

</script>

<style lang="scss">
.container-table {
  font-size: 12px;
  height: 100%;
  display: flex;
  flex-direction: column;
	background-color: #fff;
	background-image: url("/assets/img/bk.gif");
	background-repeat: repeat;
}

.header-bar {
  padding: 20px 20px 10px 20px;
  display: flex;
  justify-content: center;
  align-items: center;

  > div {
    margin: 0 5px 0 5px;
  }

  input.keywords,
  input.knn,
  input.button {
    padding: 7px;
    border: solid 1px #c3c3c3;
    border-radius: 5px;
    box-shadow: #cdcdcd 1px 1px 3px;
  }

  input.keywords {
    width: 300px;
  }

  input.knn {
    width: 50px;
  }

  input.button {
    margin-left: 3px;
    cursor: pointer;
  }

  input.button:hover {
    background-color: #f3f4f6;
  }
}

@media (max-width: 600px) {
  .header-bar {
    flex-direction: column;
  }

  .header-bar > div {
    margin-bottom: 10px;
  }
}

.table-section {
  display: flex;
  overflow: auto;
  margin: 5px 10px;
  border-bottom: solid 1px #dadada;
	box-shadow: #cdcdcd 1px 1px 3px;

  .table {
    display: table;
    text-align: left;
    border: solid 1px #dadada;
    border-top: none;
    border-bottom: none;

    .header {
      display: table-row;
      position: sticky;
      top: 0;
	    z-index: 1;

      > div {
        display: table-cell;
        background-color: #f3f4f6;
        border-top: solid 1px #dadada;
        border-bottom: solid 1px #dadada;
        font-weight: 600;
        color: #036593;
        padding: 7px;
      }

      .w1 {
        width: 1%;
      }

      .w10 {
        width: 10%;
      }

      .w30 {
        width: 30%;
      }

	    .w100 {
		    width: 100%;
	    }

	    .wp80 {
		    width: 10px;
	    }
    }

	  .align-center {
		  display: block;
		  text-align: center;
	  }

    .content {
      display: table-row;

      &:nth-child(odd) > div {
        background-color: #f0fdf4;
      }

      &:hover > div {
        background-color: #4bb0df;
        color: #fff;
      }

      > div {
	      background-color: #fff;
        display: table-cell;
        padding: 7px;
      }

	    .actions {
		    display: flex;
		    flex-direction: row;
		    flex-wrap: nowrap;

		    span {
			    display: inline-block;
			    cursor: pointer;
			    border-radius: 25px;
			    width: 25px;
			    height: 25px;
			    font-size: 14px;
			    color: #fff;
			    text-align: center;
			    align-items: center;
			    align-content: center;
			    margin: 0 2px;
		    }

		    span.red {
			    border: solid 1px #f35959;
			    background-color: #ffa1a1;
		    }

		    span.blue {
			    border: solid 1px #7f7fe8;
			    background-color: #b6b6e4;
		    }
	    }
    }
  }
}

</style>