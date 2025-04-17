<template>

	<x-input
		v-model="label"
		@focus="openList"
	/>

	<div
		v-if="showList"
		class="options-list"
	>
		<div
			v-for="option in options"
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

const openList = () => {
	showList.value = true;
}

const selectValue = (option) => {
	label.value = option.label;
	model.value = option;
	showList.value = false;
}

</script>

<style scoped lang="scss">
select {
	padding: 10px;
	border: solid 1px #a3a3a3;
	border-radius: 5px;
	box-shadow: #cdcdcd 1px 1px 2px;
	width: 323px;
	cursor: pointer;
}

select:focus {
	appearance: none;
	outline: none;
	border: solid 1px #a3a3a3;
	background-color: #ffffcb;
}
</style>