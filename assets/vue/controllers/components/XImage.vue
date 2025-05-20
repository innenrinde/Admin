<template>

	<div
		class="thumb"
		@click="openImage"
	>
		<img
			v-if="srcLocal"
			width="50"
			alt=""
			:src="srcLocal"
		>
		<font-awesome-icon
			v-else
			class="no-image"
			:icon="['far', 'image']"
		/>
	</div>

	<div
		class="panel-content-img"
		v-if="showImage"
	>
		<div class="shadow-img"
			 @click="closeAction"
		/>
		<div
			class="panel-img"
		>
			<div class="slot-content-img">
				<img
					style="max-height: 100%; max-width: 100%"
					:src="srcLocal"
					:alt="alt"
					loading="lazy"
				/>
			</div>
			<div class="actions-img">
				<x-button
					title="Close"
					type="secondary"
					@click="closeAction"
				/>
			</div>
		</div>
	</div>

</template>

<script setup>
import { defineProps, ref, toRefs } from "vue";
import XButton from "./XButton.vue";

const props = defineProps({
	src: String,
	alt: String,
});

const { src, alt } = toRefs(props);

let showImage = ref(false);
let srcLocal = ref(null);

const openImage = () => {
	showImage.value = true;
	srcLocal.value = src.value;
};

const closeAction = () => {
	showImage.value = false;
};

</script>

<style scoped lang="scss">
.thumb {
	width: 30px;
	height: 30px;
	background-color: #dadada;
	border: solid 1px #cacaca;
	cursor: pointer;
	overflow: hidden;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;

	.no-image {
		font-size: 20px;
		color: #cacaca;
	}
}

img {
	cursor: pointer;
}

.panel-content-img {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	z-index: 9999;
	-webkit-backdrop-filter: blur(2px);
	backdrop-filter: blur(2px);

	.shadow-img {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: #c8c8c8;
		opacity: 60%;
		animation: fadeBackgroundShadow 0.5s;
		animation-fill-mode: forwards;
	}

	.panel-img {
		position: fixed;
		top: 0;
		margin: 20px;
		width: calc(100% - 65px);
		height: calc(100% - 65px);
		min-width: 200px;
		display: flex;
		flex-direction: column;
		padding: 10px;
		background-color: #fff;
		box-shadow: 1px 1px 50px #d3d3d3;
		border-radius: 10px;
		text-align: center;
		animation: panelAnimation 0.3s;

		.slot-content-img {
			overflow: auto;
			height: 100%;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.actions-img {
			margin-top: 10px;
			padding-top: 10px;
		}
	}
}

@keyframes fadeBackgroundShadow {
	from {
		background-color: transparent;
	}
	to {
		background-color: #c8c8c8;
	}
}

@keyframes panelAnimation {
	from {
		top: -100%;
	}
	to {
		top: 0;
	}
}
</style>