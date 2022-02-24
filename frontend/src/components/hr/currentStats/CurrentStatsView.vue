<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col lg="4" sm="12" cols="12">
        <age-card :stats="stats" class="ma-2"/>
      </v-col>
      <v-col lg="4" sm="12" cols="12">
        <remote-card :stats="stats" class="ma-2"/>
      </v-col>
      <v-col lg="4" sm="12" cols="12">
        <cooperation-card :stats="stats" class="ma-2"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import AgeCard from './AgeCard';
  import RemoteCard from './RemoteCard';
  import CooperationCard from './CooperationCard';

  export default {
    name: 'CurrentStatsView',
    components: { CooperationCard, RemoteCard, AgeCard },
    props: {
      stats: { type: Object, required: true },
    },
  };
</script>
