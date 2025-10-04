<template>
		<div
			@click="emitFocus"
			class="input-container"
		>
			<input
        type="file"
        accept="image/png, image/jpeg"
        ref="inputFile"
        :placeholder="placeholder"
				:readonly="readonly"
				@blur="emitBlur"
        @change="emitChange"
			/>
		</div>
</template>

<script setup>
import { useTemplateRef, defineEmits, defineProps } from "vue";

const input = useTemplateRef("inputFile");

const emit = defineEmits(["focus", "blur"]);

const props = defineProps({
	placeholder: String,
	readonly: Boolean,
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

/**
 * emit selected file
 */
const emitChange = () => {
  emit("update:modelValue", input.value.files[0]);
}

</script>

<style scoped lang="scss">
.input-container {
	display: flex;
	align-items: center;
}
</style>