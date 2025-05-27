<template>

	<x-input
		v-model="label"
		readonly
		:icon="showList ? ['fas', 'chevron-up'] : ['fas', 'chevron-down']"
		class="pointer"
		@click="clickInput"
		@blur="blurInput"
	/>

	<div
		class="options-list"
		:class="{ 'opened-list': showList, 'closed-list': !showList }"
		@mouseleave="mouseLeaveList"
		@mouseover="mouseOverList"
	>
		<div
			v-for="option in options"
			:key="option"
			:class="{ 'selected' : option.value === model?.value }"
			@click="selectValue(option)"
		>
			{{ option.label }}
		</div>
	</div>

</template>

<script setup>
import { defineModel, defineProps, toRefs, ref } from "vue";
import XInput from "./XInput.vue";

const model = defineModel();

const props = defineProps({
	options: Array,
});

const { options } = toRefs(props);

const label = ref(model.value?.label);

const showList = ref(false);

/**
 * Flag to know if a list with options is opened
 * @type {boolean}
 */
let overList = false;

/**
 * Simulate opening list
 */
const clickInput = () => {
	showList.value = !showList.value;
}

/**
 * Try to close list. We clicked ooutside
 */
const blurInput = () => {
	if (!overList) {
		showList.value = false;
	}
}

/**
 * List can be closed
 */
const mouseLeaveList = () => {
	overList = false;
}

/**
 * Keep list opened
 */
const mouseOverList = () => {
	overList = true;
}

/**
 * Select a value from list
 * @param {Object} option
 */
const selectValue = (option) => {
	label.value = option.label;
	model.value = option;
	showList.value = false;
}

</script>

<style scoped lang="scss">
.pointer :deep(input) {
	cursor: pointer;
}

.options-list {
	border: solid 1px var(--input-border-color);
	background-color: var(--input-background-focus-color);
	border-radius: 5px;
	box-shadow: var(--input-shadow-color) 2px 2px 4px;
	position: absolute;
	width: 320px;
	max-height: 200px;
	overflow: auto;
	overflow-x: hidden;

	div {
		padding: 10px;
		cursor: pointer;
	}

	div.selected,
	div:hover {
		background-color: var(--input-background-hover-color);
	}
}

.opened-list {
	height: auto;
	animation: openAnimation 0.3s;
}

.closed-list {
	height: 0;
	display: none;
}

@keyframes openAnimation {
	from {
		opacity: 0;
	}
	to {
		opacity: 100%;
	}
}

</style>