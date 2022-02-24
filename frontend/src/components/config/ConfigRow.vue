<template>
  <tr>
    <template v-if="loading">
      <td colspan="6">
        <v-progress-linear height="6" indeterminate/>
      </td>
    </template>
    <template v-else>
      <td>{{ item.group_i18n }}</td>
      <td>{{ item.name_i18n }}</td>
      <template v-if="item.responsible">
        <td>{{ displayValue }}</td>
        <td>{{ createdAt }}</td>
        <td><employee-chip :employee="item.responsible"/></td>
      </template>
      <template v-else-if="item.value">
        <td>{{ displayValue }}</td>
        <td>{{ createdAt }}</td>
        <td><i>N/A</i></td>
      </template>
      <template v-else>
        <td><i>{{ $t('Not set') }}</i></td>
        <td><i>N/A</i></td>
        <td><i>N/A</i></td>
      </template>
      <td>
        <config-history-dialog v-if="history.length > 0" v-model="history"/>
        <config-edit-dialog v-if="editDialogVisible"
                            v-model="editDialogVisible"
                            :config-key="item.key"
                            :config-value="item.value"
                            :display-group="item.group_i18n"
                            :display-key="item.name_i18n"/>
        <v-tooltip right>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="editDialogVisible = true" icon><v-icon>edit</v-icon></v-btn>
          </template>
          {{ $t('Edit') }}
        </v-tooltip>
        <v-tooltip right>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on"
                   :loading="historyLoading"
                   @click="showHistory"
                   :disabled="!item.createdAt"
                   icon>
              <v-icon>timeline</v-icon>
            </v-btn>
          </template>
          {{ $t('See history') }}
        </v-tooltip>
      </td>
    </template>
  </tr>
</template>

<script>
  import EmployeeChip from '../utils/EmployeeChip';
  import moment from '@divante-adventure/work-moment';
  import ConfigEditDialog from './ConfigEditDialog';
  import ConfigHistoryDialog from './ConfigHistoryDialog';

  const MAX_VAL_LENGTH = 50;

  export default {
    name: 'ConfigRow',
    components: { ConfigHistoryDialog, ConfigEditDialog, EmployeeChip },
    props: {
      item: { type: Object, required: true },
    },
    data() {
      return {
        loading: false,
        editDialogVisible: false,
        historyLoading: false,
        history: [],
      };
    },
    computed: {
      displayValue() {
        const { value } = this.item;
        if (value.length > MAX_VAL_LENGTH) {
          const substring = value.substring(0, MAX_VAL_LENGTH - 3);
          return `${substring}...`;
        }
        return value;
      },
      createdAt() {
        return moment.unix(this.item.createdAt).format('D MMM YYYY HH:mm:ss');
      },
    },
    methods: {
      async showHistory() {
        this.historyLoading = true;
        this.history = await this.$store.state.apiClient.config.getConfigHistory(this.item.key);
        this.historyLoading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Not set': 'Nie ustawiono',
          'Edit': 'Edytuj',
          'See history': 'Zobacz historię',
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
