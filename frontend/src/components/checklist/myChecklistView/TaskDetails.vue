<template>
  <v-dialog v-model="dialogVisibleModel" width="800">
    <v-card>
      <v-app-bar color="transparent" class="headline" flat dense>
        <span>{{ $t('Task details') }}</span>
        <v-spacer/>
        <v-btn @click="dialogVisibleModel = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <v-card-text>
        <div class="font-weight-black text--primary">{{ $t('Name') }}</div>
        {{ name }}
        <div class="font-weight-black text--primary mt-2">{{ $t('Description') }}</div>
        {{ description }}
        <task-details-status class="pt-3" :checklist="checklist" :task="task" :employee-id="employeeId"/>
        <task-details-responsible class="pt-2"
                                  v-if="task.responsible"
                                  :checklist="checklist"
                                  :task-id="task.id"
                                  :responsible="task.responsible[0]"/>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import TaskDetailsResponsible from './TaskDetailsResponsible';
  import TaskDetailsStatus from './TaskDetailsStatus';

  export default {
    name: 'TaskDetails',
    components: { TaskDetailsStatus, TaskDetailsResponsible },
    props: {
      dialogVisible: { type: Boolean, default: false },
      task: { type: Object, required: true },
      checklist: { type: Object, required: true },
      employeeId: { type: Number, required: true },
    },
    computed: {
      dialogVisibleModel: {
        get() {
          return this.dialogVisible;
        },
        set(val) {
          this.$emit('update:dialogVisible', val);
        },
      },
      name() {
        switch (getSuggestedLanguage()) {
          case 'pl': return this.task.namePl;
          case 'en': return this.task.nameEn;
          default: return '';
        }
      },
      description() {
        switch (getSuggestedLanguage()) {
          case 'pl': return this.task.descriptionPl;
          case 'en': return this.task.descriptionEn;
          default: return '';
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Task details': 'Detale zadania',
          'Description': 'Opis',
          'Name': 'Nazwa',
        },
      },
    },
  };
</script>
