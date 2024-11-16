<template>
  <div class="menu-panel">
    <div class="icon-panel">
      <el-icon><Switch /></el-icon>{{ title }}
    </div>
    <div class="menu-content">
      <el-menu
        class="el-menu-vertical"
        :default-openeds="defaultOpeneds"
        @open="submenuClick"
        @close="submenuClick"
      >
        <el-sub-menu
          v-for="(item, index) of items"
          :key="item"
          :index="index.toString()"
        >
          <template #title>
            <el-icon>
              <component :is="item.icon" />
            </el-icon>
            {{ item.title }}
          </template>

          <el-menu-item
            v-for="(child, index) of item.children"
            :key="child"
            :class="{ 'menu-active': child.active }"
            @click="goToRoute(child)"
          >
            <el-icon>
              <component :is="child.icon" />
            </el-icon>
            <template #title>{{ child.title }}</template>
          </el-menu-item>
        </el-sub-menu>
      </el-menu>
    </div>
  </div>
</template>

<script>
import { ElMessageBox } from 'element-plus';

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
      defaultOpeneds: [],
    };
  },
  beforeMount() {
    this.defaultOpeneds = this.getFromStorage(DEFAULT_OPENEDS_KEY);
  },
  methods: {
    /**
     * Keep clicked menu into session
     * @param {Number} item
     */
    submenuClick(item) {
      let clicked = item.toString();

      if (this.defaultOpeneds.includes(clicked)) {
        this.defaultOpeneds = this.defaultOpeneds.filter(item => item !== clicked);
      } else {
        this.defaultOpeneds.push(clicked);
      }

      this.saveIntoStorage(DEFAULT_OPENEDS_KEY, this.defaultOpeneds);
    },
    /**
     * Get a value from storage by key
     * @param {String} key
     * @returns {Array}
     */
    getFromStorage(key) {
      if (localStorage) {
        let values = localStorage.getItem(key);
        return values ? values.split(",") : [];
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
        localStorage.setItem(key, values.join(","));
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
  }
};
</script>

<style scoped>
.menu-active {
  background-color: #ecf5ff;
}
</style>
