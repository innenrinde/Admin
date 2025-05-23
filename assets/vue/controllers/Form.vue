<template>

	<div>
		<div v-if="title">
			<h1>{{ title }}</h1>
		</div>

		<div class="form-content">
			<div
				v-for="column in localColumns.filter(item => !item.isPk)"
			  :key="column"
				class="form-line"
	    >
				<div>{{ column.title }}</div>
				<div>
					<x-select
						v-if="column.type === 'choice'"
						v-model="form[column.field]"
						:options="column.options"
					/>
					<x-checkbox
						v-else-if="column.type === 'checkbox'"
						v-model="form[column.field]"
					/>
					<x-date
						v-else-if="column.type === 'datetime'"
						v-model="form[column.field]"
					/>
					<x-password
						v-else-if="column.type === 'password'"
						v-model="form[column.field]"
						:placeholder="column.placeholder ?? ''"
					/>
					<x-input
						v-else
						v-model="form[column.field]"
						:placeholder="column.placeholder ?? ''"
					/>
				</div>
			</div>

			<div class="form-line">
				<div></div>
				<div>
					<x-button
						v-if="hasCloseButton"
						type="secondary"
						title="Close"
						@click="confirmClose"
					/>
					<x-button
						v-if="hasSaveButton"
						type="primary"
						title="Save"
						@click="confirmSave"
					/>
				</div>
			</div>
		</div>

	</div>

</template>

<script setup>
import axios from "axios";
import { HttpRequestService } from "../services/HttpRequestService";
import DateTimeTransformer from "../transformers/DateTimeTransformer";
import XButton from "./components/XButton.vue";
import XInput from "./components/XInput.vue";
import XPassword from "./components/XPassword.vue";
import XSelect from "./components/XSelect.vue";
import XCheckbox from "./components/XCheckbox.vue";
import XDate from "./components/XDate.vue";
import { defineProps, toRefs, ref, onBeforeMount, defineEmits } from "vue";

const props = defineProps({
	title: String,
	columns: {
		type: Array,
		default: []
	},
	values: {
		type: Object,
		default: {}
	},
	url: Object,
	hasCloseButton: {
		type: Boolean,
		default: false
	},
	hasSaveButton: {
		type: Boolean,
		default: true
	}
});

const { title, columns, values, url, hasCloseButton, hasSaveButton } = toRefs(props);

const emit = defineEmits(["sve", "close"]);

/**
 *
 * @type {Ref<UnwrapRef<*[]>, UnwrapRef<*[]> | *[]>}
 */
let localColumns = ref([]);

/**
 * All form values
 * @type {Object}
 */
let form = ref({});

/**
 *
 */
const getColumnsStructure = () => {
	if (columns.value.length) {
		localColumns = columns;
		return;
	}

	if (url.value.get) {
		axios
			.get(url.value.get, {
				params: {
					list: ['columns']
				}
			})
			.then(response => {
				console.log(response);
				localColumns.value = response.data.columns;
			});
	}
};

/**
 * Init form values
 */
onBeforeMount(() => {
	getColumnsStructure();
	form = values;
});

/**
 * Get form values
 * @return {Object}
 */
const getValues = () => {
	let values = { ... form.value };
	columns.value.forEach(column => {
		if (column.type === "datetime") {
			values[column.field] = DateTimeTransformer.reverseTransform(values[column.field]);
		}
	});

	return values;
};

/**
 * We need to do some actions from a parent component
 */
defineExpose({
	getValues
});

/**
 * Perform save data
 */
const confirmSave = () => {

	let values = getValues();

	if (url.value.put) {
		axios
			.put(url.value.put, values)
			.then(response => {
				HttpRequestService.parseResponse(response, () => {
					console.log(response.data.content);
				});
			});
	} else {
		emit("save", values);
	}
};

/**
 * Close action
 */
const confirmClose = () => {
	emit("close");
};
</script>

<style scoped>
h1 {
	margin: 0;
	font-size: 16px;
}

.form-content {
	display: table;
	margin: 10px 0;
	text-align: left;

	.form-line {
		display: table-row;

		> div {
			display: table-cell;
			padding: 5px 5px 5px 0;
		}
	}
}
</style>
