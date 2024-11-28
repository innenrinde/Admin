<template>
  <el-form :model="form" label-width="auto">

    <el-form-item :label="empty">
      <h1>{{ title }}</h1>
    </el-form-item>

    <el-form-item
      v-for="column in columns.filter(item => !item.isPk)"
      :key="column"
      :label="column.title"
    >
      <el-date-picker v-if="column.type === 'datetime'" type="datetime" v-model="form[column.field]" />
      <el-switch v-else-if="column.type === 'boolean'" v-model="form[column.field]" />
      <el-input v-else v-model="form[column.field]" :placeholder="column.placeholder ?? ''" />
    </el-form-item>

    <el-form-item :label="empty">
      <el-button type="primary" @click="confirmSave">
        Save
      </el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import axios from "axios";
import { HttpRequestService } from "../services/HttpRequestService";
import DateTimeTransformer from "../transformers/DateTimeTransformer";

export default {
  name: "Form",
  props: {
    title: {
      type: String,
      default: () => ""
    },
    columns: {
      type: Array,
      default: () => []
    },
    values: {
      type: Object,
      default: () => {}
    },
    url: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      form: this.values,
      empty: " ",
    };
  },
  methods: {
    /**
     * Perform save data
     */
     confirmSave() {

      let values = { ... this.form };
      this.columns.forEach(column => {
        if (column.type === "datetime") {
          values[column.field] = DateTimeTransformer.reverseTransform(values[column.field]);
        }
      });

      axios
        .put(this.url.put,  values)
        .then(response => {
          HttpRequestService.parseResponse(response, () => {
            console.log(response.data.content);
          });
        });
    },
  }
};
</script>

<style scoped>
</style>
