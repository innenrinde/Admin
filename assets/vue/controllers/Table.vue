<template>
  <div class="container-table">

    <div class="header-bar">
			<x-input
				type="text"
				placeholder="Search..."
				v-model="query"
				@focus="openSearch"
			/>
    </div>

    <div class="table-section">
      <div class="table">
        <div
          class="header"
        >
          <div
            v-for="column in visibleColumns"
            :key="column"
            :class="{ [column.width]: column.width, 'align-center': column.type === 'checkbox'}"
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

        </div> <!--END row-->

      </div> <!--END table-->
    </div>

  </div>

	<x-panel
		v-if="deleteDialogVisible"
		title="Delete row"
		size="small"
		@close="closeDeleteRow"
		@ok="confirmDeleteRow"
	>
		<template #content>
			<span>Are you sure that you want to delete the row #{{ selectedRow[pk().field] }} ?</span>
		</template>
	</x-panel>

	<x-panel
		v-if="editDialogVisible"
		title="Edit row"
		ok-label="Save"
		@close="closeEditRow"
		@ok="confirmEditRow"
	>
		<template #content>
			<Form
				ref="form"
				:columns="columns"
				:values="editForm"
				:has-close-button="false"
				:has-save-button="false"
			/>
		</template>
	</x-panel>

	<search-panel
		v-show="searchFocus"
		@close="closeSearch"
		@ok="applySearch"
	/>

</template>

<script setup>
import { defineProps, defineModel, toRefs, reactive, ref, onMounted, useTemplateRef } from "vue";
import { kNNSearch } from "../lib/kNNSearch";
import { HttpRequestService } from "../services/HttpRequestService";
import DateTimeTransformer from "../transformers/DateTimeTransformer";
import axios from "axios";
import XPanel from "./components/XPanel.vue";
import XInput from "./components/XInput.vue";
import SearchPanel from "./SearchPanel.vue";

const props = defineProps({
  columns: Array,
  rows: Array,
  url: Object,
});

const query = defineModel("");

const { columns, url } = toRefs(props);

const visibleColumns = columns.value.filter(column => !column.hidden);

let rows = [];
let localRows = reactive([]);

let selectedRow = null;
let deleteDialogVisible = ref(false);

let editForm = null;
let editDialogVisible = ref(false);

let form = ref(null);
let searchFocus = ref(false);

/**
 * Show advanced search
 */
const openSearch = () => {
	searchFocus.value = true;
}

/**
 * Close advanced search
 */
const closeSearch = () => {
	searchFocus.value = false;
}

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
 * Close confirm dialog to delete a row
 */
const closeDeleteRow = () => {
	deleteDialogVisible.value = false;
}

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
const confirmEditRow = () => {
	axios
		.post(url.value.post, form.value.getValues())
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
	searchFocus.value = false;
}

/**
 * Perform search by button click
 */
let applySearch = ({ value, k }) => {
	query.value = value;
  kNN.setK(k);
  kNN.setVector(value);
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
}

.header-bar {
	padding: 10px 10px 0 10px;
	display: flex;
	justify-content: right;
	align-items: flex-end;

	input {
		padding: 5px;
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
  margin: 5px 10px 10px 10px;
  border-bottom: solid 1px #dadada;

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
<script setup lang="ts">
</script>