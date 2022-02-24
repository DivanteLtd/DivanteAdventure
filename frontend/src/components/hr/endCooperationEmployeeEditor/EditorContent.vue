<template>
  <v-card-text>
    <v-form>
      <v-text-field :value="employee.name + ' ' + employee.lastName" :label="$t('Employee')" :disabled="true"/>
      <v-select v-model="whoEndedCooperation"
                :items="options"
                :label="$t('who-end-cooperation')"
                item-text="value"
                item-value="id"
                class="required"/>
      <v-text-field v-model="nextCompany" :label="$t('next-company')"/>
      <v-checkbox v-model="exitInterview" :label="$t('exit-interview')"/>
      <v-checkbox v-model="checklist" :label="$t('Is the end cooperation checklist completed?')"/>
      <v-text-field v-model="comment" :label="$t('Comment')"/>
      <v-label>{{ $t('Designated dismiss date*') }}</v-label>
      <div class="mt-2">
        <v-date-picker v-model="dismissDate"
                       :locale="locale"
                       :first-day-of-week="$t('date.firstDayOfWeek')"
                       color="indigo"
                       no-title full-width/>
      </div>
      <v-btn color="success" :disabled="!formValid" @click="save" :loading="loading" class="mt-2" block>
        {{ $t('Save') }}
      </v-btn>
    </v-form>
  </v-card-text>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import moment from '@divante-adventure/work-moment';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'EditorContent',
    props: {
      employee: { type: Object, required: true },
      isEdit: { type: Boolean, required: true },
    },
    data() {
      return {
        loading: false,
        whoEndedCooperation: this.employee.whoEndedCooperation || '',
        dismissDate: this.employee.dismissDate || moment().format('YYYY-MM-DD'),
        nextCompany: this.employee.nextCompany || '',
        exitInterview: this.employee.exitInterview || false,
        checklist: this.employee.checklist || false,
        comment: this.employee.comment || '',
        locale: getSuggestedLanguage(),
        options: [{ id: 'Employee', value: this.$t('Employee') }, { id: 'Company', value: this.$t('Company') }],
      };
    },
    computed: {
      formValid() {
        return !!this.whoEndedCooperation && !!this.dismissDate;
      },
    },
    methods: {
      async save() {
        const data = {
          id: this.employee.id,
          employeeId: this.employee.employeeId || -1,
          email: this.employee.email,
          dismiss: this.dismissDate,
          nextCompany: this.nextCompany,
          exitInterview: this.exitInterview,
          whoEndedCooperation: this.whoEndedCooperation,
          checklist: this.checklist,
          comment: this.comment,
        };
        this.loading = true;
        try {
          if (!this.isEdit) {
            await this.$store.dispatch('Hr/createEmployeeToDismiss', data);
          } else {
            await this.$store.dispatch('Hr/updateEmployeeToDismiss', data);
          }
          this.$store.commit('showSnackbar', {
            text: this.$t('The form for termination of cooperation has been saved'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('There was an error while saving form'),
            color: 'error',
          });
        }
        if (!this.isEdit) {
          EventBus.$emit(eventNames.deleteEmployeeAfter);
        }
        EventBus.$emit(eventNames.hrPersonListReload);
        this.loading = false;
        this.$emit('close');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Designated dismiss date*': 'Planowana data zwolnienia*',
          'Save': 'Zapisz',
          'Field is required': 'Pole jest wymagane',
          'who-end-cooperation': 'Kto zakończył współpracę',
          'next-company': 'Do jakiej firmy odszedł',
          'exit-interview': 'Czy było exit interview',
          'Is the end cooperation checklist completed?': 'Czy checklista zakończenia współpracy wypełniona?',
          'Comment': 'Uwagi',
          'Company': 'Firma',
          'Employee': 'Pracownik',
          'No data available': 'Brak danych',
          'The form for termination of cooperation has been saved': 'Formularz zakończenia współpracy został zapisany',
          'There was an error while saving form': 'Wystąpił błąd podczas zapisywania formularza',
        },
        en: {
          'next-company': 'Next company',
          'exit-interview': 'Was there exit interview?',
          'who-end-cooperation': 'Who end cooperation',
        },
      },
    },
  };
</script>

<style scoped>
  .required >>> label::after {
    content: '*';
  }
  .required >>> label.v-label--active::after {
    color: red;
  }
</style>
