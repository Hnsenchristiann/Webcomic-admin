<template>
  <comic-detail :is-edit="true" />
</template>

<script>
import ComicDetail from './components/ComicDetail';

export default {
  name: 'EditForm',
  components: { ComicDetail },
};
</script>

