<template>
  <div>
    <v-select v-model="project.criteria"
              :items="sortedCriteria"
              :readonly="!canEditCriteria"
              :item-text="checkLanguage"
              item-value="id"
              chips deletable-chips multiple/>
    <v-btn v-if="canEditCriteria"
           :disabled="!showCriteriaButton"
           color="primary"
           @click="saveCriteria"
           block>
      {{ $t('Save criteria') }}
    </v-btn>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'CriteriaTab',
    props: {
      project: { type: Object, required: true },
    },
    data() {
      return {
        language: getSuggestedLanguage(),
        showCriteriaButton: false,
        currentProject: {},
      };
    },
    computed: {
      ...mapState({
        allCriteria: state => state.Criteria.criteria,
      }),
      ...mapGetters({
        canEditCriteria: 'Authorization/isManager',
      }),
      checkLanguage() {
        return this.language === 'pl' ? 'namePl' : 'nameEn';
      },
      sortedCriteria() {
        if (this.language === 'pl') {
          return this.allCriteria.sort((a, b) => a.namePl.localeCompare(b.namePl));
        } else {
          return this.allCriteria.sort((a, b) => a.nameEn.localeCompare(b.nameEn));
        }
      },
    },
    watch: {
      'project.criteria': function(n) {
        if (this.currentProject.criteria.length !== n.length) {
          this.showCriteriaButton = true;
          return;
        }
        const newCriteria = n
          .filter(value => typeof value === 'string' || value instanceof String);

        if (newCriteria.length !== 0) {
          this.showCriteriaButton = true;
          return;
        }
        const currentCriteriaId = this.currentProject.criteria
          .filter(value => typeof value === 'object' && value.hasOwnProperty('id'))
          .map(value => value.id);
        const newCriteriaId = n
          .filter(value => typeof value === 'object' && value.hasOwnProperty('id'))
          .map(value => value.id);

        const isEqual = (currentCriteriaId.length === newCriteriaId.length
          && currentCriteriaId.sort().every((value, index) => { return value === newCriteriaId.sort()[index];}));
        if (isEqual === false) {
          this.showCriteriaButton = true;
          return;
        }
        this.showCriteriaButton = false;
      },
    },
    methods: {
      async saveCriteria() {
        let currentCriteria = [];
        currentCriteria = this.currentProject.criteria
          .filter(value => typeof value === 'object' && value.hasOwnProperty('id'))
          .map(value => value.id);
        const projectId = this.project.id;
        const addedCriteriaPromises = this.project.criteria
          .map(criterionId => this.$store.dispatch('Projects/assignCriterion', { projectId, data: { criterionId } }));
        const deletedCriteriaPromises = currentCriteria
          .map(criterionId => this.$store.dispatch('Projects/unsetCriterion', { projectId, criterionId }));
        await Promise.all([
          ...addedCriteriaPromises,
          ...deletedCriteriaPromises,
        ]).then(
          () => this.$store.dispatch('Projects/loadProjects'),
        ).then(
          () => this.$store.dispatch('Projects/sendEmail', { id: this.project.id }),
        ).then(
          () => {
            const currentProject = this.$store.state.Projects.projects.filter(f => f.id === this.project.id);
            this.currentProject = Object.assign(
              {}, currentProject[0],
            );
          },
        );
        this.$store.commit('showSnackbar', {
          text: this.$t('Criteria have been saved'),
          color: 'success',
        });
        this.showCriteriaButton = false;
        this.dialogVisible = false;
      },
    },
    mounted() {
      this.currentProject = Object.assign(
        {}, this.$store.state.Projects.projects.filter(f => f.id === this.project.id)[0],
      );
      this.$store.dispatch('Projects/loadProjects');
    },
    i18n: {
      messages: {
        pl: {
          'Save criteria': 'Zapisz kryteria',
          'Criteria have been saved': 'Kryteria zostały zapisane',
        },
      },
    },
  };
</script>
