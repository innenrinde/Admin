<template>
		<div
			@click="emitFocus"
			class="input-container"
		>
			<input
				v-model="model"
				v-focus="focus"
				:placeholder="placeholder"
				:readonly="readonly"
				@blur="emitBlur"
			/>
			<font-awesome-icon
				v-if="icon"
				:icon="icon"
				class="icon"
			/>
		</div>
</template>

<script setup>
import { defineEmits, defineModel, defineProps } from "vue";

const model = defineModel();

const emit = defineEmits(["focus", "blur"]);

const props = defineProps({
	placeholder: String,
	readonly: Boolean,
	icon: Array,
	focus: { type: Boolean, default: false },
});

/**
 * simulate to focus input element
 */
const emitFocus = () => {
	emit("focus");
}

/**
 * simulate blur input element
 */
const emitBlur = () => {
	emit("blur");
}

</script>

<style scoped lang="scss">
.input-container {
	display: flex;
	align-items: center;
}

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
	background-color: #ffffeb;
}

.icon {
	margin-left: -30px;
	font-size: 18px;
	background-color: #fff;
	border-radius: 20px;
	padding: 4px;
}
</style>