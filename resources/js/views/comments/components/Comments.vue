<template>
  <div>
    <comment :comment-object="myComment" @updateComment="closeSelf" />
    <div v-if="myComment.all_children_with_commenter && myComment.all_children_with_commenter.length > 0">
      <comment v-for="(innerComment, idx) in myComment.all_children_with_commenter" :key="idx" style="width:95%; margin-left: 5%;" :comment-object="innerComment" @updateComment="fetchComments(myComment.id)" />
    </div>
  </div>
</template>

<script>
import Comment from './Comment';
import Resource from '@/api/resource';
const commentResource = new Resource('comments');
export default {
  name: 'Comments',
  components: { Comment },
  props: {
    comment: {
      type: Object,
      default: () => {},
    },
  },
  data(){
    return {
      myComment: null,
    };
  },
  created(){
    this.myComment = this.comment;
  },
  methods: {
    closeSelf(){
      this.$emit('closeDialogThenUpdate');
    },
    fetchComments(id){
      commentResource.get(id)
        .then((resp) => {
          this.myComment = resp.data;
        });
    },
  },
};
</script>
