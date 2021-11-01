<template>
  <genre-detail :is-edit="false" />
</template>

<script>
import GenreDetail from './components/GenreDetail';

export default {
  name: 'CreateForm',
  components: { GenreDetail },
};
</script>

