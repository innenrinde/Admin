<template>
	<div class="content">
		<div
			v-if="menus.length"
			class="content-menu"
		>
			<c-menu
				:title="title"
				:items="menus"
				@select-menu="selectMenu"
			/>
		</div>
		<div class="content-body">
			<component
				:is="currentComponent"
				v-bind="currentComponentBindData"
			/>
		</div>
	</div>
</template>

<script setup>
import CMenu from "./Menu.vue";
import axios from "axios";
import { defineProps, onMounted, reactive, toRefs, ref, defineAsyncComponent } from "vue";
import { NotificationService } from "../services/NotificationService";

const props = defineProps({
	settingsUrl: String,
});

const { settingsUrl } = toRefs(props);

let title = reactive("");
let menus = reactive([]);
let currentComponent = ref(null);
let currentComponentBindData = ref({});

/**
 * Retrieve app settings
 */
onMounted(() => {
	axios
		.get(settingsUrl.value, {})
		.then(response => {

			// top title for application
			title = response.data.title;

			// list of menus
			menus.splice(0);
			response.data.menus.forEach(row => menus.push(row));

		});
});

const selectMenu = (menu) => {
	if (menu.confirm) {
		NotificationService.confirm({
			title: menu.title,
			message: "Are you sure that you want to continue?",
			okAction: () => {
				document.location.href = menu.route;
			}
		});
	} else {
		currentComponentBindData.value = menu;
		currentComponent.value = defineAsyncComponent(() =>
			import(`./${menu.component}.vue`)
		)
	}
}

</script>

<style scoped lang="scss">
.content {
	background-color: #e1e1e1;
	overflow: hidden;
	height: 100%;
	display: flex;
	flex-direction: row;

	.content-menu {
		height: 100%;
		display: flex;
	}

	.content-body {
		width: 100%;
		display: flex;
		flex-direction: column;
		margin: 12px;
		padding: 10px;
		border-radius: 5px;
		background-color: var(--primary-background-color);
		border: solid 1px var(--box-shadow-color);
		box-shadow: 1px 1px 4px var(--box-shadow-color);
		overflow: auto;
	}
}


/* START left menu */

/* END left menu */

/* START body */


//.table {
//	width: 100%;
//}

.form-container,
.table-container {
	//width: 100%;
	//display: flex;
	//margin: 12px;
	//border-radius: 5px;
	//background-color: var(--primary-background-color);
	//border: solid 1px var(--box-shadow-color);
	//box-shadow: 1px 1px 4px var(--box-shadow-color);
	//overflow: auto;
	//flex-direction: column;
}

.form-container {
	//padding: 20px;
}

.table-container {
	//padding: 0;
}
</style>