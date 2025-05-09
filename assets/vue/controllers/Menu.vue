<template>
	<div class="menu-panel">
		<div class="icon-panel">
			<font-awesome-icon :icon="['fas', 'repeat']" />
			{{ title }}
		</div>
		<div class="menu-content">
			<div
				v-for="(item, index) of localItems"
				:key="item"
			>
				<div
					class="level1-item-title"
					:class="{ 'level1-item-title-active': item.show }"
					@click="expandItem(item, index)"
				>
					<div class="icon">
						<font-awesome-icon :icon="['fas', item.show ? 'chevron-down' : 'chevron-right']" />
					</div>
					{{ item.title }}
				</div>

				<div
					class="children"
					:class="{ 'hide-item': !item.show, 'display-item': item.show }"
				>
					<div
						v-for="child of item.children"
						:key="child"
						@click="goToRoute(child)"
						class="level2-item-title"
						:class="{ 'active-item': child.active }"
					>
						<div class="icon">
							<font-awesome-icon :icon="['fas', child.icon]" />
						</div>
						<div class="label">
							{{ child.title }}
						</div>
						<div class="icon-right">
							<font-awesome-icon :icon="['fas', 'chevron-right']" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { NotificationService } from "../services/NotificationService";

const DEFAULT_OPENEDS_KEY = "defaultOpeneds";

export default {
	name: "Menu",
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
				NotificationService.confirm({
					title: menu.title,
					message: "Are you sure that you want to continue?",
					okAction: () => {
						document.location.href = menu.route;
					}
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
	height: 100%;
	background-color: #02557c;
	color: #fff;
	display: flex;
	flex-direction: column;

	::-webkit-scrollbar {
		width: 7px;
	}

	::-webkit-scrollbar-track {
		box-shadow: inset 0 0 5px grey;
		border-radius: 5px;
	}

	::-webkit-scrollbar-thumb {
		background: #0076ae;
		border-radius: 5px;
	}
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
	overflow-y: auto;

	.level1-item-title {
		display: flex;
		flex-direction: row;
		align-items: center;
		padding: 10px;
		background-color: #036593;
		border-radius: 5px;
		cursor: pointer;
		margin-top: 1px;
		font-weight: 600;

		&:hover {
			background-color: #0076ae;
		}

		.icon {
			width: 20px;
		}
	}

	.level1-item-title-active {
		background-color: #0076ae;
		border-radius: 5px 5px 0 0;
	}

	.children {
		background-color: #0076ae;
		border-radius: 0 0 5px 5px;
		padding: 5px;
		margin-bottom: 10px;

		.level2-item-title {
			display: flex;
			flex-direction: row;
			align-items: center;
			padding: 10px 10px 10px 25px;
			margin-bottom: 1px;
			border-radius: 5px;
			cursor: pointer;

			&:hover {
				background-color: #036593;
			}

			.icon {
				width: 20px;
				align-content: center;
				text-align: center;
			}

			.label {
				width: 100%;
			}

			.icon-right {
				display: none;
			}
		}

		.active-item {
			background-color: #036593;

			.icon-right {
				display: inherit;
			}
		}
	}

	.hide-item {
		display: none;
	}

	.display-item {
		display: block;
	}
}
</style>
