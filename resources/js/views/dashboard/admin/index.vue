<template>
  <div class="dashboard-editor-container">
    <div style="margin-bottom:16px;">
      Daily Transactions
    </div>
    <div style="margin-bottom:16px;">
      <el-date-picker
        v-model="startDate"
        type="date"
        placeholder="Pick start date"
        format="yyyy/MM/dd"
        @change="fetchTransactions"
      />
      <span> - </span>
      <el-date-picker
        v-model="endDate"
        type="date"
        placeholder="Pick end date"
        format="yyyy/MM/dd"
        @change="fetchTransactions"
      />
    </div>
    <!-- <github-corner style="position: absolute; top: 0px; border: 0; right: 0;" /> -->

    <!-- <panel-group @handleSetLineChartData="handleSetLineChartData" /> -->

    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
      <line-chart :chart-data="lineChartData" :dates="dates" />
    </el-row>

    <!-- <el-row :gutter="32">
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <raddar-chart />
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <pie-chart />
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <bar-chart />
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="8">
      <el-col :xs="{span: 24}" :sm="{span: 24}" :md="{span: 24}" :lg="{span: 12}" :xl="{span: 12}" style="padding-right:8px;margin-bottom:30px;">
        <transaction-table />
      </el-col>
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 6}" :xl="{span: 6}" style="margin-bottom:30px;">
        <todo-list />
      </el-col>
      <el-col :xs="{span: 24}" :sm="{span: 12}" :md="{span: 12}" :lg="{span: 6}" :xl="{span: 6}" style="margin-bottom:30px;">
        <box-card />
      </el-col>
    </el-row> -->
  </div>
</template>

<script>
// import GithubCorner from '@/components/GithubCorner';
// import PanelGroup from './components/PanelGroup';
import LineChart from './components/LineChart';
// import RaddarChart from './components/RaddarChart';
// import PieChart from './components/PieChart';
// import BarChart from './components/BarChart';
// import TransactionTable from './components/TransactionTable';
// import TodoList from './components/TodoList';
// import BoxCard from './components/BoxCard';
import { fetchDailyTransactions } from '../../../api/data';
import dayjs from 'dayjs';

const lineChartData = {
  newVisitis: {
    comic_bucket: [100, 120, 161, 134, 105, 160, 165],
    token_bucket: [120, 82, 91, 154, 162, 140, 145],
  },
  messages: {
    expectedData: [200, 192, 120, 144, 160, 130, 140],
    actualData: [180, 160, 151, 106, 145, 150, 130],
  },
  purchases: {
    expectedData: [80, 100, 121, 104, 105, 90, 100],
    actualData: [120, 90, 100, 138, 142, 130, 130],
  },
  shoppings: {
    expectedData: [130, 140, 141, 142, 145, 150, 160],
    actualData: [120, 82, 91, 154, 162, 140, 130],
  },
};

export default {
  name: 'DashboardAdmin',
  components: {
    // GithubCorner,
    // PanelGroup,
    LineChart,
    // RaddarChart,
    // PieChart,
    // BarChart,
    // TransactionTable,
    // TodoList,
    // BoxCard,
  },
  data() {
    return {
      lineChartData: lineChartData.newVisitis,
      endDate: dayjs().format('YYYY-MM-DD'),
      startDate: dayjs().add(-7, 'day').format('YYYY-MM-DD'),
      dates: [],
    };
  },
  created(){
    this.fetchTransactions();
  },
  methods: {
    fetchTransactions(){
      const startDate = dayjs(this.startDate).format('YYYY-MM-DD');
      const endDate = dayjs(this.endDate).format('YYYY-MM-DD');
      fetchDailyTransactions(startDate, endDate)
        .then((response) => {
          const { comic_bucket, token_bucket, dates } = response;
          this.lineChartData.comic_bucket = comic_bucket;
          this.lineChartData.token_bucket = token_bucket;
          this.dates = dates;
        });
    },
    handleSetLineChartData(type) {
      this.lineChartData = lineChartData[type];
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}
</style>
