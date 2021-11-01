<template>
  <div class="createPost-container">
    <el-form ref="postForm" label-position="top" :model="postForm" :rule="rules" class="form-container">
      <sticky class-name="sub-navbar">
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
            <el-form-item style="margin-bottom: 40px;" prop="title">
              <MDinput v-model="postForm.name" :maxlength="100" name="name" required>
                Author's Name
              </MDinput>
            </el-form-item>

            <div class="postInfo-container">
              <el-row>
                <el-col :span="8">
                  <el-form-item label-width="80px" label="Email :" class="postInfo-container-item">
                    <el-input
                      v-model="postForm.email"
                      :rows="1"
                      type="input"
                      class="author-input"
                      autosize
                      placeholder="Enter Email"
                    />
                  </el-form-item>
                </el-col>
                <el-col :span="8">
                  <el-form-item label-width="80px" label="User ID :" class="postInfo-container-item">
                    <el-input
                      v-model="postForm.user_id"
                      :rows="1"
                      type="input"
                      class="author-input"
                      autosize
                      placeholder="Enter User ID"
                    />
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
        <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Description:">
          <el-input
            v-model="postForm.description"
            :rows="1"
            type="textarea"
            class="comic-textarea"
            autosize
            placeholder="Please enter author description"
          />
        </el-form-item>
        <div class="text item">
          <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Social Media Links :">
            <el-row>
              <el-button style="float:right; margin-top: 5px;" type="primary" icon="el-icon-circle-plus-outline" @click="addNewSocialMedia">Add Social Media</el-button>
              <div style="margin-top: 70px;">
                <el-card v-for="(social, index) in social_media" :key="'social-' + index" class="box-card" style="margin-top: 30px;">
                  <el-col :span="8">
                    <MDinput v-model="social.name" :maxlength="100" name="name" style="margin-bottom: 30px;" required>
                      Social Media Name
                    </MDinput>
                    <el-form-item label-width="80px" class="postInfo-container-item">
                      <el-input
                        v-model="social.link"
                        :rows="1"
                        type="input"
                        class="author-input"
                        style="margin-bottom: 30px;"
                        autosize
                        placeholder="Social Media Link"
                      />
                    </el-form-item>
                    <el-button style="margin-bottom: 15px;" type="danger" @click="deleteSocialMedia(index)">cancel</el-button>
                  </el-col>
                </el-card>
              </div>
            </el-row>
          </el-form-item>
        </div>
        <el-form-item prop="profile_picture_url" style="margin-bottom: 30px;" label-width="80px" label="Profile Photo:">
          <Upload v-model="postForm.profile_picture_url" />
        </el-form-item>
      </div>
    </el-form>
  </div>
</template>

<script>
import Upload from '@/components/Upload/SingleImage';
import MDinput from '@/components/MDinput';
import Sticky from '@/components/Sticky'; // Sticky header
import { validURL } from '@/utils/validate';
// import { fetchComic } from '@/api/comic';
import Resource from '@/api/resource';
const authorResource = new Resource('authors');

const defaultForm = {
  user_id: null,
  is_draft: true,
  authors: [],
  title: '',
  content: '',
  content_short: '',
  source_uri: '',
  profile_picture_url: '',
  display_time: undefined,
  id: undefined,
  platforms: ['a-platform'],
  comment_disabled: false,
  importance: 0,
};

export default {
  name: 'ComicDetail',
  components: {
    MDinput,
    Upload,
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
    const validateSourceUri = (rule, value, callback) => {
      if (value) {
        if (validURL(value)) {
          callback();
        } else {
          this.$message({
            message: 'External URL is invalid.',
            type: 'error',
          });
          callback(new Error('External URL is invalid.'));
        }
      } else {
        callback();
      }
    };
    return {
      postForm: Object.assign({}, defaultForm),
      loading: false,
      authorListOptions: [],
      genreListOptions: [],
      social_media: [{
        name: '',
        link: '',
      }],
      rules: {
        profile_picture_url: [{ validator: validateRequire }],
        title: [{ validator: validateRequire }],
        content: [{ validator: validateRequire }],
        source_uri: [{ validator: validateSourceUri, trigger: 'blur' }],
      },
      tempRoute: {},
      chapterAccordion: {},
      pageAccordion: {},
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
    lang() {
      return this.$store.getters.language;
    },
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
    } else {
      this.postForm = Object.assign({}, defaultForm);
    }

    // Why need to make a copy of this.$route here?
    // Because if you enter this page and quickly switch tag, may be in the execution of the setTagsViewTitle function, this.$route is no longer pointing to the current page
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    addNewSocialMedia(){
      this.social_media.push({
        name: '',
        link: '',
      });
    },

    deleteSocialMedia(index){
      this.social_media.splice(index, 1);
    },

    removeChapter(index){
      console.log(index);
    },
    addChapterAfter(index){
      console.log(index);
    },
    removePage(index){
      console.log(index);
    },
    addPageAfter(index){
      console.log(index);
    },
    tester(){
      console.log(typeof this.postForm.cover_url);
    },
    togglePageAccordion(pageId){
      const tempAccordion = Object.assign({}, this.pageAccordion);
      tempAccordion[pageId] = !tempAccordion[pageId];
      this.pageAccordion = tempAccordion;
      console.log(this.pageAccordion[pageId]);
    },
    toggleChapterAccordion(cptId){
      const tempAccordion = Object.assign({}, this.chapterAccordion);
      tempAccordion[cptId] = !tempAccordion[cptId];
      this.chapterAccordion = tempAccordion;
      console.log(this.chapterAccordion[cptId]);
    },
    fetchData(id) {
      authorResource.get(id)
        .then(response => {
          this.postForm = response.data;
          const parsedSocMed = JSON.parse(this.postForm.social_media_links);
          const tempSocmed = Object.keys(parsedSocMed)
            .map((el) => {
              return {
                name: el,
                link: parsedSocMed[el],
              };
            });
          this.social_media = tempSocmed.length > 0 ? tempSocmed : this.social_media;
          // this.postForm.chapters.forEach((el) => {
          //   this.chapterAccordion[el.id] = false;
          //   el.pages.forEach((pel) => {
          //     this.pageAccordion[pel.id] = false;
          //   });
          // });
          // this.postForm.authors = this.postForm.authors.map((el) => {
          //   return el.id;
          // });
          // this.postForm.genres = JSON.parse(this.postForm.genres);
          // // Just for test
          // // this.postForm.title += `   Article Id:${this.postForm.id}`;
          // // this.postForm.content_short += `   Article Id:${this.postForm.id}`;

          // // Set tagsview title
          // this.setTagsViewTitle();
        })
        .catch(err => {
          console.log(err);
        });
    },
    setTagsViewTitle() {
      const title =
        this.lang === 'zh'
          ? '编辑文章'
          : this.lang === 'vi'
            ? 'Chỉnh sửa'
            : 'Edit Article'; // Should move to i18n
      const route = Object.assign({}, this.tempRoute, {
        title: `${title}-${this.postForm.id}`,
      });
      this.$store.dispatch('updateVisitedView', route);
    },
    submitForm() {
      console.log(this.social_media);
      this.postForm.social_media_links = JSON.stringify(this.social_media.reduce((obj, item) => Object.assign(obj, { [item.name]: item.link }), {}));
      if (this.isEdit){
        authorResource.update(this.postForm.id, this.postForm)
          .then((response) => {
            this.$router.push('/author');
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        authorResource.store(this.postForm)
          .then((response) => {
            this.$router.push('/author');
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
    getAuthorList(query) {
      console.log(query);
      authorResource.list().then(response => {
        if (!response.data.items) {
          return;
        }
        this.authorListOptions = response.data.items;
      });
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
