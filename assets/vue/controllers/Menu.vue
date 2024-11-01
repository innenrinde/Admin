<template>
  <div class="menu-panel">
    <div class="icon-panel">
      <el-icon><Switch /></el-icon>Stocks
    </div>
    <div class="menu-content">
      <el-menu class="el-menu-vertical">
        <el-menu-item
          v-for="(item, index) of items"
          :key="item"
          @click="goToRoute(item)"
          :class="{ 'menu-active': item.active }"
        >
          <el-icon>
            <component :is="item.icon" />
          </el-icon>
          <template #title>{{ item.title }}</template>
        </el-menu-item>
      </el-menu>
    </div>
  </div>
</template>

<script>
import { ElMessageBox } from 'element-plus';

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
  }
};
</script>

<style scoped>
.menu-active {
  background-color: #ecf5ff;
}
</style>
