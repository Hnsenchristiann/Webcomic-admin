<template>
  <genre-detail :is-edit="true" />
</template>

<script>
import GenreDetail from './components/GenreDetail';

export default {
  name: 'EditForm',
  components: { GenreDetail },
};
</script>

