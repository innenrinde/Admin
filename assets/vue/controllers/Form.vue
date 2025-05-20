<template>

	<div>
		<div v-if="title">
			<h1>{{ title }}</h1>
		</div>

		<div class="form-content">
			<div
				v-for="column in columns.filter(item => !item.isPk)"
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
	columns: Array,
	values: Object,
	url: Object,
	hasCloseButton: Boolean,
	hasSaveButton: { type: Boolean, default: true }
});

const { title, columns, values, url, hasCloseButton, hasSaveButton } = toRefs(props);

const emit = defineEmits(["sve", "close"]);

let form = ref({});

/**
 * Init form values
 */
onBeforeMount(() => {
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
