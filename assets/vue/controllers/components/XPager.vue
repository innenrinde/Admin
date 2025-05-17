<template>
	<div
		class="pager-navigation"
	>
		<ul
			class="pager-list"
			v-if="noPages > 1"
		>
			<li
				:class="{ 'disabled': selectedPage === 1 }"
				@click="emitPage(1)"
			>
				<font-awesome-icon :icon="['fas', 'backward-step']" title="First page" />
			</li>
			<li
				:class="{ 'disabled': selectedPage === 1 }"
				@click="emitPage(selectedPage - 1)"
			>
				<font-awesome-icon :icon="['fas', 'backward']" title="Prev page" />
			</li>
			<li
				v-for="index in pagesToShow"
				:key="index"
				:class="{ 'selected': index === selectedPage }"
				@click="emitPage(index)"
			>
				<span>{{ index }}</span>
			</li>
			<li
				:class="{ 'disabled': selectedPage === noPages }"
				@click="emitPage(selectedPage + 1)"
			>
				<font-awesome-icon :icon="['fas', 'forward']" title="Next page" />
			</li>
			<li
				:class="{ 'disabled': selectedPage === noPages }"
				@click="emitPage(noPages)"
			>
				<font-awesome-icon :icon="['fas', 'forward-step']" title="Last page" />
			</li>
		</ul>

		<div
			v-if="noPages > 1"
			class="pager-count"
		>
			page {{ selectedPage }} of {{ noPages }}
		</div>
	</div>

</template>

<script setup>
import { defineProps, defineEmits, toRefs, watch, ref } from "vue";

const emit = defineEmits(["page"]);

const props = defineProps({
	total: Number,
	page: Number,
	limit: Number,
});

const { total, limit } = toRefs(props);

/**
 * Keep selected page
 * @type {Ref<UnwrapRef<number>, UnwrapRef<number> | number>}
 */
let selectedPage = ref(1);

/**
 * Calculate number of pages
 * @type {number}
 */
let noPages = 0;
let pagesToShow = [];
watch(total, (value) => {
	noPages = limit.value ? Math.ceil(value/limit.value) : 0;
	createListOfPages();
});

/**
 * Create range pages to be displayed into list during navigation
 */
const createListOfPages = () => {
	let start = selectedPage.value - 3;
	let stop = selectedPage.value + 3;

	// check start limit
	if (start < 1) {
		start = 1;
		stop = 7;
	}

	// check end limit
	if (stop > noPages) {
		stop = noPages;
		start = noPages - 6 < 1 ? 1 : noPages - 6;
	}

	pagesToShow = Array.from(
		{ length: (stop - start) + 1 },
		(_, index) => start + index
	);
};

/**
 * Click on a page... from list pf pages
 * @param {number} page
 */
const emitPage = (page) => {

	// check page availability
	if (page < 1 || page > noPages) {
		return;
	}

	emit("page", {
		total: total.value,
		page: page - 1,
		limit: limit.value
	});

	selectedPage.value = page;
	createListOfPages();
}

</script>

<style scoped lang="scss">
.pager-navigation {
	display: flex;
	flex-direction: row;
	align-items: center;

	ul.pager-list {
		list-style-type: none;
		display: flex;
		margin: 0;
		padding: 7px;
		width: 100%;

		li {
			cursor: pointer;
			margin: 0 2px 0 2px;
			border-radius: 50px;
			height: 25px;
			min-width: 25px;
			display: flex;
			align-items: center;
			justify-content: center;

			&:hover,
			&.selected {
				background-color: #0076ae;
				color: #fff;
				animation: fadeBackgroundShadow 0.2s;
			}

			&.disabled {
				background-color: transparent;
				color: #b5b5b5;
				text-shadow: 1px 1px #fff;
				animation: none;
			}
		}
	}

	.pager-count {
		white-space: nowrap;
		padding-right: 20px;
	}
}

@keyframes fadeBackgroundShadow {
	from {
		background-color: transparent;
		color: #000;
	}
	to {
		background-color: #0076ae;
		color: #fff;
	}
}
</style>