<template>
  <v-list-item>
    <v-list-item-content>
      <v-list-item-title>
        <v-tooltip right>
          <template v-slot:activator="{ on }">
            <span v-on="on">{{ question[$t('name-key')] }}</span>
          </template>
          {{ question[$t('description-key')] }}
        </v-tooltip>
      </v-list-item-title>
    </v-list-item-content>
    <v-list-item-action>
      <v-icon :color="currentStatus.color">{{ currentStatus.icon }}</v-icon>
      <v-list-item-action-text>{{ currentStatus[$t('status-key')] }}</v-list-item-action-text>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  export default {
    name: 'QuestionsListRow',
    props: {
      question: { type: Object, required: false, default: () => ({}) },
    },
    computed: {
      currentStatus() {
        return this.question.possibleStatuses[this.question.status];
      },
    },
    i18n: {
      messages: {
        pl: {
          'name-key': 'namePl',
          'description-key': 'descriptionPl',
          'status-key': 'label_pl',
        },
        en: {
          'name-key': 'nameEn',
          'description-key': 'descriptionEn',
          'status-key': 'label_en',
        },
      },
    },
  };
</script>
