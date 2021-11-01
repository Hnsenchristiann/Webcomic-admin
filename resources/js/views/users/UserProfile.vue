<template>
  <div class="app-container">
    <el-form v-if="user" :model="user">
      <el-row :gutter="20">
        <el-col :span="6">
          <user-card
            :user="user"
            :is-token-consistent="tokenConsistent"
            @userUpdate="getUser($route.params.id) && checkBalance($route.params.id)"
          />
          <!-- <user-bio /> -->
        </el-col>
        <el-col :span="18">
          <user-activity
            :user="user"
            :token-transactions="tokenTransactions"
            :comic-transactions="comicTransactions"
            :comic-load-more="comicLoadMoreEnabled"
            :token-load-more="tokenLoadMoreEnabled"
            @nextComicPage="nextComicPage"
            @nextTransactionPage="nextTransactionPage"
          />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
// import Resource from '@/api/resource';
import UserResource from '@/api/user';
// import UserBio from './components/UserBio';
import UserCard from './components/UserCard';
import UserActivity from './components/UserActivity';
import TokenResource from '@/api/tokenTransaction';
import { Message } from 'element-ui';
const tokenResource = new TokenResource();
const userResource = new UserResource();
export default {
  name: 'EditUser',
  components: { UserCard, UserActivity },
  data() {
    return {
      tokenConsistent: true,
      comicCount: 0,
      tokenCount: 0,
      comicTransactions: [],
      tokenTransactions: [],
      user: {},
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
      comicTransactionCurrentPage: 1,
      tokenTransactionCurrentPage: 1,
      tokenLoadMoreEnabled: true,
      comicLoadMoreEnabled: true,
    };
  },
  watch: {
    '$route': 'getUser',
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    const currentUserId = this.$store.getters.userId;
    if (id === currentUserId) {
      this.$router.push('/profile/edit');
      return;
    }
    this.getUser(id);
    this.checkBalance(id);
    // this.getComicTransactions(id);
    // this.getTokenTransactions(id);
  },
  methods: {
    nextTransactionPage(){
      const id = this.$route.params && this.$route.params.id;
      this.tokenTransactionCurrentPage += 1;
      this.getTokenTransactions(id);
    },
    nextComicPage(){
      const id = this.$route.params && this.$route.params.id;
      this.comicTransactionCurrentPage += 1;
      this.getComicTransactions(id);
    },
    checkBalance(id){
      userResource.checkBalance(id)
        .then((response) => {
          this.tokenConsistent = response;
          if (!response){
            Message({
              message: 'User token balance is inconsistent with token transactions! Please rectify!',
              type: 'error',
              duration: 5 * 1000,
            });
          }
        });
    },
    async getTokenTransactions(id){
      this.tokenTransactionQuery.where_user_id = id;
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
    async getComicTransactions(id){
      this.comicTransactionQuery.where_user_id = id;
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
    async getUser(id) {
      const { data } = await userResource.get(id);
      this.user = data;
      return data;
    },
  },
};
</script>
