<template>
	<ul
		class="pager-list"
	>
		<li
			v-for="index in noPages"
			:key="index"
			:class="{ 'selected': index === selectedPage }"
			@click="emitPage(index)"
		>
			{{ index }}
		</li>
	</ul>
</template>

<script setup>
import {defineProps, defineEmits, toRefs, watch, reactive} from "vue";

const emit = defineEmits(["page"]);

const props = defineProps({
	total: Number,
	page: Number,
	limit: Number,
});

const { total, page, limit } = toRefs(props);

let selectedPage = reactive(0);

let noPages = 0;
watch(total, (value) => {
	noPages = limit.value ? Math.ceil(value/limit.value) : 0;
});

const emitPage = (page) => {
	emit("page", {
		total: total.value,
		page: page - 1,
		limit: limit.value
	});

	selectedPage = page;
}

</script>

<style scoped lang="scss">
ul.pager-list {
	list-style-type: none;
	display: flex;
	margin: 0;
	padding: 10px;
	
	li {
		padding: 5px;
		cursor: pointer;
		
		&:hover,
		&.selected {
			background-color: #f0fdf4;
		}
	}
}
</style>