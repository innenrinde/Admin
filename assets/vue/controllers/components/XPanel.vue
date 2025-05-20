<template>
	<div class="panel-content">
		<div class="shadow"
			@click="closeAction"
		/>
		<div
			class="panel"
			:class="size"
		>
			<div class="title">{{ title }}</div>
			<div class="slot-content">
				<slot name="content"/>
			</div>
			<div class="actions">
				<x-button
					title="Close"
					type="secondary"
					@click="closeAction"
				/>
				<x-button
					:title="okLabel"
					type="primary"
					@click="confirmAction"
				/>
			</div>
		</div>
	</div>
</template>

<script setup>
import XButton from "./XButton.vue";
import { defineProps, defineEmits, toRefs } from "vue";

const props = defineProps({
	size: String,
	title: String,
	okLabel: { type: String, default: "Yes" }
});

const { size, title, okLabel } = toRefs(props);

const emit = defineEmits(["ok", "close"]);

/**
 * Emmit user confirmation
 */
const confirmAction = () => {
	emit("ok");
}

/**
 * Emmit closing panel
 */
const closeAction = () => {
	emit("close");
};
</script>

<style scoped lang="scss">
.panel-content {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	z-index: 9999;
	-webkit-backdrop-filter: blur(2px);
	backdrop-filter: blur(2px);

	.shadow {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: #c8c8c8;
		opacity: 60%;
		animation: fadeBackgroundShadow 0.5s;
		animation-fill-mode: forwards;
	}

	.panel {
		position: fixed;
		top: 0;
		margin: 20px;
		left: 50%;
		transform: translate(-50%, 0%);
		width: 50%;
		height: calc(100% - 65px);
		min-width: 200px;
		max-width: 600px;
		display: flex;
		flex-direction: column;
		padding: 10px;
		background-color: #fff;
		border: solid 1px #fff;
		box-shadow: 1px 1px 50px #d3d3d3;
		border-radius: 10px;
		text-align: center;
		overflow: auto;
		animation: panelAnimation 0.3s;

		.title {
			font-size: 14px;
			margin-bottom: 10px;
			font-weight: bold;
		}

		.slot-content {
			height: 100%;
			overflow: auto;
		}

		.actions {
			margin-top: 10px;
			padding-top: 10px;
			border-top: solid 1px #efefef;
		}
	}

	.small {
		height: 240px;
	}

	.medium {
		height: 400px;
	}
}

@keyframes fadeBackgroundShadow {
	from {
		background-color: transparent;
	}
	to {
		background-color: #c8c8c8;
	}
}

@keyframes panelAnimation {
	from {
		top: -100%;
	}
	to {
		top: 0;
	}
}
</style>