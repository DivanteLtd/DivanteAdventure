<template>
  <v-list-item>
    <v-list-item-avatar
      :class="{'mr-3': $vuetify.breakpoint.smAndUp, 'mr-2': $vuetify.breakpoint.xs}"
      style="min-width: unset;">
      <v-img v-if="responsible.photo" :src="responsible.photo" @error="responsible.photo = ''"/>
      <v-icon v-else>perm_identity</v-icon>
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title>
        {{ responsible.name }} {{ responsible.lastName || responsible.lastName }}
      </v-list-item-title>
      <v-list-item-subtitle>{{ $t('Responsible') }}</v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action v-if="canPing">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn @click="ping" v-on="on" :loading="pingInProgress" icon >
            <v-icon>settings_input_antenna</v-icon>
          </v-btn>
        </template>
        {{ $t('Ping person to update status') }}
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';

  export default {
    name: 'TaskDetailsResponsible',
    props: {
      checklist: { type: Object, required: true },
      taskId: { type: Number, required: true },
      responsible: { type: Object, required: true },
    },
    data() {
      return {
        pingInProgress: false,
      };
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
        apiClient: state => state.apiClient,
      }),
      ...mapGetters({
        isHr: 'Authorization/isHr',
      }),
      canPing() {
        return this.isHr || (this.checklist.subject
          && this.responsible.id !== this.currentUser.id
          && this.checklist.subject[0].id === this.currentUser.id);
      },
    },
    methods: {
      async ping() {
        this.pingInProgress = true;
        try {
          await this.apiClient.checklist.pingQuestion(this.checklist.id, this.taskId);
          this.$store.commit('showSnackbar', {
            text: this.$t('Responsible person pinged!'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Responsible person was already pinged about that task today.'),
            color: 'error',
          });
        }
        this.pingInProgress = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Responsible': 'Osoba odpowiedzialna',
          'Ping person to update status': 'Poproś o aktualizację statusu',
          'Responsible person pinged!': 'Powiadomiono!',
          'Responsible person was already pinged about that task today.': 'Osoba odpowiedzialna za to zadanie została już powiadomiona',
        },
      },
    },
  };
</script>
