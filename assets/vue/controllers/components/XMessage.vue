<template>
	<div
		class="message-content"
		:class="{ 'error': type === 'error', 'success': type === 'success' }"
		@click="closeMessage"
	>
		<div class="title">
			{{ title }}
		</div>
		<div>
			{{ message }}
		</div>

	</div>
</template>

<script setup>
import { defineProps, defineEmits, toRefs, onMounted } from "vue";

const emit = defineEmits(["close"]);

const props = defineProps({
	title: String,
	message: String,
	type: String,
});

const { title, message, type } = toRefs(props);

/**
 * Hooking...
 */
onMounted(() => {
	setTimeout(() => {
		closeMessage();
	}, 3000);
});

/**
 * Close message panel
 */
const closeMessage = () => {
	emit("close");
};

</script>

<style scoped lang="scss">
.message-content {
	position: fixed;
	top: 0;
	right: 0;
	margin: 5px;
	width: 300px;
	min-width: 200px;
	max-width: 600px;
	padding: 10px;
	background-color: #fff;
	border: solid 1px #c8c8c8;
	box-shadow: 1px 1px 5px #d3d3d3;
	border-radius: 5px;
	text-align: left;
	overflow: auto;
	animation: panelAnimation 0.3s;

	.title {
		font-size: 14px;
		font-weight: bold;
		margin-bottom: 10px;
	}
}

.error {
	border-color: #FF0000FF;
	box-shadow: 1px 1px 5px #ff9494;
	color: #FF0000FF;
}

.success {
	border-color: #008000;
	box-shadow: 1px 1px 5px #69aa69;
	color: #008000;
}

@keyframes panelAnimation {
	from {
		right: -100%;
	}
	to {
		right: 0;
	}
}
</style>