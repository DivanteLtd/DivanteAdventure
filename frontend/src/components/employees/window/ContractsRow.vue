<template>
  <tr>
    <td class="centered">
      {{ item.contractType }}
    </td>
    <td class="centered">
      {{ item.startDate }}
    </td>
    <td class="centered">
      {{ item.endDate }}
    </td>
    <td class="centered">
      <div v-if="item.hasOwnProperty('noticePeriod')">
        {{ item.noticePeriod }} {{ $t('days') }}
      </div>
      <div v-else>
        {{ $t('Compliant with labour law regulation') }}
      </div>
    </td>
    <td class="icons">
      <v-spacer/>
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="edit()" icon>
            <v-icon>edit</v-icon>
          </v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="deleteDialogVisible = true" icon>
            <v-icon>delete</v-icon>
          </v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
      <confirm-dialog v-if="deleteDialogVisible"
                      v-model="deleteDialogVisible"
                      @yes="deleteContract"
                      :question="$t('Are you sure you want to delete this contract?')"
                      yes-color="red"/>
    </td>
  </tr>
</template>

<script>

  import ConfirmDialog from '../../utils/ConfirmDialog';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'ContractsRow',
    components: { ConfirmDialog },
    props: {
      item: { type: Object, required: true },
    },
    data() {
      return {
        deleteDialogVisible: false,
      };
    },
    methods: {
      async deleteContract() {
        try {
          await this.$store.dispatch('Employees/deleteContract', this.item.id);
          this.$store.dispatch('Employees/getContracts', this.item.employeeId);
        } catch (e) {
          throw e;
        }
      },
      edit() {
        EventBus.$emit(eventNames.editContract, this.item);
      },
    },
    i18n: {
      messages: {
        pl: {
          'days': 'dni',
          'Are you sure you want to delete this contract?': 'Czy chcesz usunąć tą umowę?',
          'Delete': 'Usuń',
          'Edit': 'Edytuj',
          'Compliant with labour law regulation': 'Zgodny z przepisami prawa pracy',
        },
      },
    },
  };
</script>
