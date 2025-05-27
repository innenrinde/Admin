<template>
  <div class="container-table">

    <div class="header-bar">

			<h1><span>{{ pager.total }} records</span><b>{{ sectionTitle }}</b></h1>

			<x-input
				type="text"
				placeholder="Search..."
				readonly
				v-model="query"
				@focus="openSearch"
			/>
    </div>

    <x-table
			v-if="columns.length"
			:columns="columns"
			:rows="localRows"
			:pager="pager"
			@edit="editRow"
			@delete="deleteRow"
			@change-page="changePage"
		/>

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
			<c-form
				ref="form"
				:columns="columns"
				:values="editForm"
				:has-close-button="false"
				:has-save-button="false"
			/>
		</template>
	</x-panel>

	<c-search-panel
		v-if="searchFocus"
		:query-text="query"
		@close="closeSearch"
		@ok="applySearch"
	/>

</template>

<script setup>
import { defineProps, defineModel, toRefs, reactive, ref, onMounted } from "vue";
import { kNNSearch } from "../lib/kNNSearch";
import { HttpRequestService } from "../services/HttpRequestService";
import axios from "axios";
import XPanel from "../components/XPanel.vue";
import XInput from "../components/XInput.vue";
import CSearchPanel from "./CSearchPanel.vue";
import XTable from "../components/XTable.vue";

const props = defineProps({
	title: String,
	sectionTitle: String,
  url: Object,
});

const query = defineModel("");

const { sectionTitle, url } = toRefs(props);

let pager = reactive({
	total: 0,
	page: 0,
	limit: 0,
});

/**
 * List of columns fetched by api; no columns is like no table to display
 * @type {Reactive<*[]>}
 */
let columns = reactive([]);

/**
 * all rows fetched by api
 * @type {Array}
 */
let rows = [];

/**
 * locally filtered rows if a such action is performed
 * @type {Reactive<*[]>}
 */
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
	let pk = columns.find(column => column.isPk);

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

	columns.forEach(column => {
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
    .get(url.value.get, {
			params: {
				...pager,
				list: ['columns', 'rows']
			}
		})
    .then(response => {
      if (!response.data) {
        throw new Error(`${url.value.get} response => .data not found`);
      }

      columns = response.data.columns ?? [];
      rows = response.data.rows ?? [];

			let responsePager = response.data.pager;
			pager.total = Number(responsePager.total);
			pager.page = Number(responsePager.page);
			pager.limit = Number(responsePager.limit);

			localRows.splice(0);
			rows.forEach(row => localRows.push(row));
    });
}

/**
 * Change pages
 * @param {object} data
 */
const changePage = (data) => {
	pager = data;
	getTableDataList();
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
  let results = kNN.applySearch(rows, columns);
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

</script>

<style lang="scss">
.container-table {
  font-size: 12px;
  height: 100%;
	width: 100%;
  display: flex;
  flex-direction: column;
	background-color: var(--primary-background-color);
}

.header-bar {
	padding: 10px 0 5px 0;
	display: flex;
	align-items: center;

	h1 {
		display: flex;
		font-size: 14px;
		font-weight: 400;
		width: 100%;
		color: var(--secondary-color);

		span {
			background-color: var(--table-header-color);
			margin-right: 10px;
			padding: 2px 10px 2px 10px;
			border-radius: 15px;
			font-size: 12px;
			white-space: nowrap;
			align-content: center;
		}
	}

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

</style>