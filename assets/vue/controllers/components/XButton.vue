<template>
	<button
		:class="type"
	>
		{{ title }}
	</button>
</template>

<script setup>
import { defineProps, toRefs } from "vue";

const props = defineProps({
	title: String,
	type: String,
});

const { title, type } = toRefs(props);

</script>

<style scoped lang="scss">
.primary,
.secondary {
	border-radius: 5px;
	padding: 7px;
	margin: 2px 4px 2px 0;
	min-width: 80px;
	cursor: pointer;
}

.primary {
	background-color: #0076ae;
	border: solid 1px #0076ae;
	color: #fff;
}

.primary:hover {
	background-color: #008dd1;
	border-color: #008dd1;
	animation: primaryAnimation 0.3s;
}

.secondary {
	background-color: #fff;
	border: solid 1px #bcbcbc;
	color: #000;
}

.secondary:hover {
	background-color: #abe2ff;
	border: solid 1px #5dc5ff;
	animation: secondaryAnimation 0.3s;
}

@keyframes primaryAnimation {
	from {
		background-color: #0076ae;
	}
	to {
		background-color: #008dd1;
	}
}

@keyframes secondaryAnimation {
	from {
		background-color: #fff;
	}
	to {
		background-color: #abe2ff;
	}
}

</style>