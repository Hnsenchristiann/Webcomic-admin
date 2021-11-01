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

      <el-table-column min-width="180px" align="center" label="Tokens Spent">
        <template slot-scope="{row}">
          <span class="link-type" @click="openTransactionsModal(row.id)">{{ row.total_tokens }}</span>
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

      <el-table-column width="120px" align="center" label="Release Date">
        <template slot-scope="scope">
          <span>{{ scope.row.release_date | dateFormatter }}</span>
        </template>
      </el-table-column>

      <el-table-column width="120px" align="center" label="Created At">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at | dateFormatter }}</span>
        </template>
      </el-table-column>

      <el-table-column width="100px" align="center" label="Status">
        <template slot-scope="scope">
          <span>{{ scope.row.is_draft ? 'Draft' : 'Published' }}</span>
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

    <!-- <el-dialog width="90%" :visible.sync="dialogVisible" title="Tokens Spent" :before-close="handleClose">
      <el-tabs v-model="transactionTabs" @tab-click="handleTabsClick">
        <el-tab-pane label="Token Transactions" name="transactions">
          <div style="margin-bottom:16px;">
            <el-date-picker
              v-model="startDate"
              type="date"
              placeholder="Pick start date"
              format="yyyy/MM/dd"
              style="width: 150px; margin-left: 8px;"
              @change="changeDate(false)"
            />
            <span> - </span>
            <el-date-picker
              v-model="endDate"
              type="date"
              placeholder="Pick end date"
              format="yyyy/MM/dd"
              style="width: 150px;"
              @change="changeDate(false)"
            />
            <el-input v-model="tokenTransactionQuery.search" placeholder="Search by user" style="width: 150px;" class="filter-item" @keyup.enter.native="getTokenTransactions" />
            <el-button class="filter-item" type="primary" icon="el-icon-search" style="margin-left: -5px; border-top-left-radius: 0; border-bottom-left-radius: 0;" @click="getTokenTransactions">
              {{ $t('table.search') }}
            </el-button>
            <el-select v-model="tokenTransactionQuery.transactions_where_chapter" placeholder="Select chapter" @change="getTokenTransactions">
              <el-option
                v-for="item in selectedChapters"
                :key="item.id"
                :label="item.chapter"
                :value="item.id"
              />
            </el-select>
          </div>
          <el-table :data="tokenTransactions" border fit highlight-current-row style="width: 100%">
            <el-table-column label="ID">
              <template slot-scope="scope">
                <span>{{ scope.row.id }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Name">
              <template slot-scope="scope">
                <span>{{ scope.row.user.name }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Date">
              <template slot-scope="scope">
                <span>{{ scope.row.created_at | dateFormatter }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Chapter">
              <template slot-scope="scope">
                <span>{{ scope.row.transactionable.chapter }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Amount">
              <template slot-scope="scope">
                <span>{{ scope.row.token_amount }}</span>
              </template>
            </el-table-column>
          </el-table>
          <div class="total-tokens-query">
            Queried Total Tokens Spent: {{ queriedTotalTokens }} tokens
          </div>
          <pagination v-show="tokenCount>0" :total="tokenCount" :page.sync="tokenTransactionQuery.page" :limit.sync="tokenTransactionQuery.limit" @pagination="getTokenTransactions(false)" />
        </el-tab-pane>
        <el-tab-pane label="Transaction Graph" name="graph">
          <div style="margin-bottom:16px;">
            <el-date-picker
              v-model="startDate"
              type="date"
              placeholder="Pick start date"
              format="yyyy/MM/dd"
              style="width: 150px; margin-left: 8px;"
              @change="changeDate(true)"
            />
            <span> - </span>
            <el-date-picker
              v-model="endDate"
              type="date"
              placeholder="Pick end date"
              format="yyyy/MM/dd"
              style="width: 150px;"
              @change="changeDate(true)"
            />
            <el-input v-model="tokenTransactionQuery.search" placeholder="Search by user" style="width: 150px;" class="filter-item" @keyup.enter.native="getAggregatedTokenTransactions" />
            <el-button class="filter-item" type="primary" icon="el-icon-search" style="margin-left: -5px; border-top-left-radius: 0; border-bottom-left-radius: 0;" @click="getAggregatedTokenTransactions">
              {{ $t('table.search') }}
            </el-button>
            <el-select v-model="tokenTransactionQuery.transactions_where_chapter" placeholder="Select chapter" @change="getAggregatedTokenTransactions">
              <el-option
                v-for="item in selectedChapters"
                :key="item.id"
                :label="item.chapter"
                :value="item.id"
              />
            </el-select>
          </div>
          <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
            <line-chart ref="lineChart" :chart-data="lineChartData" :dates="dates" />
          </el-row>
        </el-tab-pane>
      </el-tabs>
    </el-dialog> -->
    <token-dialog ref="transactionsDialog" :visible="dialogVisible" />

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
// import { fetchTransactions } from '@/api/comic';
import TokenResource from '@/api/tokenTransaction';
// import LineChart from './components/LineChart';
import TokenDialog from './components/TokenDialog';
import { fetchRawTransactions } from '@/api/data';
import _ from 'lodash';
// import dayjs from 'dayjs';
const tokenResource = new TokenResource();
const comicResource = new Resource('comics');
const chapterResource = new Resource('chapters');

export default {
  name: 'ArticleList',
  components: { Pagination, TokenDialog },
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
      dates: [],
      lineChartData: {
        transaction_bucket: [],
      },
      transactionTabs: 'transactions',
      dialogVisible: false,
      // endDate: dayjs().format('YYYY-MM-DD'),
      // startDate: dayjs().add(-7, 'day').format('YYYY-MM-DD'),
      endDate: null,
      startDate: null,
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
        with: ['authors', 'chapters'],
      },
      tokenTransactionQuery: {
        page: 1,
        limit: 20,
        paginate: 20,
        with: ['user', 'transactionable'],
        // sort_by_desc: 'created_at',
        transactions_where_type: 'purchase_comic',
        where_created_after: null,
        where_created_before: null,
        search: null,
        transactions_where_chapter: null,
        sort_by_desc: 'created_at',
      },
      tokenTransactionCurrentPage: 1,
      tokenTransactions: [],
      tokenCount: 0,
      tokenLoadMoreEnabled: true,
      selectedComic: null,
      selectedChapters: {},
      queriedTotalTokens: 0,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getAggregatedTokenTransactions(){
      const { transaction_bucket, dates } = await fetchRawTransactions(_.omit(this.tokenTransactionQuery, ['page', 'limit', 'paginate', 'with']));
      this.dates = dates;
      this.lineChartData.transaction_bucket = transaction_bucket;
    },
    handleTabsClick(tab, event){
      if (tab.name === 'graph'){
        this.getAggregatedTokenTransactions();
        this.$nextTick(() => {
          this.$refs.lineChart.resize();
        });
      }
    },
    handleClose(){
      this.tokenTransactionQuery.where_created_after = null;
      this.tokenTransactionQuery.where_created_before = null;
      this.tokenTransactionQuery.search = null;
      this.tokenTransactionQuery.transactions_where_chapter = null;
      this.dialogVisible = false;
      this.selectedChapters = {};
      this.selectedComic = null;
      this.tokenTransactions = [];
      this.transactionTabs = 'transactions';
    },
    changeDate(aggregate = false){
      if (_.isNull(this.startDate) || _.isNull(this.endDate)){
        return;
      }

      this.tokenTransactionQuery.where_created_after = this.startDate;
      this.tokenTransactionQuery.where_created_before = this.endDate;
      if (aggregate){
        this.getAggregatedTokenTransactions();
      } else {
        this.getTokenTransactions();
      }
    },
    async getTokenTransactions(withTotal = true){
      this.tokenTransactionQuery.transactions_belong_to_comic = this.selectedComic;
      // this.tokenTransactionQuery.page = this.tokenTransactionCurrentPage;
      const { data } = await tokenResource.list(this.tokenTransactionQuery);
      this.tokenTransactions = data.items;
      this.tokenCount = data.total;
      if (withTotal){
        const totalData = await tokenResource.queriedTotalTokensSpent(_.omit(this.tokenTransactionQuery, ['page', 'limit', 'paginate', 'with']));
        this.queriedTotalTokens = totalData.data.total;
      }
      // if (this.tokenTransactions.length > 0){
      //   this.tokenTransactions = this.tokenTransactions.concat(data.items);
      // } else {
      //   this.tokenTransactions = data.items;
      //   this.tokenCount = data.total;
      // }
      // if (this.tokenCount <= this.tokenTransactions.length){
      //   this.tokenLoadMoreEnabled = false;
      // }
      return data;
    },
    async getChapters(id){
      const { data } = await chapterResource.list({
        select: ['id', 'chapter'],
        where_comic_id: id,
      });
      this.selectedChapters = [{ id: null, chapter: null }, ...data.items];
    },
    async openTransactionsModal(id){
      this.selectedComic = id;
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)',
      });
      // await this.getTokenTransactions();
      // await this.getChapters(id);
      this.$refs.transactionsDialog.openDialog(this.selectedComic, {
        transactions_belong_to_comic: this.selectedComic,
      });
      this.dialogVisible = true;
      loading.close();
    },
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
.fake-link{
  color: rgb(51, 122, 183);
  cursor: pointer;
}
.total-tokens-query{
  text-align: end;
  margin-top: 15px;
}
</style>

<style rel="stylesheet/scss" lang="scss" scoped>
.filter-item{
  input{
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
}
</style>
