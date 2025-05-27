<template>
	<div
		class="table-section"
	>
		<div class="table">
			<div
				class="header"
			>
				<div
					v-for="column in visibleColumns"
					:key="column"
					:class="{ [column.width]: column.width, 'align-center': column.type === 'checkbox'}"
				>
					{{ column.title }}
				</div>
				<div class="w1">
					<!-- Operations-->
				</div>
				<div class="w1 align-center">
					#
				</div>
			</div>

			<div
				v-for="(row, index) in rows"
				:key="row"
				class="content"
				:data-index="index"
			>
				<div
					v-for="column in visibleColumns"
					:key="column"
				>
					<span
						v-if="column.type === 'checkbox'"
						class="align-center"
					>
						<font-awesome-icon
							v-if="row[column.field]"
							:icon="['fas', 'circle-check']"
							class="icon-check"
						/>
						<span v-else>-</span>
					</span>
					<span v-else-if="column.type === 'datetime'">
						{{ dateFormat(row[column.field]) }}
					</span>
					<span v-else-if="column.type === 'choice'">
						{{ row[column.field].label }}
					</span>
					<span v-else-if="column.type === 'file' && row[column.field]">
						<x-image
							:src="row[column.field]"
						/>
					</span>
					<span v-else>{{ row[column.field] }}</span>
				</div>

				<div class="actions">
					<span
						@click="deleteRow(row)"
						title="Delete"
						class="red"
					>
						<font-awesome-icon :icon="['fas', 'xmark']" />
					</span>
					<span
						@click="editRow(row)"
						title="Edit"
						class="blue"
					>
						<font-awesome-icon :icon="['far', 'pen-to-square']" />
					</span>
				</div>

				<div class="align-center">
					{{ index + localPager.page * localPager.limit + 1 }}
				</div>

			</div> <!--END row-->

		</div> <!--END table-->

	</div>

	<div
		class="pager"
	>
		<x-pager
			:total="pager.total"
			:page="pager.page"
			:limit="pager.limit"
			@page="changePage"
		/>
	</div>

</template>

<script setup>
import { defineProps, toRefs, defineEmits, defineAsyncComponent } from "vue";
import DateTimeTransformer from "../transformers/DateTimeTransformer";
import XPager from "./XPager.vue";

const XImage = defineAsyncComponent(
	() => import("./XImage.vue")
);

const emit = defineEmits(["edit", "delete", "changePage"]);

const props = defineProps({
	columns: Array,
	rows: Array,
	pager: Object,
});

const { columns, rows, pager } = toRefs(props);

// extract only visible columns for table header
const visibleColumns = columns.value.filter(column => !column.hidden);

// local data pages to calculate row's index
let localPager = pager;

/**
 * Emit deleting row
 * @param row
 */
const deleteRow = (row) => {
	emit("delete", row);
};

/**
 * Emit editing row
 * @param row
 */
const editRow = (row) => {
	emit("edit", row);
}

/**
 * Emit pager options
 * @param pager
 */
const changePage = (pager) => {
	localPager = pager;
	emit("changePage", pager);
}

/**
 * Custom date time format
 * @param {String} value
 * @returns {String}
 */
const dateFormat = (value) => {
	return value ? DateTimeTransformer.transform(value) : "-";
};

</script>

<style scoped lang="scss">
.table-section {
	display: flex;
	flex-direction: column;
	overflow: auto;
	border: solid 1px var(--table-border-color);

	.table {
		display: table;
		text-align: left;

		.header {
			display: table-row;
			position: sticky;
			top: 0;
			z-index: 1;

			> div {
				display: table-cell;
				background-color: var(--table-header-color);
				border-bottom: solid 1px var(--table-border-color);
				font-weight: 600;
				color: var(--secondary-color);
				padding: 7px;
				white-space: nowrap;
			}

			.w1 {
				width: 1%;
			}

			.w10 {
				width: 10%;
			}

			.w20 {
				width: 20%;
			}

			.w30 {
				width: 30%;
			}

			.w100 {
				width: 100%;
			}

			.wp80 {
				width: 10px;
			}
		}

		.align-center {
			display: block;
			text-align: center;
		}

		.content {
			display: table-row;

			&:nth-child(odd) > div {
				background-color: var(--table-alt-line-color);
			}

			&:hover > div {
				background-color: var(--table-hover-line-color);
				color: var(--primary-text-color);
			}

			> div {
				background-color: var(--primary-background-color);
				display: table-cell;
				vertical-align: middle;
				padding: 7px;
			}

			:deep(.icon-check) {
				font-size: 16px;
				color: var(--green-button-color);
			}

			.actions {
				text-wrap : nowrap;

				span {
					display: inline-block;
					cursor: pointer;
					border-radius: 25px;
					width: 25px;
					height: 25px;
					font-size: 14px;
					color: var(--primary-text-color);
					text-align: center;
					align-items: center;
					align-content: center;
					margin: 0 2px;
				}

				span.red {
					border: solid 1px var(--red-button-border-color);
					background-color: var(--red-button-color);
				}

				span.red:hover {
					background-color: var(--red-button-border-color);
				}

				span.blue {
					border: solid 1px var(--primary-button-color);
					background-color: var(--primary-button-hover-color);
				}

				span.blue:hover {
					background-color: var(--primary-button-color);
				}
			}
		}
	}
}

.pager {
	border: solid 1px var(--table-border-color);
	border-top: none;
	background-color: var(--table-header-color);
}
</style>