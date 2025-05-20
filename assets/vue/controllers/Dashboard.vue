<template>

	<div class="dahsboard-container">
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
import { defineProps, toRefs } from "vue";
import XChart from "./components/XChart.vue";

const props = defineProps({
	charts: Array,
	presentation: String,
});

const { charts, presentation } = toRefs(props);

</script>

<style scoped lang="scss">
.dahsboard-container {
	display: flex;
	flex-direction: column;
	height: 100%;

	.charts {
		display: flex;
		padding: 20px;
	}

	.presentation {
		margin: 0 25px 25px 25px;
		border: solid 1px #c8c8c8;
		border-radius: 7px;
		overflow: auto;
	}
}
</style>