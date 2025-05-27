<template>
	<div
		class="chart-container"
		:class="color"
		@click="gotoLink"
	>
		<div
			class="chart-label"
		>
			<label>{{ label }}</label>
			<span><font-awesome-icon :icon="['fas', 'arrow-up-right-from-square']" /></span>
		</div>
		<div
			class="chart-number"
		>
			<span
				:class="color"
			>
				{{ count }}
			</span>
		</div>
	</div>
</template>

<script setup>
import { defineProps, toRefs } from "vue";

const props = defineProps({
	label: String,
	count: Array,
	color: String,
	link: String,
});

const { label, count, color, link } = toRefs(props);

/**
 * Redirect to defined route
 */
const gotoLink = () => {
	document.location.href = link.value;
}

</script>

<style scoped lang="scss">
.chart-container {
	width: 100%;
	margin: 5px;
	padding: 10px;
	border: solid 1px #aaaaaa;
	background-color: #fbfbfb;
	border-radius: 5px;
	display: flex;
	flex-direction: column;
	cursor: pointer;

	&:hover {
		box-shadow: #787878 0 0 10px;
		animation: bkAnimation 0.3s;

		.chart-label span {
			display: initial;
		}
	}

	.chart-label {
		font-size: 14px;
		display: flex;
		height: 100%;

		label {
			width: 100%;
			cursor: pointer;
		}

		span {
			display: none;
			color: #000;
		}
	}

	.chart-number {
		margin-top: 10px;

		span {
			display: inline-block;
			font-size: 16px;
			font-weight: 500;
			padding: 0 10px;
			color: #fff;
			border-radius: 20px;
		}
	}

	.c1 {
		background-color: #00bc7d;
	}

	.c2 {
		background-color: #2b7fff;
	}

	.c3 {
		background-color: #e1a600;
	}

	.c4 {
		background-color: #fb2c36;
	}
}

@keyframes bkAnimation {
	from {
		box-shadow: none;
	}
	to {
		box-shadow: #787878 0 0 10px;
	}
}

.c1 {
	background-color: #00bc7d70;
	border: solid 1px #00bc7d;
}

.c2 {
	background-color: #2b7fff70;
	border: solid 1px #2b7fff;
}

.c3 {
	background-color: #f0b10070;
	border: solid 1px #e1a600;
}

.c4 {
	background-color: #fb2c3670;
	border: solid 1px #fb2c36;
}
</style>