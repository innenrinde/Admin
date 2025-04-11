<template>
	<input
		type="date"
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
</style>