<template>
	<input
		type="checkbox"
		v-model="model"
		:disabled="disabled"
		:id="id"
	/>
	<label
		:for="id"
		v-if="label"
	>
		{{ label }}
	</label>
</template>

<script setup>
import { defineModel, defineProps, toRefs } from "vue";

const props = defineProps({
	label: String,
	disabled: Boolean,
});

const { label, disabled } = toRefs(props);

const id = Math.random();

const model = defineModel();

</script>

<style scoped lang="scss">
</style>