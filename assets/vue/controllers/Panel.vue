<template>
	<div class="panel-content">
		<div class="shadow"
			@click="close"
		/>
		<div class="panel">
			<slot name="content" />
		</div>
	</div>
</template>

<script>
export default {
	name: "Panel",
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
		};
	},
	methods: {
		close() {
			this.$emit("close");
		}
	}
};
</script>

<style scoped lang="scss">
.panel-content {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	z-index: 9999;
	-webkit-backdrop-filter: blur(3px);
	backdrop-filter: blur(3px);

	.shadow {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: #fff;
		opacity: 60%;
	}

	.panel {
		position: fixed;
		top: 150px;
		left: 50%;
		transform: translate(-50%, -50%);
		width: 50%;
		height: 200px;
		min-width: 200px;
		max-width: 600px;
		display: flex;
		flex-direction: column;
		padding: 20px;
		background-color: #fff;
		border: solid 1px #fff;
		box-shadow: 1px 1px 50px #d3d3d3;
		border-radius: 10px;
		text-align: center;
	}
}
</style>