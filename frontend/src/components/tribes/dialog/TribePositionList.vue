<template>
  <v-list two-line>
    <tribe-position-row v-for="(position, index) in positions"
                        :key="index"
                        :position="position"
                        :position-count="positionsCount"/>
  </v-list>
</template>

<script>
  import TribePositionRow from './TribePositionRow';

  export default {
    name: 'TribePositionList',
    components: { TribePositionRow },
    props: {
      positions: { type: Array, required: true },
      positionsCount: { type: Object, required: true },
    },
  };
</script>
