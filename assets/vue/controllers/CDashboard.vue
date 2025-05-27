<template>

	<div class="dashboard-container">
		<div class="charts">
			<x-chart
				v-for="chart in charts"
				:index="chart"
				:label="chart.label"
				:count="chart.count"
				:color="chart.color"
				:link="chart.link"
			/>
		</div>
		<div class="presentation" v-html="presentation" />
	</div>

</template>

<script setup>
import { defineProps, toRefs, onMounted, reactive, ref } from "vue";
import axios from "axios";
import XChart from "../components/XChart.vue";

const props = defineProps({
	url: Object,
});

const { url } = toRefs(props);

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