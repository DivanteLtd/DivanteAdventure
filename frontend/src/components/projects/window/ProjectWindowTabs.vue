<template>
  <v-card v-if="canEdit()" class="elevation-0">
    <v-app-bar color="transparent" flat dense>
      <v-row no-gutters wrap>
        <v-spacer/>
        <v-col cols="10" sm="10">
          <v-tabs v-model="selectedTab" centered>
            <v-tab>{{ $t('People') }}</v-tab>
            <v-tab>{{ $t('Criteria') }}</v-tab>
          </v-tabs>
        </v-col>
        <v-col cols="2" sm="1" class="tribe-more-menu">
          <v-spacer/>
          <v-menu v-if="canAssignPerson" offset-y>
            <template v-slot:activator="{ on }">
              <v-btn v-on="on" icon><v-icon>more_vert</v-icon></v-btn>
            </template>
            <v-list dense>
              <v-list-item @click="assignPerson">
                {{ $t('Assign person') }}
              </v-list-item>
              <v-list-item @click="addNewCriteria">
                {{ $t('Add new criteria') }}
              </v-list-item>
              <v-list-item @click="manageCriteria">
                {{ $t('Manage criteria') }}
              </v-list-item>
            </v-list>
          </v-menu>
        </v-col>
      </v-row>
    </v-app-bar>
    <v-divider/>
    <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
      <v-tabs-items v-model="selectedTab" touchless>
        <v-tab-item>
          <v-progress-linear height="6" v-if="loading" indeterminate/>
          <employees-tab v-else :project="project"/>
        </v-tab-item>
        <v-tab-item>
          <v-progress-linear height="6" v-if="loading" indeterminate/>
          <criteria-tab v-else :project="project"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../../../eventbus';
  import EmployeesTab from './EmployeesTab';
  import CriteriaTab from './CriteriaTab';

  export default {
    name: 'ProjectWindowTabs',
    components: { CriteriaTab, EmployeesTab },
    props: {
      project: { type: Object, required: true },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        selectedTab: 0,
        dialogVisible: false,
      };
    },
    computed: {
      ...mapGetters({
        canAssignPerson: 'Authorization/isManager',
      }),
    },
    methods: {
      addNewCriteria() {
        EventBus.$emit(eventNames.addCriteria);
      },
      assignPerson() {
        EventBus.$emit(eventNames.addEmployeeProject, this.project);
      },
      manageCriteria() {
        EventBus.$emit(eventNames.manageExistingCriteria);
      },
      canEdit() {
        if (this.project.visibility === 1) {
          return false;
        }
        return true;
      },
    },
    i18n: {
      messages: {
        pl: {
          'People': 'Osoby',
          'Criteria': 'Kryteria',
          'Assign person': 'Przypisz osobę',
          'Add new criteria': 'Dodaj nowe kryteria',
          'Manage criteria': 'Zarządzaj kryteriami',
        },
      },
    },
  };
</script>
