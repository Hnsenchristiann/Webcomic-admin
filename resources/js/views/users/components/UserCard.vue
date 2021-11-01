<template>
  <el-card v-if="user.name">
    <div class="user-profile">
      <div class="user-avatar box-center">
        <pan-thumb :image="user.avatar" :height="'100px'" :width="'100px'" :hoverable="false" />
      </div>
      <div class="box-center">
        <div class="user-name text-center">
          {{ user.name }}
        </div>
        <div class="user-role text-center text-muted">
          {{ getRole() }}
        </div>
      </div>
      <div class="box-social">
        <el-table :data="social" :show-header="false">
          <el-table-column prop="name" label="Name" />
          <el-table-column label="Count" align="left" width="100">
            <template slot-scope="scope">
              {{ scope.row.count }}
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div v-if="!isTokenConsistent" class="user-follow">
        <el-button type="primary" style="width: 100%;" @click="rectifyUserTokens">
          Rectify User Tokens
        </el-button>
      </div>
      <div v-else class="user-follow">
        <el-button type="primary" style="width: 100%;" @click="openGrantTokenModal">
          Grant Tokens
        </el-button>
      </div>
    </div>
    <el-dialog
      :title="'Grant tokens to ' + user.name"
      :visible.sync="dialogVisible"
      width="30%"
      :before-close="handleClose"
    >
      <span>Enter token amount to grant:</span>
      <el-input-number v-model="tokenToGrant" :min="1" />
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="grantToken">Confirm</el-button>
      </span>
    </el-dialog>
  </el-card>
</template>

<script>
import PanThumb from '@/components/PanThumb';
import _ from 'lodash';
import UserResource from '@/api/user';
const userResource = new UserResource();

export default {
  components: { PanThumb },
  props: {
    isTokenConsistent: {
      type: Boolean,
      default: true,
    },
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
  },
  data() {
    return {
      tokenToGrant: 1,
      dialogVisible: false,
      social: [
      ],
      fields: [
        'id',
        'email',
        'total_tokens',
      ],
    };
  },
  watch: {
    user: function(){
      this.social = [];
      Object.keys(this.user).forEach((el) => {
        if (_.includes(this.fields, el)){
          this.social.push({
            'name': _.startCase(el),
            'count': this.user[el],
          });
        }
      });
      return this.user;
    },
  },
  created(){
    console.log(this.isTokenConsistent);
    // this.social = [];
    // Object.keys(this.user).forEach((el) => {
    //   this.social.push({
    //     'name': el,
    //     'count': this.user[el],
    //   });
    // });
  },
  methods: {
    handleClose(){
      this.tokenToGrant = 1;
    },
    grantToken(){
      if (this.tokenToGrant < 1){
        this.$message({
          type: 'error',
          message: 'What the fuck are you doing? You can\'t grant less than 1 token to user?!?!',
        });
        return;
      }
      userResource.grantToken(this.user.id, this.tokenToGrant)
        .then((response) => {
          this.dialogVisible = false;
          this.$emit('userUpdate');
          this.$message({
            type: 'success',
            message: 'Token granted!',
          });
        })
        .catch((err) => {
          this.$message({
            type: 'error',
            message: 'Something went wrong: ' + err,
          });
        });
    },
    openGrantTokenModal(){
      this.dialogVisible = true;
    },
    rectifyUserTokens(){
      this.$confirm('This will permanently delete the file. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        userResource.rectifyBalance(this.user.id)
          .then((response) => {
            this.$emit('userUpdate');
            this.$message({
              type: 'success',
              message: 'Balance rectified',
            });
          })
          .catch((err) => {
            this.$message({
              type: 'error',
              message: 'Something went wrong: ' + err,
            });
          });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Canceled',
        });
      });

      // userResource.rectifyBalance(this.user.id)
      //   .then((response) => {
      //     //  do something
      //   });
    },
    getRole() {
      const roles = this.user.roles.map(value => this.$options.filters.uppercaseFirst(value));
      return roles.join(' | ');
    },
  },
};
</script>

<style lang="scss" scoped>
.user-profile {
  .user-name {
    font-weight: bold;
  }
  .box-center {
    padding-top: 10px;
  }
  .user-role {
    padding-top: 10px;
    font-weight: 400;
    font-size: 14px;
  }
  .box-social {
    padding-top: 30px;
    .el-table {
      border-top: 1px solid #dfe6ec;
    }
  }
  .user-follow {
    padding-top: 20px;
  }
}
</style>
