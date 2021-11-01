<template>
  <el-dialog :visible.sync="dialogVisible" title="Token Spent">
    <div style="margin-bottom:16px;">
      <el-date-picker
        v-model="startDate"
        type="date"
        placeholder="Pick start date"
        format="yyyy/MM/dd"
        style="width: 135px; margin-left: 8px;"
      />
      <span> - </span>
      <el-date-picker
        v-model="endDate"
        type="date"
        placeholder="Pick end date"
        format="yyyy/MM/dd"
        style="width: 135px;"
      />
    </div>
    <div v-for="(innerTransaction, idx) in tokenTransactions" :key="idx" class="post">
      <el-card style="margin: 10px;">
        <div style="float: right; margin-top: 8px;">{{ innerTransaction.created_at | dateFormatter }} </div>
        <div style="margin-top: 50px;">Name : {{ innerTransaction.user.name }}</div>
        <div style="margin-top: 5px;">Chapter : {{ innerTransaction.transactionable.chapter }} </div>
        <div style="margin-top: 5px;">Token Amount : {{ innerTransaction.token_amount }} </div>
      </el-card>
    </div>
    <pagination v-show="total>0" :total="total" :page.sync="tokenTransactionQuery.page" :limit.sync="tokenTransactionQuery.limit" @pagination="getTransactions" />
  </el-dialog>
</template>

<script>
export default {
  name: 'TokenTransactions',
  props: {
    tokenTransactions: {
      type: Object,
      default: () => {},
    },
  },
  data(){
    return {
      tokenTransactionQuery: {
        page: 1,
        limit: 20,
        paginate: 20,
        with: ['user', 'transactionable'],
        transactions_where_type: 'purchase_comic',
      },
    };
  },
  methods: {
    getTransactions(){

    },
  },
};
</script>
