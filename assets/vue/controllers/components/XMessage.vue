<template>
	<div
		class="message-content"
		:class="{ 'error': type === 'error', 'success': type === 'success', 'closed': closedAnimation }"
		@click="closeMessage"
	>
		<div class="title">
			{{ title }}
		</div>
		<div class="content">
			<font-awesome-icon :icon="['fas', 'circle-exclamation']" v-if="type === 'error'" />
			<font-awesome-icon :icon="['fas', 'circle-check']" v-if="type === 'success'" />
			{{ message }}
		</div>

	</div>
</template>

<script setup>
import { defineProps, defineEmits, toRefs, onMounted, ref } from "vue";

const emit = defineEmits(["close"]);

const props = defineProps({
	title: String,
	message: String,
	type: String,
});

const { title, message, type } = toRefs(props);

const closedAnimation = ref(false);

/**
 * Set timeout to auto close notification
 */
onMounted(() => {
	setTimeout(() => {
		closeMessage();
	}, 8000);
});

/**
 * Close message panel
 */
const closeMessage = () => {
	closedAnimation.value = true;
	setTimeout(() => {
		emit("close");
	}, 100);
};

</script>

<style scoped lang="scss">
.message-content {
	position: relative;
	right: 0;
	margin: 5px;
	width: 300px;
	min-width: 200px;
	max-width: 600px;
	padding: 15px;
	background-color: #fff;
	border: solid 1px #c8c8c8;
	box-shadow: 1px 1px 5px #d3d3d3;
	border-radius: 7px;
	text-align: left;
	overflow: hidden;
	z-index: 9999;
	animation: openAnimation 0.3s;

	.title {
		font-size: 14px;
		font-weight: bold;
		margin-bottom: 10px;
	}

	.content {
		display: flex;
		align-items: center;
	}
}

.error svg,
.success svg {
	margin-right: 10px;
	font-size: 26px;
}

.error svg {
	color: #FF0000FF;
}

.success svg {
	color: #008000;
}

.closed {
	animation: closeAnimation 0.2s;
}

@keyframes openAnimation {
	from {
		right: -100%;
	}
	to {
		right: 0;
	}
}

@keyframes closeAnimation {
	from {
		right: 0;
	}
	to {
		right: -100%;
	}
}
</style>