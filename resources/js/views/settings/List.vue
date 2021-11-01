<template>
  <div style="padding: 15px;">
    <el-card style="font-size: 14px;">
      <el-tabs v-model="activeName" style="margin: 10px;" @tab-click="handleClick">
        <el-tab-pane label="Banners" name="zeroth">
          <el-button style="margin-top: 5px;" type="primary" icon="el-icon-circle-plus-outline" @click="addBanner">Add banners</el-button>
          <el-card v-for="(item, index) in banners" :key="'social-' + index" style="margin-top: 15px; width: 420px;">
            <el-input
              v-model="banners[index].title"
              placeholder="Title"
              style="width: 250px;"
              clearable
            />
            <el-select style="margin-top: 10px;" v-model="banners[index].route.params.comic" placeholder="Link banner to comic">
              <el-option
                v-for="(comicOption, idx) in comicOptions"
                :key="'comix-' + idx"
                :label="comicOption.title"
                :value="comicOption.id"
              />
            </el-select>
            <!-- add route params here -->
            <Upload style="margin-top: 15px;" v-model="banners[index].image" />
            <el-button type="danger" style="margin-top: 10px;" @click="deleteBanner(index)">Delete</el-button>
          </el-card>
          <el-button type="success" style="margin-top: 15px;" @click="saveSetting('banners')">Save</el-button>
        </el-tab-pane>
        <el-tab-pane label="Dashboard Tags" name="first">
          <el-button style="margin-top: 5px;" type="primary" icon="el-icon-circle-plus-outline" @click="addTags">Add tags</el-button>
          <el-card v-for="(item, index) in tags" :key="'social-' + index" style="margin-top: 15px; width: 420px;">
            <div style="margin-bottom: 10px; font-weight: bold;">Tags Name</div>
            <el-input
              v-model="tags[index]"
              placeholder="Input Tags"
              style="width: 250px;"
              clearable
            />
            <el-button type="danger" style="margin-left: 10px;" @click="deleteTag(index)">Delete</el-button>
          </el-card>
          <el-button type="success" style="margin-top: 15px;" @click="saveSetting('tags')">Save</el-button>
        </el-tab-pane>
        <el-tab-pane label="Prices" name="second">
          <el-button style="margin-top: 5px;" type="primary" icon="el-icon-circle-plus-outline" @click="addPrices">Add Prices</el-button>
          <el-card v-for="(item, index) in prices" :key="'social-' + index" style="margin-top: 15px; width: 670px;">
            <div style="display: flex;">
              <div style="margin-bottom: 10px; font-weight: bold;">Token Price</div>
              <div style="margin-bottom: 10px; font-weight: bold; margin-left: 180px;">Token Amount</div>
            </div>
            <el-input
              v-model="prices[index].price"
              placeholder="Input Price"
              style="width: 250px;"
              clearable
            />
            -
            <el-input
              v-model="prices[index].amount"
              placeholder="Input Amount"
              style="width: 250px;"
              clearable
            />
            <el-button type="danger" style="margin-left: 10px;" @click="deletePrice(index)">Delete</el-button>
          </el-card>
          <el-button type="success" style="margin-top: 15px;" @click="saveSetting('prices')">Save</el-button>
        </el-tab-pane>
        <el-tab-pane label="Social Media" name="third">
          <el-button style="margin-top: 5px;" type="primary" icon="el-icon-circle-plus-outline" @click="addNewSocialMedia">Add Social Media</el-button>
          <div>
            <el-card v-for="(social, index) in social_media_links" :key="'social-' + index" style="margin-top: 20px; width: 500px;">
              <div>
                <MDinput v-model="social_media_links[index].name" style="width: 350px;">
                  Social Media Name
                </MDinput>
                <el-input
                  v-model="social_media_links[index].link"
                  placeholder="Please input"
                  clearable
                  style="width: 350px; padding-top: 15px;"
                />
              </div>
              <el-button type="danger" style="margin-top: 10px;" @click="deleteSocialMedia(index)">Delete</el-button>
            </el-card>
            <el-button type="success" style="margin-top: 15px;" @click="saveSetting('social')">Save</el-button>
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>
    <el-button type="success" style="margin-top: 15px; float:right;" @click="saveSetting(null)">Save All</el-button>
  </div>
</template>

<script>
import MDinput from '@/components/MDinput';
import Upload from '@/components/Upload/SingleImage';
import Resource from '@/api/resource';
const comicResource = new Resource('comics');
const settingResource = new Resource('settings');
export default {
  name: 'SettingsList',
  components: {
    MDinput,
    Upload,
  },
  data() {
    return {
      comicOptions: [
        {
          id: null,
          title: '',
        },
      ],
      activeName: 'first',
      social_media_links: [{
        name: '',
        link: '',
      }],
      prices: [{
        price: '',
        amount: '',
      }],
      tags: [''],
      banners: [
        {
          title: '',
          route: {
            name: 'web.comic',
            params: { comic: null },
          },
          image: '',
        },
      ],
      comicQuery: {
        select: ['id', 'title'],
        just_comics: true,
      },
    };
  },
  created(){
    settingResource.get('dashboard.tags')
      .then((resp) => {
        this.tags = resp;
      });
    settingResource.get('dashboard.banners')
      .then((resp) => {
        this.banners = resp;
      });
    settingResource.get('token.prices')
      .then((resp) => {
        this.prices = resp;
      });
    settingResource.get('site.social_media_links')
      .then((resp) => {
        this.social_media_links = [];
        Object.keys(resp).forEach((smKey) => {
          this.social_media_links.push({
            name: smKey,
            link: resp[smKey],
          });
        });
      });
    this.getComics();
  },
  methods: {
    saveSetting(type = null){
      switch (type){
        case 'social': {
          const obj = this.social_media_links.reduce((acc, smObj) => {
            acc[smObj.name] = smObj.link;
            return acc;
          }, {});
          settingResource.update('site.social_media_links', { values: obj });
          break;
        }
        case 'banners':
          settingResource.update('dashboard.banners', { values: this.banners });
          break;
        case 'tags':
          settingResource.update('dashboard.tags', { values: this.tags });
          break;
        case 'prices':
          this.prices = this.prices.map((price) => {
            const priceObj = {};
            if ('special_tag' in price){
              priceObj.special_tag = price.special_tag;
            }
            priceObj.price = +price.price;
            priceObj.amount = +price.amount;
            return priceObj;
          });
          settingResource.update('token.prices', { values: this.prices });
          break;
        default:
          this.saveSetting('social');
          this.saveSetting('banners');
          this.saveSetting('tags');
          this.saveSetting('prices');
          break;
      }
    },
    async getComics(){
      const { data } = await comicResource.list(this.comicQuery);
      this.comicOptions = data.items;
    },
    addBanner(){
      this.banners.push({
        title: '',
        route: {
          name: 'web.comic',
          params: { comic: null },
        },
        image: '',
      });
    },
    deleteBanner(index){
      this.banners.splice(index, 1);
    },
    addNewSocialMedia(){
      this.social_media_links.push({
        name: '',
        link: '',
      });
    },
    deleteSocialMedia(index){
      this.social_media_links.splice(index, 1);
    },
    addPrices(){
      this.prices.push({
        price: 0,
        amount: 0,
      });
    },
    deletePrice(index){
      this.prices.splice(index, 1);
    },
    addTags(){
      this.tags.push({
        tags: '',
      });
    },
    deleteTag(index){
      this.tags.splice(index, 1);
    },
    handleClick(tab, event) {
      console.log(tab, event);
    },
  },
};
</script>
