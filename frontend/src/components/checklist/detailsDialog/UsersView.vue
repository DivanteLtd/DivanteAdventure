<template>
  <v-list id="checklist-details-users-view">
    <single-user-row v-if="checklist.subject" :label="$t('Subject:')" :employee="checklist.subject[0]"/>
    <v-divider/>
    <multi-user-row v-if="checklist.owners" :label="$t('Owners:')" :employees="checklist.owners"/>
    <v-divider/>
    <v-list-item class="single-row-flex-top">
      <div class="mr-1">{{ $t('Type:') }}</div>
      <div>{{ type }}</div>
      <v-spacer/>
      <div class="mr-1">{{ $t('Due date:') }}</div>
      <div>{{ formatDate }}</div>
    </v-list-item>
  </v-list>
</template>

<script>
  import SingleUserRow from './SingleUserRow';
  import MultiUserRow from './MultiUserRow';
  import { ChecklistType } from '../../../util/checklists';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'UsersView',
    components: { SingleUserRow, MultiUserRow },
    props: {
      /** @type {Checklist} */
      checklist: { type: Object, required: true },
    },
    computed: {
      formatDate() {
        return moment.unix(this.checklist.dueDate).format('DD-MM-YYYY');
      },
      type() {
        if (this.checklist.type === ChecklistType.united) {
          return this.$t('United');
        } else {
          return this.$t('Distributed');
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Subject:': 'Podmiot:',
          'Due date:': 'Termin realizacji:',
          'Owners:': 'Właściciele:',
          'Type:': 'Typ:',
          'United': 'Złączona',
          'Distributed': 'Rozdzielona',
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  #checklist-details-users-view {
    border-right: 1px solid rgba(0, 0, 0, 0.12);
    width: 100%;
  }

  .single-row-flex-top >>> .v-list__tile {
    display: flex;
    flex-flow: row wrap;
    justify-content: space-between;
    width: 100%;
  }
</style>
