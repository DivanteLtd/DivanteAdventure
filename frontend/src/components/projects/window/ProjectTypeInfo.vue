<template>
  <v-list-item v-if="type !== '' || budget !== ''">
    <v-list-item-action>
      <v-icon v-if="type !== ''">category</v-icon>
      <v-icon v-else>watch_later</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title v-if="type !== ''">{{ type }}</v-list-item-title>
      <v-list-item-title v-else>{{ budget }}</v-list-item-title>
      <v-list-item-subtitle v-if="type !== ''">{{ budget }}</v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  const TYPE_IMPLEMENTATION = 1;
  const TYPE_MAINTENANCE = 2;

  export default {
    name: 'ProjectTypeInfo',
    props: {
      project: { type: Object, required: true },
    },
    computed: {
      type() {
        switch (this.project.project_type) {
          case TYPE_IMPLEMENTATION: return this.$t('Implementation');
          case TYPE_MAINTENANCE: return this.$t('Maintenance');
          default: return '';
        }
      },
      budget() {
        if (this.project.planned_budget > 0) {
          return `${this.$t('Budget of project per month')}: ${this.project.planned_budget}h`;
        } else {
          return '';
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Implementation': 'Wdrożenie',
          'Maintenance': 'Utrzymanie',
          'Budget of project per month': 'Budżet projektu miesięcznie',
        },
      },
    },
  };
</script>
