<template>
	<div class="table-section">
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
			</div>

			<div
				v-for="row in rows"
				:key="row"
				class="content"
			>
				<div
					v-for="column in visibleColumns"
					:key="column"
				>
					<span
						v-if="column.type === 'checkbox'"
						class="align-center"
					>
						<font-awesome-icon :icon="['far', 'circle-check']" v-if="row[column.field]" />
						<span v-else>-</span>
					</span>
					<span v-else-if="column.type === 'datetime'">
						{{ dateFormat(row[column.field]) }}
					</span>
					<span v-else-if="column.type === 'choice'">
						{{ row[column.field].label }}
					</span>
					<span v-else-if="column.type === 'file'">
							<span v-if="row[column.field]">[img]</span>
						<!--		          <img-->
						<!--								v-if="row[column.field]"-->
						<!--								:src="row[column.field]"-->
						<!--								width="50"-->
						<!--							/>-->
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

			</div> <!--END row-->

		</div> <!--END table-->
	</div>
</template>

<script setup>
import { defineProps, toRefs, defineEmits } from "vue";
import DateTimeTransformer from "../../transformers/DateTimeTransformer";

const emit = defineEmits(["edit", "delete"]);

const props = defineProps({
	columns: Array,
	rows: Array,
});

const { columns, url } = toRefs(props);

const visibleColumns = columns.value.filter(column => !column.hidden);

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
	overflow: auto;
	margin: 5px 10px 10px 10px;
	border: solid 1px #dadada;

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
				background-color: #f3f4f6;
				border-bottom: solid 1px #dadada;
				font-weight: 600;
				color: #036593;
				padding: 7px;
			}

			.w1 {
				width: 1%;
			}

			.w10 {
				width: 10%;
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
				background-color: #f0fdf4;
			}

			&:hover > div {
				background-color: #4bb0df;
				color: #fff;
			}

			> div {
				background-color: #fff;
				display: table-cell;
				vertical-align: middle;
				padding: 7px;
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
					color: #fff;
					text-align: center;
					align-items: center;
					align-content: center;
					margin: 0 2px;
				}

				span.red {
					border: solid 1px #f35959;
					background-color: #ffa1a1;
				}

				span.blue {
					border: solid 1px #7f7fe8;
					background-color: #b6b6e4;
				}
			}
		}
	}
}
</style>