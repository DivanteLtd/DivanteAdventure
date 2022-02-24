<template>
  <v-list-item @click="showEmployeeWindow" class="my-1">
    <v-list-item-action>
      <v-avatar v-if="employee.photo">
        <img v-if="employee.photo !== ''"
             :src="employee.photo"
             @error="employee.photo = ''"/>
      </v-avatar>
      <v-avatar v-else>
        <v-icon large>perm_identity</v-icon>
      </v-avatar>
    </v-list-item-action>
    <v-list-item-content class="ml-2">
      <v-list-item-title :class="{'employee-name': $vuetify.breakpoint.xs}">
        {{ employee.name }} {{ employee.lastName }}
      </v-list-item-title>
    </v-list-item-content>
    <v-tooltip v-if="canEditUnassign" :class="{'icons': $vuetify.breakpoint.xs}" left>
      <template v-slot:activator="{ on }">
        <v-btn :loading="editInProgress"
               @click.stop="edit"
               v-on="on"
               icon>
          <v-icon>edit</v-icon>
        </v-btn>
      </template>
      {{ $t('Edit') }}
    </v-tooltip>
    <v-tooltip v-if="canEditUnassign" :class="{'icons': $vuetify.breakpoint.xs}" left>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" @click.stop="dialogDelete = true" icon>
          <v-icon>delete</v-icon>
        </v-btn>
      </template>
      {{ $t('Unassign from project') }}
    </v-tooltip>
    <confirm-dialog
      v-model="dialogDelete"
      :width="400"
      :title="this.$t('Unassign all project periods from person')"
      :question="this.$t('Are you sure to unassign all project ' +
        'periods from person? All history of entries will be deleted')"
      yes-color="red"
      @yes="unassign"
      @no="dialogDelete = false"
    />
  </v-list-item>
</template>

<script>
  import { mapGetters } from 'vuex';
  import ConfirmDialog from '../../utils/ConfirmDialog.vue';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'EmployeeRow',
    components: { ConfirmDialog },
    props: {
      employee: { type: Object, required: true },
    },
    data() {
      return {
        dialogDelete: false,
        unassignInProgress: false,
        editInProgress: false,
      };
    },
    computed: {
      ...mapGetters({
        canEditUnassign: 'Authorization/isManager',
      }),
    },
    watch: {
      employee() {
        this.unassignInProgress = false;
      },
    },
    methods: {
      showEmployeeWindow() {
        EventBus.$emit(eventNames.showEmployeeWindow, this.employee);
      },
      async unassign() {
        this.unassignInProgress = true;
        await this.$store.dispatch('Employees/deletePairings', this.employee.pairingId);
        this.unassignInProgress = false;
        EventBus.$emit(eventNames.refreshProjectWindow);
      },
      edit() {
        const data = {
          isEdit: true,
          employeeId: this.employee.id,
          pairingId: this.employee.pairingId,
        };
        EventBus.$emit(eventNames.editEmployeeProject, data);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Unassign all project periods from person': 'Odłącz wszystkie okresy projektu od osoby',
          'Are you sure to unassign all project periods from person? All history of entries will be deleted':
            'Czy na pewno chcesz odłączyć wszystkie okresy projektu od osoby? Cała historia wpisów zostanie usunięta',
          'Unassign from project': 'Usuń z projektu',
          'Edit': 'Edytuj',
          'Back': 'Cofnij',
          'Delete': 'Usuń',
        },
      },
    },
  };
</script>
<style scoped>
  .employee-name {
    font-size: small;
  }
  .icons {
    margin-right: -5px;
  }
</style>
