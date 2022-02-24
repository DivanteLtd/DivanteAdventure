<template>
  <v-list-item>
    <v-list-item-content>
      <v-list-item-title>
        {{ position.name }}
        <template v-if="positionCount[position.name]">
          ({{ positionCount[position.name] }})
        </template>
      </v-list-item-title>
      <v-list-item-subtitle v-if="levels !== ''">{{ levels }}</v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  export default {
    name: 'TribePositionRow',
    props: {
      position: { type: Object, required: true },
      positionCount: { type: Object, required: true },
    },
    computed: {
      levels() {
        return (this.position.strategy.levels || [])
          .sort((a, b) => a.priority - b.priority)
          .map(level => level.name)
          .join(', ');
      },
    },
  };
</script>
