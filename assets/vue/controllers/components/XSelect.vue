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
</style>