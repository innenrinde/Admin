<template>

	<x-panel
		title="Search for all columns"
		ok-label="Search"
		size="small"
		@close="close"
		@ok="apply"
	>
		<template #content>
			<div class="search-container">
				<div>
					<label>
						Search by
					</label>
					<x-input
						class="keywords"
						type="text"
						placeholder="type here a phrase and then Enter..."
						v-model="query"
						:focus="true"
						@keydown="onQueryEnter"
					/>
				</div>
				<div>
					<label>
						using K =
					</label>
					<x-input
						class="knn"
						type="text"
						placeholder="define k number"
						v-model="kNumber"
						@keydown="onKNumberEnter"
					/>
				</div>
			</div>
		</template>
	</x-panel>

</template>

<script setup>
import { defineEmits, defineModel, defineProps, toRefs, onMounted } from "vue";
import XInput from "./components/XInput.vue";
import XPanel from "./components/XPanel.vue";

const props = defineProps({
	queryText: { type: String, default: "" }
});

const { queryText } = toRefs(props);

const emit = defineEmits(["ok", "close"]);

const query = defineModel("");
const kNumber = defineModel({ default: 100 });

/**
 * Catch event
 */
onMounted(() => {
	query.value = queryText.value;
});

/**
 * Enter on keywords field
 * @param event
 */
const onQueryEnter = (event) => {
	if (event.keyCode === 13) {
		apply();
	}
}

/**
 * Enter on k number field
 * @param event
 */
const onKNumberEnter = (event) => {
	if (event.keyCode === 13) {
		apply();
	}
}

/**
 * Close search panel
 */
const close = () => {
	emit("close");
}

/**
 * Perform search based on input criteria
 */
const apply = () => {
	emit("ok", {
		value: query.value,
		k: kNumber.value
	});
}

</script>

<style scoped lang="scss">
.search-container div {
	display: flex;
	align-content: center;
	align-items: center;
	margin: 5px 10px;

	label {
		text-align: left;
		width: 80px;
	}
}
</style>