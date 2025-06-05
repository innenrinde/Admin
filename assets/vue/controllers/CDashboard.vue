<template>

	<div class="dashboard-container">
		<div class="charts">
			<x-chart
				v-for="chart in charts"
				:index="chart"
				:label="chart.label"
				:count="chart.count"
				:color="chart.color"
				:component="chart.menu"
				@action="chartAction"
			/>
		</div>
		<div
			v-if="presentation"
			class="presentation"
			v-html="presentation"
		/>
	</div>

</template>

<script setup>
import {defineProps, toRefs, onMounted, reactive, ref, defineEmits} from "vue";
import axios from "axios";
import XChart from "../components/XChart.vue";

const props = defineProps({
	url: Object,
});

const { url } = toRefs(props);

const emit = defineEmits(["action"]);

let charts = reactive([]);
let presentation = ref("");

/**
 * Retrieve dashboard configuration
 */
onMounted(() => {
	axios
		.get(url.value.get, {})
		.then(response => {
			charts = response.data.charts;
			presentation.value = response.data.presentation;
		});
});

/**
 * Action click for a chart component
 * @param {Object} data
 */
const chartAction = (data) => {
	emit("action", data);
}

</script>

<style scoped lang="scss">
.dashboard-container {
	display: flex;
	flex-direction: column;
	height: 100%;

	.charts {
		display: flex;
	}

	.presentation {
		margin: 15px 5px 5px 5px;
		border: solid 1px var(--table-border-color);
		border-radius: 7px;
		overflow: auto;
	}
}
</style>