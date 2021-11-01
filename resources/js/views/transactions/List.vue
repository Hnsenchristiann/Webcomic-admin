<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-plus" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
    </div>
    <el-table v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <!-- <el-table-column width="180px" align="center" label="Date">
        <template slot-scope="scope">
          <span>{{ scope.row.timestamp | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column> -->

      <el-table-column min-width="180px" label="Title">
        <template slot-scope="{row}">
          <router-link :to="'/comic/edit/'+row.id" class="link-type">
            <span>{{ row.title }}</span>
          </router-link>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Authors">
        <template slot-scope="scope">
          <span>{{ scope.row.authors }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Genres">
        <template slot-scope="scope">
          <span>{{ scope.row.genres }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Release Date">
        <template slot-scope="scope">
          <span>{{ scope.row.release_date | dateFormatter }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="Created At">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at | dateFormatter }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="200">
        <template slot-scope="scope">
          <router-link :to="'/comic/edit/'+scope.row.id">
            <el-button type="primary" size="small" icon="el-icon-edit">
              Edit
            </el-button>
          </router-link>
          <el-button type="primary" size="small" icon="el-icon-delete" @click="deleteItem(scope.row.id)">
            Delete
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
const comicResource = new Resource('comics');

export default {
  name: 'ArticleList',
  components: { Pagination },
  filters: {
    dateFormatter(date) {
      return (new Date(date)).toLocaleDateString('id-ID');
    },
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
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
        with: 'authors',
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    deleteItem(id){
      comicResource.destroy(id)
        .then((response) => {
          this.getList();
        });
    },
    async getList() {
      this.listLoading = true;
      const { data } = await comicResource.list(this.listQuery);
      this.list = data.items;
      this.list.forEach((el, key) => {
        this.list[key].genres = JSON.parse(el.genres).join(', ');
        this.list[key].tags = JSON.parse(el.tags).join(', ');
        this.list[key].authors = el.authors.map((author) => {
          return author.name;
        }).join(', ');
      });
      this.total = data.total;
      this.listLoading = false;
    },
    handleFilter(){
      this.listQuery['search'] = this.query.keyword;
      this.getList();
    },
    handleCreate(){
      this.$router.push('/comic/create');
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
