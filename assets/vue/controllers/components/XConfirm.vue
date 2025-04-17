<template>
	<div
		class="message-content"
	>
		<div class="shadow"
     @click="closeAction"
		/>
		<div
			class="message"
			:class="{ 'closed': closedAnimation }"
		>
			<div class="title">
				{{ title }}
			</div>
			<div>
				{{ message }}
			</div>
			<div class="actions">
				<x-button
					title="Close"
					type="secondary"
					@click="closeAction"
				/>
				<x-button
					title="Confirm"
					type="primary"
					@click="confirmAction"
				/>
			</div>
		</div>

	</div>
</template>

<script setup>
import { defineProps, defineEmits, toRefs, ref } from "vue";
import XButton from "./XButton.vue";

const emit = defineEmits(["ok", "close"]);

const props = defineProps({
	title: String,
	message: String,
});

const { title, message } = toRefs(props);

const closedAnimation = ref(false);

/**
 * Close message panel
 */
const closeAction = () => {
	doAction(() => emit("close"));
};

/**
 * Ok action
 */
const confirmAction = () => {
	doAction(() => emit("ok"));
}

/**
 * Do a message action
 * @param callback
 */
const doAction = (callback) => {
	closedAnimation.value = true;
	setTimeout(() => {
		callback();
	}, 200);
}

</script>

<style scoped lang="scss">
.message-content {
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

	.message {
		position: fixed;
		top: calc(50% - 120px);
		left: 50%;
		transform: translate(-50%, 0%);
		width: 50%;
		height: 120px;
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
		opacity: 100%;
		animation: panelAnimation 0.3s;

		.title {
			font-size: 14px;
			margin-bottom: 10px;
			font-weight: bold;
		}

		.actions {
			margin-top: 10px;
			padding-top: 10px;
			border-top: solid 1px #efefef;
		}
	}

	.closed {
		animation: closedAnimation 0.3s;
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
		opacity: 0%;
	}
	to {
		opacity: 100%;
	}
}

@keyframes closedAnimation {
	from {
		opacity: 100%;
	}
	to {
		opacity: 0%;
	}
}
</style>