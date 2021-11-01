<template>
  <tag-detail :is-edit="true" />
</template>

<script>
import TagDetail from './components/TagDetail';

export default {
  name: 'EditForm',
  components: { TagDetail },
};
</script>

