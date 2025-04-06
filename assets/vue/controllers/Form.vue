<template>
  <el-form :model="form" label-width="auto">

    <el-form-item v-if="title" :label="empty">
      <h1>{{ title }}</h1>
    </el-form-item>

    <el-form-item
      v-for="column in columns.filter(item => !item.isPk)"
      :key="column"
      :label="column.title"
    >
      <el-date-picker v-if="column.type === 'datetime'" type="datetime" v-model="form[column.field]" />
      <el-switch v-else-if="column.type === 'checkbox'" v-model="form[column.field]" />
      <el-select
          v-else-if="column.type === 'choice'"
          v-model="form[column.field]"
          placeholder="- select an option -"
          size="large"
      >
        <el-option
          v-for="option in column.options"
          :key="option"
          :label="option.label"
          :value="option.value"
        />
      </el-select>
      <el-input v-else v-model="form[column.field]" :placeholder="column.placeholder ?? ''" />
    </el-form-item>

    <el-form-item :label="empty">
      <el-button
	      v-if="hasSaveButton"
        type="primary"
        @click="confirmSave"
      >
        Save
      </el-button>
      <el-button
        v-if="hasCloseButton"
        type="secondary"
        @click="confirmClose"
      >
        Close
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
      default: () => { return {}; }
    },
    hasCloseButton: {
      type: Boolean,
      default: () => false
    },
	  hasSaveButton: {
		  type: Boolean,
		  default: () => false
	  }
  },
  data() {
    return {
      form: this.values,
      empty: " ",
    };
  },
  methods: {
		getValues() {
			let values = { ... this.form };
			this.columns.forEach(column => {
				if (column.type === "datetime") {
					values[column.field] = DateTimeTransformer.reverseTransform(values[column.field]);
				}
			});

			return values;
		},
    /**
     * Perform save data
     */
     confirmSave() {

      let values = this.getValues();

      if (this.url.put) {
        axios
          .put(this.url.put, values)
          .then(response => {
            HttpRequestService.parseResponse(response, () => {
              console.log(response.data.content);
            });
          });
      } else {
        this.$emit("save", values);
      }
    },
    /**
     * Close action
     */
    confirmClose() {
       this.$emit("close");
    }
  }
};
</script>

<style scoped>
</style>
