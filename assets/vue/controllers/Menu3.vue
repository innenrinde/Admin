<template>
	<div class="menu-panel">
		<div class="icon-panel">
			<el-icon><Switch /></el-icon>{{ title }}
		</div>
		<div class="menu-content">
			<div
				v-for="(item, index) of localItems"
				:key="item"
			>
				<div
					class="main-item"
					@click="expandItem(item, index)"
				>
					<div class="icon">
						<font-awesome-icon :icon="['fas', item.show ? 'chevron-down' : 'chevron-right']" />
					</div>
					{{ item.title }}
				</div>
				<div
					v-for="child of item.children"
					:key="child"
					@click="goToRoute(child)"
					class="main-item second-item"
					:class="{ 'active-item': child.active, 'hide-item': !item.show, 'display-item': item.show }"
				>
					<el-icon>
						<component :is="child.icon" />
					</el-icon>
					{{ child.title }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { ElMessageBox } from 'element-plus';

const DEFAULT_OPENEDS_KEY = "defaultOpeneds";

export default {
	name: "Menu3",
	props: {
		title: {
			type: String,
			default: () => ""
		},
		items: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			localItems: [],
			defaultOpeneds: [],
		};
	},
	beforeMount() {
		this.localItems = [ ...this.items ];
		this.defaultOpeneds = this.getFromStorage(DEFAULT_OPENEDS_KEY);

		this.localItems.map((item, index) => {
			if (this.defaultOpeneds.includes(index)) {
				item.show = true;
			}
		});
	},
	methods: {
		/**
		 * Get a value from storage by key
		 * @param {String} key
		 * @returns {Array}
		 */
		getFromStorage(key) {
			if (localStorage) {
				let values = localStorage.getItem(key);
				return values ? values.split("-").map(item => parseInt(item)) : [];
			}
			return [];
		},
		/**
		 * Keep an array value into storage
		 * @param {String} key
		 * @param {Array} values
		 */
		saveIntoStorage(key, values) {
			if (localStorage) {
				localStorage.setItem(key, values.join("-"));
			}
		},
		/**
		 * Redirect to url
		 * @param {Object} menu
		 */
		goToRoute(menu) {
			if (menu.confirm) {
				ElMessageBox.confirm(
					'Are you sure that you want to continue?',
					menu.title,
					{
						confirmButtonText: 'OK',
						cancelButtonText: 'Cancel',
						type: 'info',
					}
				)
					.then(() => {
						document.location.href = menu.route;
					})
					.catch(() => {
					});
			} else {
				document.location.href = menu.route;
			}
		},
		/**
		 * Show/hide children
		 * @param {Object} item
		 * @param index
		 */
		expandItem(item, index) {
			item.show = !item.show;

			if (this.defaultOpeneds.includes(index)) {
				this.defaultOpeneds = this.defaultOpeneds.filter(item => item !== index);
			} else {
				this.defaultOpeneds.push(index);
			}

			this.saveIntoStorage(DEFAULT_OPENEDS_KEY, this.defaultOpeneds);
		}
	}
};
</script>

<style scoped lang="scss">
.menu-panel {
	width: 200px;
	background-color: #02557c;
	color: #fff;
}

.icon-panel {
	font-size: 20px;
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 20px 0;
}

.menu-content {
  display: flex;
  flex-direction: column;
  height: 100%;
	padding: 5px;

	.main-item {
		display: flex;
		flex-direction: row;
		align-items: center;
		padding: 10px;
		background-color: #036593;
		border-radius: 5px;
		cursor: pointer;
		margin-bottom: 1px;
		font-weight: bold;

		&:hover {
			background-color: #0076ae;
		}

		.icon {
			width: 20px;
		}
	}

	.second-item {
		padding-left: 30px;
		font-weight: normal;
	}

	.active-item {
		background-color: #0076ae;
	}

	.hide-item {
		display: none;
	}

	.display-item {
		display: block;
	}
}
</style>
