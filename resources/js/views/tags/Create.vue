<template>
  <tag-detail :is-edit="false" />
</template>

<script>
import TagDetail from './components/TagDetail';

export default {
  name: 'CreateForm',
  components: { TagDetail },
};
</script>

