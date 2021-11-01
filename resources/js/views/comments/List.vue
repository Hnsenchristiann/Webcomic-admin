<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
    </div>

    <el-table v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column min-width="180px" label="Commenter">
        <template slot-scope="{row}">
          <span>{{ row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Comment">
        <template slot-scope="scope">
          <!-- <span v-html="scope.row.comment" /> -->
          <span class="link-type" @click="openComment(scope.row.id)">{{ scope.row.comment | truncate(50, '...') }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Commented On">
        <template slot-scope="scope">
          <span>{{ scope.row.commentable_type | parseCommentableObject }} ID {{ scope.row.commentable_id }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Children">
        <template slot-scope="scope">
          <span>{{ scope.row.children }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Created At">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at | dateFormatter }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="200">
        <template slot-scope="scope">
          <el-button type="primary" size="small" icon="el-icon-delete" @click="deleteItem(scope.row.id)">
            Delete
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :visible.sync="dialogVisible" title="Comment">
      <comments
        :comment="selectedComment"
        @closeDialogThenUpdate="closeDialogThenUpdate"
      />
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import Comments from './components/Comments';
const commentResource = new Resource('comments');

export default {
  name: 'CommentList',
  components: { Pagination, Comments },
  filters: {
    dateFormatter(date) {
      return (new Date(date)).toLocaleDateString('id-ID');
    },
    truncate(text, length, clamp){
      clamp = clamp || '...';
      var node = document.createElement('div');
      node.innerHTML = text;
      var content = node.textContent;
      return content.length > length ? content.slice(0, length) + clamp : content;
    },
    parseCommentableObject(objClass){
      return objClass.split('\\')[2];
    },
  },
  data() {
    return {
      selectedComment: null,
      dialogVisible: false,
      query: {
        keyword: '',
      },
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        paginate: 20,
        // comment_parentless: 1,
        comment_with_children: 1,
        with: ['commentable', 'commenter'],
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    closeDialogThenUpdate(){
      this.dialogVisible = false;
      this.getList();
    },
    async openComment(id){
      const { data } = await commentResource.get(id);
      this.selectedComment = data;
      this.dialogVisible = true;

      // commentResource.get(id)
      //   .then(({ data }) => {
      //     this.selectedComment = data;
      //     console.log(this.selectedComment);
      //     this.dialogVisible = true;
      //     console.log('asdasdf');
      //   });
    },
    deleteItem(id){
      commentResource.destroy(id)
        .then((response) => {
          this.getList();
        });
    },
    handleFilter(){
      this.listQuery['search'] = this.query.keyword;
      this.getList();
    },
    async getList() {
      this.listLoading = true;
      const { data } = await commentResource.list(this.listQuery);
      this.list = data.items;
      this.list.forEach((el, key) => {
        this.list[key].name = el.commenter.name;
        this.list[key].children = el.all_children_with_commenter.length;
      });
      this.total = data.total;
      this.listLoading = false;
    },
  },
};
</script>

<style scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
</style>
