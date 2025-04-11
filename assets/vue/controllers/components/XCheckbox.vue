<template>
	<input
		type="checkbox"
		v-model="model"
	/>
</template>

<script setup>
import { defineModel } from "vue";

const model = defineModel();

</script>

<style scoped lang="scss">
</style>