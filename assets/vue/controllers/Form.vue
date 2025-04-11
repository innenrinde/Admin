<template>

	<div>
		<div v-if="title">
			<h1>{{ title }}</h1>
		</div>

		<div
			v-for="column in columns.filter(item => !item.isPk)"
		  :key="column"
    >
			<div>{{ column.title }}</div>
			<div>
				<x-select
					v-if="column.type === 'choice'"
					v-model="form[column.field]"
					:options="column.options"
				/>
				<x-checkbox
					v-else-if="column.type === 'checkbox'"
					v-model="form[column.field]"
				/>
				<x-date
					v-else-if="column.type === 'datetime'"
					v-model="form[column.field]"
				/>
				<x-password
					v-else-if="column.type === 'password'"
					v-model="form[column.field]"
					:placeholder="column.placeholder ?? ''"
				/>
				<x-input
					v-else
					v-model="form[column.field]"
					:placeholder="column.placeholder ?? ''"
				/>
			</div>
		</div>

		<x-button
			v-if="hasCloseButton"
			type="secondary"
			title="Close"
			@click="confirmClose"
		/>
		<x-button
			v-if="hasSaveButton"
			type="primary"
			title="Save"
			@click="confirmSave"
		/>

	</div>

</template>

<script>
import axios from "axios";
import { HttpRequestService } from "../services/HttpRequestService";
import DateTimeTransformer from "../transformers/DateTimeTransformer";
import XButton from "./components/XButton.vue";
import XInput from "./components/XInput.vue";
import XPassword from "./components/XPassword.vue";
import XSelect from "./components/XSelect.vue";
import XCheckbox from "./components/XCheckbox.vue";
import XDate from "./components/XDate.vue";

export default {
  name: "Form",
	components: { XInput, XPassword, XSelect, XCheckbox, XDate, XButton },
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
		  default: () => true
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
