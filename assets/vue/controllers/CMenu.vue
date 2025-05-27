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
					:class="{ 'level1-item-title-active': item.show || isActiveSubMenu(item) }"
					@click="expandItem(item, index)"
				>
					<div class="icon">
						<font-awesome-icon :icon="['fas', item.show ? 'chevron-down' : 'chevron-right']" />
					</div>
					{{ item.title }}
				</div>

				<div
					class="children"
					:class="{ 'hide-item': !item.show, 'display-item': item.show || isActiveSubMenu(item) }"
				>
					<div
						v-for="child of item.children"
						:key="child"
						@click="selectMenu(child)"
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

<script setup>
import { defineProps, toRefs, onBeforeMount, defineEmits, reactive } from "vue";

const DEFAULT_OPENEDS_KEY = "defaultOpeneds";

const emit = defineEmits(["selectMenu"]);

const props = defineProps({
	title: String,
	items: Array,
});

const { title, items } = toRefs(props);

/**
 * List of all menus (hierarchically)
 * @type {Reactive<*[]>}
 */
let localItems = reactive([]);

/**
 * Keep expanded menus to be shown after refresh
 * @type {Array}
 */
let defaultOpeneds = [];

/**
 * Init items menu opened by default
 */
onBeforeMount(() => {
	localItems = [ ...items.value ];

	// opened menu by default
	defaultOpeneds = getFromStorage(DEFAULT_OPENEDS_KEY);
	localItems.map((item, index) => {
		if (defaultOpeneds.includes(index)) {
			item.show = true;
		}
	});

	// load first active menu
	menus: for(let menu of localItems) {
		for(let submenu of menu.children) {
			if (submenu.active) {
				emit("selectMenu", submenu);
				break menus;
			}
		}
	}
});

/**
 * Get a value from storage by key
 * @param {String} key
 * @returns {Array}
 */
const getFromStorage = (key) => {
	if (localStorage) {
		let values = localStorage.getItem(key);
		return values ? values.split("-").map(item => parseInt(item)) : [];
	}
	return [];
};

/**
 * Keep an array value into storage
 * @param {String} key
 * @param {Array} values
 */
const saveIntoStorage = (key, values) => {
	if (localStorage) {
		localStorage.setItem(key, values.join("-"));
	}
};

/**
 * Emit selected menu to be managed into parent component
 * @param {Object} submenu
 */
const selectMenu = (submenu)=> {

	localItems.forEach(menu => {
		menu.children.forEach(submenu => {
			submenu.active = false;
		});
	});

	submenu.active = true;

	emit("selectMenu", submenu);
};

/**
 * Show/hide children
 * @param {Object} item
 * @param index
 */
const expandItem = (item, index) => {
	item.show = isActiveSubMenu(item) ? true : !item.show;

	if (defaultOpeneds.includes(index)) {
		defaultOpeneds = defaultOpeneds.filter(item => item !== index);
	} else {
		defaultOpeneds.push(index);
	}

	saveIntoStorage(DEFAULT_OPENEDS_KEY, defaultOpeneds);
};

/**
 * Check if a main menu (level 1) can be displayed (it has at least one child active)
 * @param {Object} item
 * @return {Boolean}
 */
const isActiveSubMenu = (item) => {
	return item.children.find(child => child.active);
};
</script>

<style scoped lang="scss">
.menu-panel {
	width: 200px;
	height: 100%;
	background-color: var(--primary-color);
	color: var(--primary-text-color);
	display: flex;
	flex-direction: column;

	::-webkit-scrollbar {
		width: 7px;
	}

	::-webkit-scrollbar-track {
		box-shadow: inset 0 0 5px var(--scrollbar-color);
		border-radius: 5px;
	}

	::-webkit-scrollbar-thumb {
		background: var(--primary-button-color);
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
		background-color: var(--secondary-color);
		border-radius: 5px;
		cursor: pointer;
		margin-top: 1px;
		font-weight: 600;

		&:hover {
			background-color: var(--primary-button-color);
		}

		.icon {
			width: 20px;
		}
	}

	.level1-item-title-active {
		background-color: var(--primary-button-color);
		border-radius: 5px 5px 0 0;
	}

	.children {
		background-color: var(--primary-button-color);
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
				background-color: var(--secondary-color);
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
			background-color: var(--secondary-color);

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
