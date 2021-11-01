<template>
  <author-detail :is-edit="false" />
</template>

<script>
import AuthorDetail from './components/AuthorDetail';

export default {
  name: 'CreateForm',
  components: { AuthorDetail },
};
</script>

