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
	border: solid 1px #a3a3a3;
	border-radius: 5px;
	box-shadow: #cdcdcd 1px 1px 2px;
	width: 300px;
}

input:focus {
	appearance: none;
	outline: none;
	border: solid 1px #a3a3a3;
	background-color: #e4ffcb;
}
</style>