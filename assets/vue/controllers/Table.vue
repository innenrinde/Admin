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
          v-if="column.type === 'boolean'"
          :prop="column.field"
          :label="column.title"
          prop="tag"
          width="120"
          sortable
          align="center"
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
          v-else
          :prop="column.field"
          :label="column.title"
          sortable
        />
      </template>
      <el-table-column fixed="right" label="Operations" width="120">
        <template #default="scope">
          <el-button link type="primary" size="small" @click="deleteRow(scope.row)">
            Remove
          </el-button>
          <el-button link type="primary" size="small" @click="editRow(scope.row)">
            Edit
          </el-button>
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
        <el-date-picker v-if="column.type === 'date'" v-model="form[column.field]" />
        <el-switch v-else-if="column.type === 'boolean'" v-model="form[column.field]" />
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
import { ElMessage } from 'element-plus';

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
          if (!response.data.rows) {
            throw new Error(`${this.url.list} response => .data.rows not found`);
          }
          this.tableData = response.data.rows;
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
          this.processDeletedRow(response.data);
          this.deleteDialogVisible = false;
        });
    },
    /**
     * Perform edit
     */
    confirmEditRow() {
      axios
        .post(this.url.post,  this.form)
        .then(response => {
          this.processEditedRow(response.data);
          this.editDialogVisible = false;
          this.showMessage("Row successfully edited");
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
     * Display custom message
     * @param {String} message
     */
    showMessage(message) {
      ElMessage({
        message,
        type: 'success',
        plain: true,
      })
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
