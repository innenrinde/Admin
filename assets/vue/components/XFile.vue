<template>
		<div
			@click="emitFocus"
			class="input-container"
		>
			<input
        type="file"
        :accept="accept"
        ref="inputFile"
        :placeholder="placeholder"
				:readonly="readonly"
				@blur="emitBlur"
        @change="emitChange"
			/>
      <div v-if="model">
        <a :href="model" target="_blank">Open existing file</a>
      </div>
		</div>
</template>

<script setup>
import { useTemplateRef, defineEmits, defineProps, defineModel } from "vue";

const model = defineModel();

const input = useTemplateRef("inputFile");

const emit = defineEmits(["focus", "blur"]);

const props = defineProps({
	placeholder: String,
	readonly: Boolean,
	focus: { type: Boolean, default: false },
  accept: String,
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