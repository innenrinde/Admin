<template>
  <div class="menu-panel">
    <div class="icon-panel">
      <div class="icon"></div>
    </div>
    <div class="menu-content">
      <el-menu class="el-menu-vertical">
        <el-menu-item
          v-for="(item, index) of items"
          :key="item"
          :index="index"
          @click="goToRoute(item.route)"
        >
          <el-icon><component :is="item.icon" /></el-icon>
          <template #title>{{ item.title }}</template>
        </el-menu-item>
      </el-menu>
    </div>
  </div>
</template>

<script>

export default {
  name: "Menu",
  props: {
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
    /**
     * Redirect to url
     * @param {String} route
     */
    goToRoute(route) {
      document.location.href = route;
    },
  }
};
</script>

<style scoped>
.menu-panel {
  display: flex;
  flex-direction: column;
}

.icon-panel {
  background: #fff;
  border-right: 1px solid #cacaca;
  padding: 15px;
  display: flex;
  flex-direction: column;
  align-content: center;
  align-items: center;
  text-align: center;
}

.icon {
  width: 50px;
  height: 50px;
  border-radius: 50px;
  background-color: #418465;
  background-size: 500%;
  background-position: center;
}

.menu-content {
  display: flex;
  height: 100%;
}
</style>
