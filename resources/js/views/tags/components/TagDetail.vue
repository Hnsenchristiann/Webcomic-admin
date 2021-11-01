<template>
  <div class="createPost-container">
    <el-form ref="postForm" label-position="top" :model="postForm" :rule="rules" class="form-container">
      <sticky :class-name="'sub-navbar '+ (postForm.is_draft ? 'draft' : 'published')">
        <el-button
          v-loading="loading"
          style="margin-left: 10px;"
          type="success"
          @click="submitForm"
        >
          Submit
        </el-button>
        <el-button v-loading="loading" type="warning" @click="draftForm">
          Draft
        </el-button>
      </sticky>
      <div class="createPost-main-container">
        <el-row>
          <el-col :span="24">
            <el-form-item style="margin-bottom: 40px;" prop="name">
              <MDinput v-model="postForm.name" :maxlength="100" name="name" required>
                Tag's Names
              </MDinput>
            </el-form-item>
          </el-col>
        </el-row>
      </div>
    </el-form>
  </div>
</template>

<script>
// import Tinymce from '@/components/Tinymce';
// import Upload from '@/components/Upload/SingleImage';
import MDinput from '@/components/MDinput';
import Sticky from '@/components/Sticky'; // Sticky header
// import { validURL } from '@/utils/validate';
import { fetchTag } from '@/api/tag';
import Resource from '@/api/resource';
const tagResource = new Resource('tags');
const updateTag = (id, data) => {
  return tagResource.update(id, data);
};
const createTag = (data) => {
  return tagResource.store(data);
};
// import _ from 'lodash';

const defaultTagForm = {
  is_draft: true,
  name: '',
  source_uri: '',
  id: undefined,
};

export default {
  name: 'TagDetail',
  components: {
    MDinput,
    // Upload,
    Sticky,
  },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const validateRequire = (rule, value, callback) => {
      if (value === '') {
        this.$message({
          message: rule.field + ' is required',
          type: 'error',
        });
        callback(new Error(rule.field + ' is required'));
      } else {
        callback();
      }
    };
    // const validateSourceUri = (rule, value, callback) => {
    //   if (value) {
    //     if (validURL(value)) {
    //       callback();
    //     } else {
    //       this.$message({
    //         message: 'External URL is invalid.',
    //         type: 'error',
    //       });
    //       callback(new Error('External URL is invalid.'));
    //     }
    //   } else {
    //     callback();
    //   }
    // };
    return {
      postForm: Object.assign({}, defaultTagForm),
      loading: false,
      rules: {
        name: [{ validator: validateRequire }],
      },
    };
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
    } else {
      this.postForm = Object.assign({}, defaultTagForm);
    }

    // Why need to make a copy of this.$route here?
    // Because if you enter this page and quickly switch tag, may be in the execution of the setTagsViewTitle function, this.$route is no longer pointing to the current page
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    fetchData(id) {
      fetchTag(id)
        .then(response => {
          this.postForm = response.data;
        })
        .catch(err => {
          console.log(err);
        });
    },

    submitForm() {
      if (this.isEdit){
        updateTag(this.postForm.id, this.postForm)
          .then((response) => {
            this.$router.push('/tag');
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        createTag(this.postForm)
          .then((response) => {
            this.$router.push('/tag');
          })
          .catch((error) => {
            console.log(error);
          });
      }
      // this.postForm.display_time = parseInt(this.display_time / 1000);
      // this.$refs.postForm.validate(valid => {
      //   if (valid) {
      //     this.loading = true;
      //     this.$notify({
      //       title: 'Success',
      //       message: 'Article has been published successfully',
      //       type: 'success',
      //       duration: 2000,
      //     });
      //     this.postForm.is_draft = false;
      //     this.loading = false;
      //   } else {
      //     console.log('error submit!!');
      //     return false;
      //   }
      // });
    },
    draftForm() {
      if (
        this.postForm.content.length === 0 ||
        this.postForm.title.length === 0
      ) {
        this.$message({
          message: 'Please enter required title and content',
          type: 'warning',
        });
        return;
      }
      this.$message({
        message: 'Successfully saved',
        type: 'success',
        showClose: true,
        duration: 1000,
      });
      this.postForm.is_draft = true;
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
@import "~@/styles/mixin.scss";
.createPost-container {
  position: relative;
  .createPost-main-container {
    padding: 0 45px 20px 50px;
    .postInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;
      .postInfo-container-item {
        float: left;
      }
    }
  }
  .word-counter {
    width: 40px;
    position: absolute;
    right: -10px;
    top: 0px;
  }
}
</style>
<style>
.createPost-container label.el-form-item__label {
  text-align: left;
}
</style>
