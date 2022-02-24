<template>
  <v-dialog v-model="dialogVisible" width="500">
    <v-card>
      <v-card-title class="headline">{{ $t('Change task status') }}</v-card-title>
      <v-progress-linear height="6" v-if="loading" indeterminate/>
      <v-card-text v-else>
        <v-list two-line>
          <v-list-item v-if="task">
            <v-list-item-action><v-icon>person</v-icon></v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ task.responsible.name }} {{ task.responsible.lastName }}</v-list-item-title>
              <v-list-item-subtitle>{{ $t('Responsible') }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="task">
            <v-list-item-action><v-icon>notes</v-icon></v-list-item-action>
            <v-list-item-content>
              <v-list-item-title v-if="language==='pl'">{{ task.namePl }}</v-list-item-title>
              <v-list-item-title v-else>{{ task.nameEn }}</v-list-item-title>
              <v-list-item-subtitle>{{ $t('Task name') }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="task">
            <v-list-item-action><v-icon>create</v-icon></v-list-item-action>
            <v-list-item-content>
              <v-autocomplete
                :items="task.possibleStatuses"
                @input="handelStatusChange"
                item-text="label_pl"
                item-value="label_en"
                label="Status"
                class="autocomplete"
              ></v-autocomplete>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <v-btn color="primary" class="px-3" @click="save" block>Save</v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'QuestionUpdateDialog',
    data() {
      return {
        dialogVisible: false,
        loading: false,
        checklist: null,
        task: null,
        status: '',
        language: getSuggestedLanguage(),
      };
    },
    methods: {
      show(checklist, task) {
        if (!this.dialogVisible) {
          this.checklist = checklist;
          this.task = task;
          this.dialogVisible = true;
        }
      },
      handelStatusChange(value) {
        this.status = value;
      },
      async save() {
        const status = this.task.possibleStatuses.findIndex(x => x.label_en === this.status);
        const data = {
          checklistId: this.checklist.id,
          questionId: this.task.id,
          status,
        };
        await this.$store.dispatch('Checklist/updateStatus', data);
        this.$store.commit('showSnackbar', {
          text: this.$t('Task status changed'),
          color: 'success',
        });
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    mounted() {
      EventBus.$on(eventNames.showQuestionUpdate, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Change task status': 'Zmień status zadania',
          'Task status changed': 'Status zadania zmieniony',
          'Responsible': 'Odpowiedzialny',
          'Task name': 'Nazwa zadania',
        },
      },
    },
  };
</script>

<style scoped>
  .autocomplete{
    width: 100%;
  }
</style>
