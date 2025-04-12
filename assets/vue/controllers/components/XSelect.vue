<template>
	<select
		v-model="model"
	>
		<option
			v-for="option in options"
			:value="option.value"
		>
			{{ option.label }}
		</option>
	</select>
</template>

<script setup>
import { defineModel, defineProps, toRefs } from "vue";

const model = defineModel();

const props = defineProps({
	options: Array,
});

const { options } = toRefs(props);

options.value.unshift({
	value: undefined,
	label: "- select an option -"
});

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