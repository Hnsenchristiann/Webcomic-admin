<template>
  <div class="createPost-container">
    <el-form ref="postForm" label-position="top" :model="postForm" :rule="rules" class="form-container">
      <sticky :class-name="'sub-navbar '+ (postForm.is_draft ? 'draft' : 'published')">
        <el-button
          v-loading="loading"
          style="margin-left: 10px;"
          type="success"
          @click="submitForm(false)"
        >
          Publish
        </el-button>
        <el-button v-loading="loading" type="warning" @click="submitForm(true)">
          Draft
        </el-button>
      </sticky>
      <div class="createPost-main-container">
        <el-descriptions v-if="isEdit" title="Comic Info" style="margin-top:36px;">
          <el-descriptions-item label="View Count">{{ postForm.views }}</el-descriptions-item>
          <el-descriptions-item label="Favorite Count">{{ postForm.favorites_count }}</el-descriptions-item>
        </el-descriptions>
        <el-row>
          <el-col :span="24">
            <el-form-item style="margin-bottom: 40px;" prop="title">
              <MDinput v-model="postForm.title" :maxlength="100" name="name" required>
                Title
              </MDinput>
            </el-form-item>

            <div class="postInfo-container">
              <el-row>
                <el-col :span="6">
                  <el-form-item label-width="80px" label="Authors:" class="postInfo-container-item">
                    <el-select
                      v-model="postForm.authors"
                      filterable
                      multiple
                      placeholder="Search user"
                      @change="authorChange"
                    >
                      <el-option
                        v-for="(item,index) in authorListOptions"
                        :key="'author-' + item.id + '-' + index"
                        :label="item.name"
                        :value="item.id"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label-width="80px" label="Genres:" class="postInfo-container-item">
                    <el-select
                      v-model="postForm.genres"
                      filterable
                      multiple
                      placeholder="Genre"
                    >
                      <el-option
                        v-for="(item,index) in genreListOptions"
                        :key="'genre-' + item.id + '-' + index"
                        :label="item.name"
                        :value="item.name"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label-width="80px" label="Tags:" class="postInfo-container-item">
                    <el-select
                      v-model="postForm.tags"
                      filterable
                      multiple
                      placeholder="Tag"
                    >
                      <el-option
                        v-for="(item,index) in tagListOptions"
                        :key="'tag-' + item.id + '-' + index"
                        :label="item.name"
                        :value="item.name"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item label-width="80px" label="Release Date:" class="postInfo-container-item">
                    <el-date-picker
                      v-model="postForm.release_date"
                      type="date"
                      placeholder="Select comic release date"
                    />
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
            <el-form-item label-width="80px" label="Author's share of tokens:">
              <div v-for="(id, index) in postForm.authors" :key="'input-' + index" style="width: 200px; margin-top: 10px; margin-left: 5px; float:left;">
                <span>Share of {{ lodashFind(authorListOptions, {id: id}).name }}</span>
                <el-input
                  v-model="postForm.author_split[id]"
                  type="input"
                  :placeholder="'Share of ' + lodashFind(authorListOptions, {id: id}).name"
                />
              </div>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Description:">
          <el-input
            v-model="postForm.description"
            :rows="1"
            type="textarea"
            class="comic-textarea"
            autosize
            placeholder="Please enter description"
          />
        </el-form-item>
        <el-form-item prop="cover_url" style="margin-bottom: 30px;" label-width="80px" label="Cover Image:">
          <Upload v-model="postForm.cover_url" />
        </el-form-item>
        <div>
          <el-card v-for="(chapter, idx) in postForm.chapters" :key="'chapter-' + idx" class="box-card" style="margin-bottom: 30px;">
            <div slot="header" class="clearfix" @click="toggleChapterAccordion(idx)">
              <span>Chapter {{ chapter.chapter }}</span>
              <div style="float: right; padding: 3px 0">
                <el-button v-if="postForm.chapters.length > 1" @click.stop="removeChapter(idx)">Remove Chapter</el-button>
                <el-button @click.stop="addChapterAfter(idx)">Insert Chapter After</el-button>
              </div>
            </div>
            <div v-if="chapterAccordion[idx]">
              <el-descriptions v-if="isEdit" title="Chapter Info" style="margin-top:36px;">
                <el-descriptions-item label="View Count">{{ chapter.views }}</el-descriptions-item>
                <el-descriptions-item label="Favorite Count">{{ chapter.favorites_count }}</el-descriptions-item>
              </el-descriptions>
              <el-form-item prop="image_url" style="margin-bottom: 30px;" label-width="80px" label="Chapter Preview Image:">
                <Upload v-model="postForm.chapters[idx].image_url" />
              </el-form-item>
              <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Chapter:">
                <el-input
                  v-model="postForm.chapters[idx].chapter"
                  :rows="1"
                  autosize
                />
              </el-form-item>
              <el-row>
                <el-col :span="12">
                  <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Token Price:">
                    <el-input
                      v-model="postForm.chapters[idx].token_price"
                      :rows="1"
                      autosize
                    />
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item style="margin-bottom: 40px;" label-width="80px" label="AR Token Price:">
                    <el-input
                      v-model="postForm.chapters[idx].token_price_ar"
                      :rows="1"
                      autosize
                    />
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="24">
                  <el-form-item label-width="80px" label="Release Date:" class="postInfo-container-item">
                    <el-date-picker
                      v-model="postForm.chapters[idx].release_date"
                      type="date"
                      placeholder="Select chapter release date"
                    />
                  </el-form-item>
                </el-col>
              </el-row>
              <el-card v-for="(page, pidx) in chapter.pages" :key="'pages-' + pidx + '-' + chapter.id" class="box-card" style="margin-bottom: 30px;">
                <div slot="header" class="clearfix" @click="togglePageAccordion(idx, pidx)">
                  <span>Page {{ page.section }}</span>
                  <div style="float: right; padding: 3px 0">
                    <el-button v-if="chapter.pages.length > 1" @click.stop="removePage(idx, pidx)">Remove Page</el-button>
                    <el-button @click.stop="addPageAfter(idx, pidx)">Insert Page After</el-button>
                  </div>
                </div>
                <div v-if="pageAccordion[idx][pidx]">
                  <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Page:">
                    <el-input
                      v-model="postForm.chapters[idx].pages[pidx].section"
                      :rows="1"
                      autosize
                    />
                  </el-form-item>
                  <el-form-item prop="image_url" style="margin-bottom: 30px;" label-width="80px" label="Page Image:">
                    <Upload v-model="postForm.chapters[idx].pages[pidx].image_url" />
                  </el-form-item>
                  <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Scene Element:">
                    <el-input
                      v-model="postForm.chapters[idx].pages[pidx].scene"
                      :rows="10"
                      type="textarea"
                      class="comic-textarea"
                      autosize
                      placeholder="Please enter scene element"
                    />
                  </el-form-item>
                  <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Scene Config:">
                    <el-input
                      v-model="postForm.chapters[idx].pages[pidx].config"
                      :rows="10"
                      type="textarea"
                      class="comic-textarea"
                      autosize
                      placeholder="Please enter JSON config"
                    />
                  </el-form-item>
                </div>
              </el-card>
            </div>
          </el-card>
        </div>
      </div>
    </el-form>
    <!-- <el-form ref="postForm" :model="postForm" :rules="rules" class="form-container">
      <sticky :class-name="'sub-navbar '+postForm.is_draft">
        <CommentDropdown v-model="postForm.comment_disabled" />
        <PlatformDropdown v-model="postForm.platforms" />
        <SourceUrlDropdown v-model="postForm.source_uri" />
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
              <MDinput v-model="postForm.title" :maxlength="100" name="name" required>
                Title
              </MDinput>
            </el-form-item>

            <div class="postInfo-container">
              <el-row>
                <el-col :span="8">
                  <el-form-item label-width="80px" label="Author:" class="postInfo-container-item">
                    <el-select
                      v-model="postForm.author"
                      :remote-method="getAuthorList"
                      filterable
                      remote
                      placeholder="Search user"
                    >
                      <el-option
                        v-for="(item,index) in authorListOptions"
                        :key="item+index"
                        :label="item"
                        :value="item"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>

                <el-col :span="10">
                  <el-form-item
                    label-width="120px"
                    label="Published date:"
                    class="postInfo-container-item"
                  >
                    <el-date-picker
                      v-model="postForm.display_time"
                      type="datetime"
                      format="yyyy-MM-dd HH:mm:ss"
                      placeholder="Select date and time"
                    />
                  </el-form-item>
                </el-col>

                <el-col :span="6">
                  <el-form-item
                    label-width="80px"
                    label="Important:"
                    class="postInfo-container-item"
                  >
                    <el-rate
                      v-model="postForm.importance"
                      :max="3"
                      :colors="['#99A9BF', '#F7BA2A', '#FF9900']"
                      :low-threshold="1"
                      :high-threshold="3"
                      style="margin-top:8px;"
                    />
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>

        <el-form-item style="margin-bottom: 40px;" label-width="80px" label="Summary:">
          <el-input
            v-model="postForm.content_short"
            :rows="1"
            type="textarea"
            class="article-textarea"
            autosize
            placeholder="Please enter the content"
          />
          <span v-show="contentShortLength" class="word-counter">{{ contentShortLength }} word</span>
        </el-form-item>

        <el-form-item prop="content" style="margin-bottom: 30px;">
          <Tinymce ref="editor" v-model="postForm.content" :height="400" />
        </el-form-item>

        <el-form-item prop="cover_url" style="margin-bottom: 30px;">
          <Upload v-model="postForm.cover_url" />
        </el-form-item>
      </div>
    </el-form> -->
  </div>
</template>

<script>
// import Tinymce from '@/components/Tinymce';
import Upload from '@/components/Upload/SingleImage';
import MDinput from '@/components/MDinput';
import Sticky from '@/components/Sticky'; // Sticky header
import { validURL } from '@/utils/validate';
import { fetchComic, createComic, updateComic } from '@/api/comic';
import Resource from '@/api/resource';
import _ from 'lodash';
const authorResource = new Resource('authors');
const genreResource = new Resource('genres');
const tagResource = new Resource('tags');
// import { userSearch } from '@/api/search';
// import {
//   CommentDropdown,
//   PlatformDropdown,
//   SourceUrlDropdown,
// } from './Dropdown';
const defaultPageForm = {
  image_url: '',
  config: '',
  scene: '',
  section: 0,
};
const defaultChapterForm = {
  image_url: '',
  chapter: 0,
  token_price: 0,
  token_price_ar: 0,
  // release_date: (new Date()).toLocaleDateString('id-ID'),
  favorites_count: 0,
  views: 0,
  pages: [
    defaultPageForm,
  ],
};
const defaultComicForm = {
  is_draft: false,
  author_split: {},
  authors: [],
  title: '',
  content: '',
  content_short: '',
  source_uri: '',
  cover_url: '',
  display_time: undefined,
  id: undefined,
  platforms: ['a-platform'],
  comment_disabled: false,
  importance: 0,
  chapters: [
    defaultChapterForm,
  ],
};

export default {
  name: 'ComicDetail',
  components: {
    // Tinymce,
    MDinput,
    Upload,
    Sticky,
    // CommentDropdown,
    // PlatformDropdown,
    // SourceUrlDropdown,
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
      postForm: Object.assign({}, defaultComicForm),
      loading: false,
      authorListOptions: [],
      genreListOptions: [],
      tagListOptions: [],
      rules: {
        cover_url: [{ validator: validateRequire }],
        title: [{ validator: validateRequire }],
        content: [{ validator: validateRequire }],
        source_uri: [{ validator: validateSourceUri, trigger: 'blur' }],
      },
      tempRoute: {},
      chapterAccordion: {
        0: false,
      },
      pageAccordion: {
        0: {
          0: false,
        },
      },
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
    this.getAuthorList();
    this.getGenreList();
    this.getTagList();
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
    } else {
      this.postForm = Object.assign({}, defaultComicForm);
    }

    // Why need to make a copy of this.$route here?
    // Because if you enter this page and quickly switch tag, may be in the execution of the setTagsViewTitle function, this.$route is no longer pointing to the current page
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    lodashFind(a, b){
      return _.find(a, b);
    },
    authorChange(){
      // const tempObj = {};
      // this.postForm.authors.forEach((auth) => {
      //   tempObj[auth] = 0;
      // });
      // this.postForm.author_split = tempObj;
    },
    tester(){
      console.log(JSON.stringify(this.postForm));
    },
    removeChapter(index){
      this.postForm.chapters.splice(index, 1);
    },
    addChapterAfter(index){
      this.postForm.chapters.splice(index + 1, 0, Object.assign({}, defaultChapterForm));

      const tempAccordion = Object.assign({}, this.chapterAccordion);
      tempAccordion[index + 1] = false;
      this.chapterAccordion = tempAccordion;

      const tempPageAccordion = Object.assign({}, this.pageAccordion);
      tempPageAccordion[index + 1] = { 0: false };
      this.pageAccordion = tempPageAccordion;
    },
    removePage(index, pindex){
      this.postForm.chapters[index].pages.splice(pindex, 1);
    },
    addPageAfter(index, pindex){
      this.postForm.chapters[index].pages.splice(pindex + 1, 0, Object.assign({}, defaultPageForm));

      const tempAccordion = Object.assign({}, this.pageAccordion);
      if (!_.has(tempAccordion, index)){
        tempAccordion[index + 1] = {};
      }
      tempAccordion[index][pindex + 1] = false;
      this.pageAccordion = tempAccordion;
    },
    togglePageAccordion(cptIdx, pageIdx){
      const tempAccordion = Object.assign({}, this.pageAccordion);
      tempAccordion[cptIdx][pageIdx] = !tempAccordion[cptIdx][pageIdx];
      this.pageAccordion = tempAccordion;
      console.log(this.pageAccordion);
    },
    toggleChapterAccordion(cptIdx){
      const tempAccordion = Object.assign({}, this.chapterAccordion);
      tempAccordion[cptIdx] = !tempAccordion[cptIdx];
      this.chapterAccordion = tempAccordion;
      console.log(this.chapterAccordion);
    },
    fetchData(id) {
      fetchComic(id)
        .then(response => {
          this.postForm = response.data;
          this.postForm.chapters.forEach((el, idx) => {
            this.chapterAccordion[idx] = false;
            this.pageAccordion[idx] = {};
            el.pages.forEach((pel, pidx) => {
              this.pageAccordion[idx][pidx] = false;
            });
          });
          this.postForm.authors = this.postForm.authors.map((el) => {
            return el.id;
          });
          this.postForm.genres = JSON.parse(this.postForm.genres);
          this.postForm.tags = JSON.parse(this.postForm.tags);
          this.postForm.author_split = JSON.parse(this.postForm.author_split);
          this.postForm.is_draft = false;
          // Just for test
          // this.postForm.title += `   Article Id:${this.postForm.id}`;
          // this.postForm.content_short += `   Article Id:${this.postForm.id}`;

          // Set tagsview title
          this.setTagsViewTitle();
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
    submitForm(draft = false) {
      this.postForm.is_draft = draft ? 1 : 0;
      if (this.isEdit){
        updateComic(this.postForm.id, this.postForm)
          .then((response) => {
            this.$router.push('/comic');
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        createComic(this.postForm)
          .then((response) => {
            this.$router.push('/comic');
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
      // if (
      //   this.postForm.content.length === 0 ||
      //   this.postForm.title.length === 0
      // ) {
      //   this.$message({
      //     message: 'Please enter required title and content',
      //     type: 'warning',
      //   });
      //   return;
      // }
      // this.$message({
      //   message: 'Successfully saved',
      //   type: 'success',
      //   showClose: true,
      //   duration: 1000,
      // });
      this.postForm.is_draft = true;
      this.submitForm();
    },
    getTagList(query) {
      tagResource.list().then(response => {
        if (!response.data.items) {
          return;
        }
        this.tagListOptions = response.data.items;
      });
    },
    getGenreList(query) {
      genreResource.list().then(response => {
        if (!response.data.items) {
          return;
        }
        this.genreListOptions = response.data.items;
      });
    },
    getAuthorList(query) {
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
