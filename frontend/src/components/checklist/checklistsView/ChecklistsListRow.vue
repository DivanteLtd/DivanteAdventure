<template>
  <tr>
    <template v-if="!loading">
      <td style="min-width: 20em;" @click="checklistDetailsDialog" class="pointer">
        {{ item[$t('name-key')] }}
      </td>
      <td v-if="!reduced" @click="checklistDetailsDialog" class="pointer">
        <v-chip v-if="item.owners && item.owners.length > 1">
          <v-avatar>
            <v-icon>group</v-icon>
          </v-avatar>
          <span class="pl-2">
            {{ $t('Many people') }}
          </span>
        </v-chip>
        <employee-chip v-else-if="item.owners" :employee="item.owners[0]"/>
      </td>
      <td @click="checklistDetailsDialog" class="pointer">
        <employee-chip v-if="item.subject" :employee="item.subject[0]"/>
      </td>
      <td @click="checklistDetailsDialog" class="pointer">
        {{ formatDate(item.dueDate) }}
      </td>
      <td v-if="!reduced" @click="checklistDetailsDialog" class="pointer">
        {{ formatDate(item.startedAt) }}
      </td>
      <td v-if="!reduced" @click="checklistDetailsDialog" class="pointer">
        {{ item.finishedAt ? formatDate(item.finishedAt) : "" }}
      </td>
      <td @click="checklistDetailsDialog" class="pointer">
        <colored-progress-bar :max="item.tasksAllCount" :value="item.tasksFinishedCount"/>
        <span>{{ item.tasksFinishedCount }} / {{ item.tasksAllCount }}</span>
      </td>
      <td v-if="!reduced && isHr" class="centered">
        <v-tooltip right>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="dialogDelete = true" icon>
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ $t('Delete') }}
        </v-tooltip>
      </td>
    </template>
    <template v-else>
      <td :colspan="reduced ? 4 : 10">
        <v-progress-linear height="6" indeterminate/>
      </td>
    </template>
    <confirm-dialog v-model="dialogDelete"
                    v-if="dialogDelete"
                    @yes="deleteChecklist"
                    :question="$t('confirm-delete')"
                    yes-color="red"/>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import EmployeeChip from '../../utils/EmployeeChip';
  import ColoredProgressBar from '../../utils/ColoredProgressBar';
  import ConfirmDialog from '../../utils/ConfirmDialog';
  import { mapGetters } from 'vuex';

  export default {
    name: 'ChecklistListRow',
    components: { ColoredProgressBar, EmployeeChip, ConfirmDialog },
    props: {
      item: { type: Object, required: true },
      reduced: { type: Boolean, default: false },
    },
    data() {
      return {
        loading: false,
        dialogDelete: false,
      };
    },
    computed: {
      ...mapGetters({
        isHr: 'Authorization/isHr',
      }),
    },
    methods: {
      checklistDetailsDialog() {
        EventBus.$emit(eventNames.showChecklistDetails, this.item.id);
      },
      formatDate(date) {
        return moment.unix(date).format('DD-MM-YYYY');
      },
      async deleteChecklist() {
        this.$emit('loading');
        await this.$store.dispatch('Checklist/deleteChecklist', this.item.id);
        this.$store.commit('showSnackbar', {
          text: this.$t('Checklist has been deleted'),
          color: 'success',
        });
        await this.$store.dispatch('Checklist/getAllChecklists');
        this.$emit('loading');
      },
    },
    i18n: {
      messages: {
        pl: {
          'name-key': 'namePl',
          'confirm-delete': 'Czy na pewno chcesz usunąć tą checklistę?',
          'Checklist has been deleted': 'Checklista została usunięta',
          'Delete': 'Usuń',
          'Many people': 'Wiele osób',
        },
        en: {
          'name-key': 'nameEn',
          'confirm-delete': 'Are you sure you want to delete this checklist?',
        },
      },
    },
  };
</script>
<style scoped>
    td{
        text-align: center;
    }
    .pointer{
        cursor: pointer;
    }
</style>
