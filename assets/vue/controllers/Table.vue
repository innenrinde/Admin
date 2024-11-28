<template>
  <div
    class="table-list"
    ref="tableList"
  >
    <el-table :data="tableData" :height="height">
      <template
        v-for="column in columns"
        :key="column"
      >
        <el-table-column
          v-if="column.type === 'boolean' && !column.hidden"
          :prop="column.field"
          :label="column.title"
          prop="tag"
          width="130"
          sortable
          align="center"
          :filters="[
            { text: 'Yes', value: true },
            { text: 'No', value: false }
          ]"
          :filter-method="filterHandler"
        >
          <template #default="scope">
            <el-tag
              :type="scope.row[column.field] ? 'primary' : 'error'"
              disable-transitions
            >{{ scope.row[column.field] ? 'Yes' : 'No' }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          v-else-if="column.type === 'number'"
          :prop="column.field"
          :label="column.title"
          width="80"
          sortable
        />
        <el-table-column
          v-else-if="column.type === 'datetime'"
          :prop="column.field"
          :label="column.title"
          width="150"
          sortable
        >
          <template #default="scope">
            {{ dateFormat(scope.row[column.field]) }}
          </template>
        </el-table-column>
        <el-table-column
          v-else-if="!column.hidden"
          :prop="column.field"
          :label="column.title"
          :width="column.width ?? 0"
          sortable
        />
      </template>
      <el-table-column
        fixed="right"
        label="Operations"
        width="120"
        align="center"
      >
        <template #default="scope">
          <el-button type="danger" :icon="Delete" plain circle size="small" @click="deleteRow(scope.row)" />
          <el-button type="primary" :icon="Edit" plain circle size="small" @click="editRow(scope.row)" />
        </template>
      </el-table-column>
    </el-table>
  </div>

  <el-dialog
    v-model="deleteDialogVisible"
    title="Delete row"
    width="500"
    center
  >
    <span class="dialog-message">Do you want to delete the row #{{ this.selectedRow[this.pk().field] }} ?</span>
    <template #footer>
      <div class="dialog-footer">
        <el-button @click="deleteDialogVisible = false">No</el-button>
        <el-button type="primary" @click="confirmDeleteRow">
          Yes
        </el-button>
      </div>
    </template>
  </el-dialog>

  <el-dialog
    v-model="editDialogVisible"
    title="Edit row"
    width="500"
    center
  >
    <el-form :model="form" label-width="auto" style="max-width: 600px">
      <el-form-item
        v-for="column in columns.filter(item => !item.isPk)"
        :key="column"
        :label="column.title"
      >
        <el-date-picker v-if="column.type === 'datetime'" type="datetime" v-model="form[column.field]" />
        <el-switch v-else-if="column.type === 'boolean'" v-model="form[column.field]" />
        <el-select
          v-else-if="column.type === 'select'"
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
        <el-input v-else v-model="form[column.field]" />
      </el-form-item>
    </el-form>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="editDialogVisible = false">Close</el-button>
        <el-button type="primary" @click="confirmEditRow">
          Save
        </el-button>
      </div>
    </template>
  </el-dialog>

</template>

<script>
import axios from "axios";
import { ElNotification } from 'element-plus';
import { Delete, Edit } from '@element-plus/icons-vue';
import { HttpRequestService } from "../services/HttpRequestService";

export default {
  name: "Table",
  props: {
    columns: {
     type: Array,
     default: () => []
    },
    url: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      tableData: [],
      height: 0,
      deleteDialogVisible: false,
      editDialogVisible: false,
      selectedRow: {},
      form: {},
      Delete,
      Edit,
    };
  },
  mounted() {
    this.height = this.$refs.tableList.clientHeight;
    this.getTableDataList();
  },
  methods: {
    /**
     * Retrieve rows
     */
    getTableDataList() {
      axios
        .get(this.url.get)
        .then(response => {
          if (!response.data.content) {
            throw new Error(`${this.url.list} response => .data.content not found`);
          }
          this.tableData = response.data.content;
        });
    },
    /**
     * Show edit/create dialog
     * @param row
     */
    editRow(row) {
      this.form = { ...row };
      this.editDialogVisible = true;
    },
    /**
     * Show delete dialog
     * @param row
     */
    deleteRow(row) {
      this.selectedRow = row;
      this.deleteDialogVisible = true;
    },
    /**
     * Perform deletion
     */
    confirmDeleteRow() {
      axios
        .delete(this.url.delete, {
          headers: {
            Authorization: ""
          },
          data: this.selectedRow[this.pk().field]
        })
        .then(response => {
          HttpRequestService.parseResponse(response, () => {
            this.processDeletedRow(response.data);
          });
          this.deleteDialogVisible = false;
        });
    },
    /**
     * Perform edit
     */
    confirmEditRow() {

      let values = { ... this.form };
      this.columns.forEach(column => {
        if (column.type === "datetime") {
          values[column.field] = this.dateFormatWS(values[column.field]);
        }
      });

      axios
        .post(this.url.post,  values)
        .then(response => {
          HttpRequestService.parseResponse(response, () => {
            this.processEditedRow(response.data.content);
            this.editDialogVisible = false;
          });
        });
    },
    /**
     * Delete row from local list
     * @param {Object} data
     */
    processDeletedRow(data) {
      let pk = this.pk();

      if (!data[pk.field]) {
        throw new Error("Can't find a PK field to be deleted!");
      }

      this.tableData = this.tableData.filter(row => row[pk.field] !== data[pk.field]);
    },
    /**
     * Delete row from local list
     * @param {Object} data
     */
    processEditedRow(data) {
      let pk = this.pk();

      if (!data[pk.field]) {
        throw new Error("Can't find a PK field to be updated!");
      }

      let row = this.tableData.find(row => row[pk.field] === data[pk.field]);

      this.columns.forEach(column => {
        if (data[column.field]) {
          row[column.field] = data[column.field];
        }
      });
    },
    /**
     * Try to find the primary column for table
     * @returns {Object}
     */
    pk() {
      let pk = this.columns.find(column => column.isPk);

      if (!pk) {
        throw new Error("Can't find a PK column!");
      }

      return pk;
    },
    /**
     * Display a success message
     * @param {String} message
     */
    okMessage(message) {
      ElNotification({ title: "Success", message, type: "success" })
    },
    /**
     * Display an error message
     * @param {String} message
     */
    errorMessage(message) {
      ElNotification({ title: "Error", message, type: "error" })
    },
    /**
     * Custom date time format
     * @param {String} value
     * @returns {String}
     */
    dateFormat(value) {
      return value ? this.$moment(value).format("DD/MM/YYYY HH:mm:ss") : "-";
    },
    /**
     * Send date to ws
     * @param {Date} value
     * @returns {String}
     */
    dateFormatWS(value) {
      return value ? this.$moment(value).format("YYYY-MM-DD HH:mm:ss") : "";
    },
    /**
     * Local filtration by bool column
     * @param value
     * @param row
     * @param column
     * @returns {boolean}
     */
    filterHandler(value, row, column) {
      const property = column['property']
      return row[property] === value;
    }
  }
};
</script>

<style scoped>
.table-list {
  width: 100%;
}

.dialog-message {
  display: block;
  text-align: center;
}
</style>
