<template>
  <comic-detail :is-edit="false" />
</template>

<script>
import ComicDetail from './components/ComicDetail';

export default {
  name: 'CreateForm',
  components: { ComicDetail },
};
</script>

