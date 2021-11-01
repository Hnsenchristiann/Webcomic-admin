<template>
  <div>
    <input ref="testfile" multiple type="file" @change="fileChange()">
    <button @click="testing">asdf</button>
  </div>
</template>

<script>
export default {
  data(){
    return {
      files: null,
    };
  },
  methods: {
    testing(){
      console.log(this.files);
      console.log(JSON.stringify({
        hello: 'hello',
        hi: this.files,
      }));
    },
    fileChange(){
      this.files = this.$refs.testfile.files[0];
    },
  },
};
</script>
