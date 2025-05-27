<template>
	<button
		:class="type"
	>
		{{ title }}
	</button>
</template>

<script setup>
import { defineProps, toRefs } from "vue";

const props = defineProps({
	title: String,
	type: String,
});

const { title, type } = toRefs(props);

</script>

<style scoped lang="scss">
.primary,
.secondary {
	border-radius: 5px;
	padding: 7px;
	margin: 2px 4px 2px 0;
	min-width: 80px;
	cursor: pointer;
}

.primary {
	background-color: var(--primary-button-color);
	border: solid 1px var(--primary-button-color);
	color: var(--primary-text-color);
}

.primary:hover {
	background-color: var(--primary-button-hover-color);
	border-color: var(--primary-button-hover-color);
	animation: primaryAnimation 0.3s;
}

.secondary {
	background-color: var(--secondary-button-color);
	border: solid 1px var(--secondary-button-border-color);
	color: var(--secondary-text-color);
}

.secondary:hover {
	background-color: var(--secondary-button-hover-color);
	border: solid 1px var(--secondary-button-border-hover-color);
	animation: secondaryAnimation 0.3s;
}

@keyframes primaryAnimation {
	from {
		background-color: var(--primary-button-color);
	}
	to {
		background-color: var(--primary-button-hover-color);
	}
}

@keyframes secondaryAnimation {
	from {
		background-color: var(--secondary-button-color);
	}
	to {
		background-color: var(--secondary-button-hover-color);
	}
}

</style>