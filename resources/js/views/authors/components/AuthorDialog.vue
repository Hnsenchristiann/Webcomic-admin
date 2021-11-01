<template>
  <el-dialog width="90%" :visible.sync="visible" title="Tokens Spent" :before-close="handleClose">
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
        <pagination v-show="tokenCount > 0" :total="tokenCount" :page.sync="tokenTransactionQuery.page" :limit.sync="tokenTransactionQuery.limit" @pagination="getTokenTransactions(false)" />
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
        </div>
        <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
          <line-chart :chart-data="lineChartData" :dates="dates" />
        </el-row>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
// import { fetchTransactions } from '@/api/comic';
import TokenResource from '@/api/tokenTransaction';
import LineChart from './LineChart';
import { fetchRawTransactions } from '@/api/data';
import _ from 'lodash';
// import dayjs from 'dayjs';
const tokenResource = new TokenResource();
const authorResource = new Resource('authors');
const chapterResource = new Resource('chapters');
export default {
  name: 'AuthorDialog',
  components: { Pagination, LineChart },
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
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      dates: [],
      lineChartData: {
        transaction_bucket: [],
      },
      transactionTabs: 'transactions',
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
        with: 'authors',
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
      selectedAuthor: null,
      selectedChapters: {},
      queriedTotalTokens: 0,
    };
  },
  methods: {
    async openDialog(id, extraQuery = {}){
      Object.keys(extraQuery).forEach((el) => {
        this.tokenTransactionQuery[el] = extraQuery[el];
      });
      await this.getTokenTransactions(true);
      this.visible = true;
    },
    async getAggregatedTokenTransactions(){
      const { transaction_bucket, dates } = await fetchRawTransactions(_.omit(this.tokenTransactionQuery, ['page', 'limit', 'paginate', 'with']));
      this.dates = dates;
      this.lineChartData.transaction_bucket = transaction_bucket;
    },
    handleTabsClick(tab, event){
      console.log(tab);
      if (tab.name === 'graph'){
        this.getAggregatedTokenTransactions();
      }
    },
    handleClose(){
      this.tokenTransactionQuery.where_created_after = null;
      this.tokenTransactionQuery.where_created_before = null;
      this.tokenTransactionQuery.search = null;
      this.tokenTransactionQuery.transactions_where_chapter = null;
      this.selectedChapters = {};
      this.selectedAuthor = null;
      this.tokenTransactions = [];
      this.transactionTabs = 'transactions';
      this.visible = false;
      this.startDate = null;
      this.endDate = null;
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
      let innerData;
      tokenResource.list(this.tokenTransactionQuery)
        .then(({ data }) => {
          this.tokenTransactions = data.items;
          this.tokenCount = data.total;
          innerData = data;
        });
      // const { data } = await tokenResource.list(this.tokenTransactionQuery);
      // this.tokenTransactions = data.items;
      // this.tokenCount = data.total;
      if (withTotal){
        const totalData = await tokenResource.queriedTotalTokensSpent(_.omit(this.tokenTransactionQuery, ['page', 'limit', 'paginate', 'with']));
        this.queriedTotalTokens = totalData.data.total;
      }
      return innerData;
    },
    async getChapters(id){
      const { data } = await chapterResource.list({
        select: ['id', 'chapter'],
        where_comic_id: id,
      });
      this.selectedChapters = [{ id: null, chapter: null }, ...data.items];
      console.log(this.selectedChapters);
    },
    deleteItem(id){
      authorResource.destroy(id)
        .then((response) => {
          this.getList();
        });
    },
    handleFilter(){
      this.listQuery['search'] = this.query.keyword;
      this.getList();
    },
    handleCreate(){
      this.$router.push('/author/create');
    },
  },
};
</script>
