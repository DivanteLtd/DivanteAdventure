<template>
  <tr>
    <template v-if="loading">
      <td colspan="10">
        <v-progress-linear height="6" indeterminate/>
      </td>
    </template>
    <template v-else>
      <td class="pa-1" @click="rowClicked">
        <th class="pa-0">
          <v-avatar class="avatar" v-if="item.photo !== ''">
            <img
              v-if="item.photo !== ''"
              :src="item.photo"
              :alt="item.name"
              :title="item.name"
              @error="item.photo = ''"
              class="photo mr-2"/>
          </v-avatar>
          <v-avatar v-else class="mr-2">
            <v-icon large>perm_identity</v-icon>
          </v-avatar>
        </th>
        <th class="pa-0 free-day tribe-employee__th--avatar">
          <v-tooltip v-if="item.freeToday" right>
            <template v-slot:activator="{ on }">
              <v-chip v-on="on" color="red" dark outlined>
                <v-icon>work_off</v-icon>
              </v-chip>
            </template>
            {{ $t('Not available today') }}
          </v-tooltip>
        </th>
      </td>
      <td class="centered pa-0" @click="rowClicked" style="text-align: left">
        {{ item.lastName }} {{ item.name }}
      </td>
      <td v-if="isTribeMaster" class="centered" @click="rowClicked">
        <employee-list-contract-info :employee="item"/>
      </td>
      <td class="centered" @click="rowClicked">
        <span v-if="item.level && item.position">
          {{ item.level.name }} {{ item.position.name }}
        </span>
        <span v-else-if="item.position">
          {{ item.position.name }}
        </span>
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.phone }}
      </td>
      <td v-if="isTribeMaster" class="centered">
        <v-btn icon @click.stop="deleteDialogVisible = true"><v-icon>delete</v-icon></v-btn>
        <confirm-dialog v-model="deleteDialogVisible"
                        v-if="deleteDialogVisible"
                        @yes="unassign"
                        :question="$t('confirmation_question', item)"
                        yes-color="red"/>
      </td>
    </template>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapGetters } from 'vuex';
  import EmployeeListContractInfo from '../../employees/EmployeeListContractInfo';
  import ConfirmDialog from '../../utils/ConfirmDialog';

  export default {
    name: 'TribeEmployeeListEntry',
    components: { EmployeeListContractInfo, ConfirmDialog },
    props: {
      item: { type: Object, required: true },
      canView: { type: Boolean, required: true },
    },
    data() { return {
      loading: false,
      deleteDialogVisible: false,
    };},
    computed: {
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
      }),
    },
    methods: {
      rowClicked() {
        EventBus.$emit(eventNames.showEmployeeWindow, this.item);
      },
      async unassign() {
        const text = this.$t('Person has been deleted from tribe');
        try {
          await this.$store.dispatch('Employees/deleteFromTribe', {
            idEmployee: this.item.id,
            idTribe: this.item.tribe.id,
          });
          await EventBus.$emit(eventNames.refreshTribeWindow, this.item.tribe);
          this.$store.commit('showSnackbar', {
            text,
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Person cannot be deleted from tribe'),
            color: 'error',
          });
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.deleteEmployeeAfter, () => {
        this.loading = false;
      });
    },
    i18n: { messages: {
      pl: {
        'Not available today': 'Dziś niedostępny',
        'Person has been deleted from tribe': 'Osoba została usunięta z plemienia',
        'Person cannot be deleted from tribe': 'Nie udało się usunąć osoby z plemienia',
        'confirmation_question': 'Czy na pewno chcesz usunąć osobę {name} {lastName}?',
      },
      en: {
        confirmation_question: 'Do you really want to delete {name} {lastName}?',
      },
    } },
  };
</script>

<style scoped>
  .photo {
    text-align: center;
    mix-blend-mode: multiply;
    width: 48px;
    max-width: 48px;
  }
  .tribe-employee__th--avatar{
    border-bottom: none !important;
  }
  td.centered {
    text-align: center;
    cursor: pointer;
  }
  .avatar{
    display: flex;
    width: auto !important;
  }
  .free-day span{
    margin: 0;
  }
</style>
