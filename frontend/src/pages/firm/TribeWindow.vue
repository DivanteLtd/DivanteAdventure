<template>
  <v-dialog v-model="dialogVisible" width="1200">
    <v-card id="dialog-tribe-details">
      <v-app-bar color="transparent" class="headline" flat >
        <span :class="{'tribe-name-xs': $vuetify.breakpoint.xs}">{{ item.name }}</span>
        <v-spacer/>
        <v-btn v-if="role" icon @click="EventBus.$emit(eventNames.deleteTribeForm, item.id)">
          <v-icon>delete</v-icon>
        </v-btn>
        <v-btn v-if="role" icon @click="dialogTribeForm = true">
          <v-icon>edit</v-icon>
        </v-btn>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <tribe-window-content :tribe="item" :loading="loading"/>
    </v-card>
    <tribe-form v-if="dialogTribeForm" v-model="dialogTribeForm" :item="item"/>
  </v-dialog>
</template>

<script>
  import { mapState } from 'vuex';
  import { EventBus, eventNames } from '../../eventbus';
  import TribeWindowContent from '../../components/tribes/dialog/TribeWindowContent';
  import TribeForm from '../../components/tribes/TribeForm';

  export default {
    name: 'TribeWindow',
    components: { TribeForm, TribeWindowContent },
    data() {
      return {
        EventBus,
        eventNames,
        dialogVisible: false,
        dialogTribeForm: false,
        dialog: false,
        loading: false,
        employee: {},
        item: {},
      };
    },
    computed: {
      ...mapState({
        allEmployees: state => state.Employees.employees,
        allProjects: state => state.Projects.projects,
      }),
      role() {
        return this.$store.getters['Authorization/isTribeMaster'];
      },
    },
    watch: {
      dialogVisible() {
        if (this.dialogVisible) {
          window.history.pushState({}, null, `/#/firm/tribesByYear/${this.item.id}`);
        } else {
          window.history.pushState({}, null, `/#/firm/tribesByYear/`);
        }
      },
    },
    methods: {
      async show(item) {
        if (this.dialogVisible) {
          return;
        }
        this.loading = true;
        this.item = item;
        this.dialogVisible = true;
        await Promise.all([
          this.$store.dispatch('Projects/loadProjects'),
          this.$store.dispatch('Employees/loadEmployees'),
          this.$store.dispatch('Tribes/loadTribes'),
        ]);
        this.loading = false;
      },
      async refreshWindow(item) {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Projects/loadProjects'),
          this.$store.dispatch('Employees/loadEmployees'),
          this.$store.dispatch('Tribes/loadTribes'),
        ]);
        if (item !== undefined) {
          this.item = item;
        }
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showTribeWindow, this.show);
      EventBus.$on(eventNames.refreshTribeWindow, this.refreshWindow);
      EventBus.$on(eventNames.escapePressed, () => { this.dialogVisible = false; });
    },
  };
</script>
<style scoped>
  .tribe-name-xs{
    font-size: medium;
    line-height: initial;
  }
</style>
