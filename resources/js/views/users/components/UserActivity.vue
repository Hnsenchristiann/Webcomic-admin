<template>
  <el-card v-if="user.name">
    <el-tabs v-model="activeActivity" @tab-click="handleClick">
      <el-tab-pane label="Comic Purchases" name="first">
        <div class="user-activity">
          <div v-for="(innerTransaction, idx) in comicTransactions" :key="idx" class="post">
            <div class="user-block">
              <div>Comic Title: {{ innerTransaction.transactionable ? innerTransaction.transactionable.comic.title : '' }}</div>
              <div>Chapter: {{ innerTransaction.transactionable ? innerTransaction.transactionable.chapter : '' }}</div>
              <div>Token: {{ innerTransaction.token_amount }}</div>
              <div>Date: {{ innerTransaction.created_at | dateFormatter }}</div>
            </div>
          </div>
          <button :disabled="!comicLoadMoreEnabled" @click="nextComicPage">Load More</button>
        </div>
      </el-tab-pane>
      <el-tab-pane label="Token Purchases" name="second">
        <div class="user-activity">
          <div v-for="(innerTransaction, idx) in tokenTransactions" :key="idx" class="post">
            <div class="user-block">
              <div>Token Amount: {{ innerTransaction.token_amount }}</div>
              <!-- <div>Price: {{ parseMoneyAmount(innerTransaction.descriptor) }}</div> -->
              <div>Price: {{ parseDescriptor(innerTransaction.descriptor).money_value | currencyFormatter }}</div>
              <div>Payment Method: {{ parseDescriptor(innerTransaction.descriptor).payment_type }}</div>
              <div>Date: {{ innerTransaction.created_at | dateFormatter }}</div>
            </div>
          </div>
          <button :disabled="!tokenLoadMoreEnabled" @click="nextTokenPage">Load More</button>
        </div>
      </el-tab-pane>
      <el-tab-pane label="Token Granted" name="third">
        <div class="user-activity">
          <div v-for="(innerTransaction, idx) in tokenGrantedTransactions" :key="idx" class="post">
            <div class="user-block">
              <div>Token Amount: {{ innerTransaction.token_amount }}</div>
              <div>Date: {{ innerTransaction.created_at | dateFormatter }}</div>
            </div>
          </div>
          <button :disabled="!tokenLoadMoreEnabled" @click="nextTokenGrantedPage">Load More</button>
        </div>
      </el-tab-pane>
      <el-tab-pane v-loading="updating" label="Account" name="fourth">
        <el-form-item label="Name">
          <el-input v-model="user.name" :disabled="user.role === 'admin'" />
        </el-form-item>
        <el-form-item label="Email">
          <el-input v-model="user.email" :disabled="user.role === 'admin'" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :disabled="user.role === 'admin'" @click="onSubmit">
            Update
          </el-button>
        </el-form-item>
      </el-tab-pane>
    </el-tabs>
  </el-card>
</template>

<script>
import Resource from '@/api/resource';
import TokenResource from '@/api/tokenTransaction';
// import TokenResource from '@/api/tokenTransaction';
const userResource = new Resource('users');
const tokenResource = new TokenResource();
// const tokenResource = new TokenResource();
export default {
  filters: {
    dateFormatter(date){
      return new Date(date).toLocaleString('id-ID');
    },
    currencyFormatter(amount){
      return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
    },
  },
  props: {
    // comicLoadMore: {
    //   type: Boolean,
    //   default: true,
    // },
    // tokenLoadMore: {
    //   type: Boolean,
    //   default: true,
    // },
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          avatar: '',
          roles: [],
        };
      },
    },
    // tokenTransactions: {
    //   type: Array,
    //   default: () => [],
    // },
    // comicTransactions: {
    //   type: Array,
    //   default: () => [],
    // },
  },
  data() {
    return {
      comicCount: 0,
      tokenCount: 0,
      tokenGrantedCount: 0,
      comicTransactions: [],
      tokenTransactions: [],
      tokenGrantedTransactions: [],
      // user: {},
      comicTransactionQuery: {
        limit: 20,
        paginate: 20,
        sort_by_desc: 'created_at',
        with: 'transactionable.comic',
        // where_not_null: 'transactionable_type',
        transactions_where_type: 'purchase_comic',
      },
      tokenTransactionQuery: {
        limit: 20,
        paginate: 20,
        sort_by_desc: 'created_at',
        with: 'transactionable.comic',
        // where_null: 'transactionable_type',
        transactions_where_type: 'purchase_token',
      },
      tokenGrantedTransactionQuery: {
        limit: 20,
        paginate: 20,
        sort_by_desc: 'created_at',
        with: 'transactionable.comic',
        // where_null: 'transactionable_type',
        transactions_where_type: 'granted_token',
      },
      comicTransactionCurrentPage: 1,
      tokenTransactionCurrentPage: 1,
      tokenGrantedTransactionCurrentPage: 1,
      tokenGrantedLoadMoreEnabled: true,
      tokenLoadMoreEnabled: true,
      comicLoadMoreEnabled: true,
      activeActivity: 'first',
      carouselImages: [
        'https://cdn.laravue.dev/photo1.png',
        'https://cdn.laravue.dev/photo2.png',
        'https://cdn.laravue.dev/photo3.jpg',
        'https://cdn.laravue.dev/photo4.jpg',
      ],
      updating: false,
      // transactions: [],
      // transactionQuery: {
      //   page: 1,
      //   limit: 15,
      //   keyword: '',
      //   role: '',
      // },
    };
  },
  mounted(){
    this.getTokenTransactions();
    this.getComicTransactions();
    this.getTokenGrantedTransactions();
  },
  methods: {
    async getTokenGrantedTransactions(){
      this.tokenGrantedTransactionQuery.where_user_id = this.$route.params && this.$route.params.id;
      this.tokenGrantedTransactionQuery.page = this.tokenGrantedTransactionCurrentPage;
      const { data } = await tokenResource.list(this.tokenGrantedTransactionQuery);
      if (this.tokenGrantedTransactions.length > 0){
        this.tokenGrantedTransactions = this.tokenGrantedTransactions.concat(data.items);
      } else {
        this.tokenGrantedTransactions = data.items;
        this.tokenGrantedCount = data.total;
      }
      if (this.tokenGrantedCount <= this.tokenGrantedTransactions.length){
        this.tokenGrantedLoadMoreEnabled = false;
      }
      return data;
    },
    async getTokenTransactions(){
      this.tokenTransactionQuery.where_user_id = this.$route.params && this.$route.params.id;
      this.tokenTransactionQuery.page = this.tokenTransactionCurrentPage;
      const { data } = await tokenResource.list(this.tokenTransactionQuery);
      if (this.tokenTransactions.length > 0){
        this.tokenTransactions = this.tokenTransactions.concat(data.items);
      } else {
        this.tokenTransactions = data.items;
        this.tokenCount = data.total;
      }
      if (this.tokenCount <= this.tokenTransactions.length){
        this.tokenLoadMoreEnabled = false;
      }
      return data;
    },
    async getComicTransactions(){
      this.comicTransactionQuery.where_user_id = this.$route.params && this.$route.params.id;
      this.comicTransactionQuery.page = this.comicTransactionCurrentPage;
      const { data } = await tokenResource.list(this.comicTransactionQuery);
      if (this.comicTransactions.length > 0){
        this.comicTransactions = this.comicTransactions.concat(data.items);
      } else {
        this.comicTransactions = data.items;
        this.comicCount = data.total;
      }
      if (this.comicCount <= this.comicTransactions.length){
        this.comicLoadMoreEnabled = false;
      }
      return data;
    },
    parseDescriptor(descriptorObj){
      return JSON.parse(descriptorObj);
    },
    parseMoneyAmount(descriptorObj){
      const descriptor = this.parseDescriptor(descriptorObj);
      return descriptor.money_value.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
    },
    nextTokenGrantedPage(){
      this.tokenGrantedTransactionCurrentPage += 1;
      this.getTokenGrantedTransactions();
    },
    nextTokenPage(){
      this.tokenTransactionCurrentPage += 1;
      this.getTokenTransactions();
    },
    nextComicPage(){
      this.comicTransactionCurrentPage += 1;
      this.getComicTransactions();
    },
    handleClick(tab, event) {
      console.log('Switching tab ', tab, event);
    },
    onSubmit() {
      this.updating = true;
      userResource
        .update(this.user.id, this.user)
        .then(response => {
          this.updating = false;
          this.$message({
            message: 'User information has been updated successfully',
            type: 'success',
            duration: 5 * 1000,
          });
        })
        .catch(error => {
          console.log(error);
          this.updating = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.user-activity {
  .user-block {
    .username, .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }
    img {
      width: 40px;
      height: 40px;
      float: left;
    }
    :after {
      clear: both;
    }
    .img-circle {
      border-radius: 50%;
      border: 2px solid #d2d6de;
      padding: 2px;
    }
    span {
      font-weight: 500;
      font-size: 12px;
    }
  }
  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;
    .image {
      width: 100%;
    }
    .user-images {
      padding-top: 20px;
    }
  }
  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;
    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }
    .link-black {
      &:hover, &:focus {
        color: #999;
      }
    }
  }
  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }
  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }
  .el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
  }
}
</style>
