<template>
	<input
		type="password"
		v-model="model"
		:placeholder="placeholder"
	/>
</template>

<script setup>
import { defineModel, defineProps, toRefs } from "vue";

const model = defineModel();

const props = defineProps({
	placeholder: String,
});

const { placeholder } = toRefs(props);

</script>

<style scoped lang="scss">
input {
	padding: 10px;
	border: solid 1px var(--input-border-color);
	border-radius: 5px;
	box-shadow: var(--input-shadow-color) 1px 1px 2px;
	width: 300px;
}

input:focus {
	appearance: none;
	outline: none;
	border: solid 1px var(--input-border-color);
	background-color: var(--input-background-focus-color);
}
</style>