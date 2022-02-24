<template>
  <tr>
    <td>{{ item.name }}</td>
    <td>{{ date }}</td>
    <td>
      <v-icon v-if="item.repeating" color="green">done</v-icon>
      <v-icon v-else color="red">close</v-icon>
    </td>
    <td>
      <v-icon v-if="item.enabled" color="green">done</v-icon>
      <v-icon v-else color="red">close</v-icon>
    </td>
    <td>
      <confirm-dialog v-if="confirmDeleteVisible"
                      v-model="confirmDeleteVisible"
                      :question="$t('delete-question', [ item.name ])"
                      yes-color="red"
                      @yes="deleteRow"/>
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on"
                 :disabled="item.calculated"
                 @click="confirmDeleteVisible = true"
                 :loading="deletionLoading"
                 icon>
            <v-icon>delete</v-icon>
          </v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="switchEnabled" :loading="switchLoading" icon>
            <v-icon v-if="item.enabled">close</v-icon>
            <v-icon v-else>done</v-icon>
          </v-btn>
        </template>
        {{ item.enabled ? $t('Disable') : $t('Enable') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import ConfirmDialog from '../utils/ConfirmDialog';

  export default {
    name: 'FreeDayRow',
    components: { ConfirmDialog },
    props: {
      item: { type: Object, required: true },
    },
    data() {
      return {
        confirmDeleteVisible: false,
        deletionLoading: false,
        switchLoading: false,
      };
    },
    computed: {
      date() {
        return moment(this.item.date).format(this.$t('date.formats.dateWithWeekDay'));
      },
    },
    methods: {
      async deleteRow() {
        this.deletionLoading = true;
        await this.$store.dispatch('Config/deleteFreeDay', this.item.id);
        this.deletionLoading = false;
      },
      async switchEnabled() {
        this.switchLoading = true;
        await this.$store.dispatch('Config/switchFreeDay', this.item.id);
        this.switchLoading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Delete': 'Usuń',
          'Enable': 'Włącz',
          'Disable': 'Wyłącz',
          'delete-question': 'Czy na pewno chcesz usunąć święto "{0}?',
        },
        en: {
          'delete-question': 'Do you really want to delete holiday "{0}?"',
        },
      },
    },
  };
</script>

<style scoped>
  td {
    text-align: center;
  }
</style>
