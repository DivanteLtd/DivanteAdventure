<template>
  <v-dialog v-model="dialogVisible" v-if="dialogVisible" width="550">
    <v-card id="project-confirm-dialog">
      <v-card-title class="headline">
        <span>{{ titleText }}</span>
      </v-card-title>
      <v-card-text>
        {{ formText }}
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue" text @click="dialogVisible = false">
          {{ $t('Cancel') }}
        </v-btn>
        <v-btn color="red" text @click="employeesLength === 0 ? deleteProject() : hideProject()">
          {{ actionText }}
        </v-btn>
      </v-card-actions>
      <v-snackbar
        v-model="snackbar"
        color="error"
        :timeout="timeout"
        absolute
        top
      >
        {{ $t('Project can not be deleted, please read instruction') }}
        <v-btn
          dark
          text
          @click="snackbar = false"
        >
          <v-icon>cancel</v-icon>
        </v-btn>
      </v-snackbar>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'DeleteProjectForm',
    data() { return {
      dialogVisible: false,
      id: 0,
      formText: '',
      actionText: '',
      titleText: '',
      snackbar: false,
      timeout: 5000,
      employeesLength: 0,
    };},
    methods: {
      show(data) {
        this.formText = '';
        this.actionText = '';
        this.titleText = '';
        if (data.notVisiblePerson) {
          this.formText = this.$t('You are not able to delete this project. At least one person from this project not working anymore but is still connected with project for history entries purposes. Please archive this project or if you are sure, there is no need to keep history of this project, contact with DA Team to permanently delete this project.');
          this.actionText = this.$t('Archive');
          this.titleText = this.$t('Archive project');
          this.snackbar = true;
        } else if (data.employeesLength > 0) {
          this.formText = this.$t('You are not able to delete this project, because people are still assigned. Please delete them first or if you want to keep their history in project, you can easily make this project as archived.');
          this.actionText = this.$t('Archive');
          this.titleText = this.$t('Archive project');
          this.snackbar = true;
        } else {
          this.formText = this.$t('Are you sure you want to delete this project?');
          this.actionText = this.$t('Delete');
          this.titleText = this.$t('Delete project');
        }
        this.employeesLength = data.employeesLength;
        this.id = data.id;
        this.dialogVisible = true;
      },
      async deleteProject() {
        try {
          await this.$store.dispatch('Projects/delete', this.id);
          await this.$store.dispatch('Projects/loadProjects');
          this.$store.commit('showSnackbar', {
            text: this.$t('Project has been deleted'),
            color: 'success',
          });
          EventBus.$emit(eventNames.escapePressed);
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Project cannot be deleted'),
            color: 'error',
          });
        }
        this.dialogVisible = false;
      },
      async hideProject() {
        try {
          await this.$store.dispatch('Projects/hide', this.id);
          await this.$store.dispatch('Projects/loadProjects');
          this.$store.commit('showSnackbar', {
            text: this.$t('Project has been archived'),
            color: 'success',
          });
          EventBus.$emit(eventNames.escapePressed);
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Project cannot be archived'),
            color: 'error',
          });
        }
        this.dialogVisible = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.deleteProjectForm, this.show);
    },
    i18n: { messages: {
      pl: {
        'Delete project': 'Usuń projekt',
        'Archive project': 'Archiwizuj projekt',
        'Cancel': 'Anuluj',
        'Project can not be deleted, please read instruction': 'Projekt nie może zostać usunięty, proszę zapoznać się z instrukcją',
        'You are not able to delete this project, because people are still assigned. Please delete them first or if you want to keep their history in project, you can easily make this project as archived.': 'Nie możesz usunąć tego projektu, ponieważ są jeszcze przypisane osoby. Wróć i usuń wcześniej przypisane osoby, lub jeśli chcesz zachować ich historie pracy przy projekcie, możesz po prostu zarchiwizować projekt.',
        'Are you sure you want to delete this project?': 'Czy na pewno chcesz usunąć ten projekt?',
        'Delete': 'Usuń',
        'Archive': 'Archiwizuj',
        'Project has been deleted': 'Projekt został usunięty',
        'Project cannot be deleted': 'Projekt nie został usunięty',
        'Project has been archived': 'Projekt został zarchiwizowany',
        'Project cannot be archived': 'Projekt nie został zarchiwizowany',
        'You are not able to delete this project. At least one person from this project not working anymore but is still connected with project for history entries purposes. Please archive this project or if you are sure, there is no need to keep history of this project, contact with Adventure Team to permanently delete this project.':
          'Nie możesz usunąć tego projektu. Przynajmniej jedna osoba z projektu nie nie jest już zatrudniona ale ciągle jest przypisana do projektu w celu utrzymania historii wpisów. Możesz zarchiwizować ten projekt, lub, jeśli jeśli masz pewność, że nie ma potrzeby trzymania dalej historii tego projektu, proszę skontaktować się z zespołem Adventure w celu całkowitego usunięcia projektu',

      },
    } },
  };
</script>
