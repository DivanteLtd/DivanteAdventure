<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card id="agreement-form">
      <v-row no-gutters wrap class="justify-center">
        <v-col cols="12">
          <v-app-bar color="transparent" class="headline mt-3" flat >
            {{ $t('Manage criteria') }}
            <v-spacer/>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn v-on="on" @click="dialogVisible = false" icon>
                  <v-icon>close</v-icon>
                </v-btn>
              </template>
              {{ $t('Close') }}
            </v-tooltip>
          </v-app-bar>
        </v-col>
        <v-divider/>
        <v-col cols="12" sm="6" md="4" class="pa-4">
          <v-btn block @click="addNewCriteria">
            {{ $t('Add criteria') }}
          </v-btn>
        </v-col>
      </v-row>
      <v-card-title class="title px-4">
        <span>{{ $t('List of current criteria') }}</span>
      </v-card-title>
      <v-card-text class="pa-0">
        <v-data-table mobile-breakpoint="0"
                      :items="allCriteria"
                      disable-pagination
                      hide-default-header
                      hide-default-footer>
          <template v-slot:item="{ item }">
            <project-criterion-row :item="item" :criterion="item"/>
          </template>
        </v-data-table>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="red" text @click="dialogVisible = false">
          {{ $t('Cancel') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { mapState } from 'vuex';
  import { EventBus, eventNames } from '../../../eventbus';
  import ProjectCriterionRow from './ProjectCriterionRow';

  export default {
    name: 'ProjectCriteria',
    components: { ProjectCriterionRow },
    data() {
      return {
        selectedTab: 0,
        dialogVisible: false,
      };
    },
    computed: {
      ...mapState({
        allCriteria: state => state.Criteria.criteria,
      }),
    },
    methods: {
      addNewCriteria() {
        EventBus.$emit(eventNames.addCriteria);
      },
      show() {
        this.dialogVisible = true;
      },
    },
    mounted() {
      EventBus.$on(eventNames.manageExistingCriteria, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Manage criteria': 'Zarządzaj kryteriami',
          'Add criteria': 'Dodaj kryteria',
          'List of current criteria': 'Lista obecnych kryteriów',
          'Cancel': 'Anuluj',
          'Close': 'Zamknij',
        },
      },
    },
  };
</script>
