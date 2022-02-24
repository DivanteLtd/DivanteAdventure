<template>
  <v-dialog v-model="dialogVisible" width="800">
    <v-card class="pa-4">
      <v-card-title class="headline">
        <span>{{ $t('Person - project edition') }}</span>
      </v-card-title>
      <v-text-field
        :value="item.name"
        :label="$t('Project')"
        disabled/>
      <v-text-field
        :value="item.employeeName"
        :label="$t('Person')"
        disabled/>
      <div class="layout">
        <v-col cols="5" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-menu v-for="(date, index) in datesFrom" :key="index" v-model="visibleEditFromPicker[index]"
                  offset-y full-width :close-on-content-click="false">
            <template v-slot:activator="{ on }">
              <v-text-field v-on="on"
                            :label="$t('Start of project')"
                            :rules="[isValidate, isDuplicate, projectPeriod]"
                            v-model="datesFrom[index].value"
                            prepend-icon="date_range"
                            readonly/>
            </template>
            <v-date-picker type="month"
                           color="primary"
                           v-model="dateStartHolder[index]"
                           :locale="locale"
                           @change="validateDates"/>
          </v-menu>
        </v-col>
        <v-col cols="5" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-menu v-for="(date, index) in datesTo" :key="index" v-model="visibleEditToPicker[index]"
                  offset-y full-width :close-on-content-click="false">
            <template v-slot:activator="{ on }">
              <v-text-field v-on="on"
                            :label="$t('End of project')"
                            :rules="[isValidate, isDuplicate, projectPeriod]"
                            v-model="datesTo[index]"
                            prepend-icon="date_range"
                            readonly/>
            </template>
            <v-date-picker type="month"
                           color="primary"
                           v-model="dateEndHolder[index]"
                           :locale="locale"
                           @change="validateDates"/>
            <v-spacer/>
          </v-menu>
        </v-col>
        <v-col class="toDelete" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-btn small text icon v-for="(index) in datesTo" :key="index" @click="dialogSingleDeletions(index)">
            <v-icon size="25">delete</v-icon>
          </v-btn>
        </v-col>
      </div>
      <confirm-dialog
        v-if="dialog"
        v-model="dialog"
        :width="400"
        :title="this.$t('Unassign all project periods from person')"
        :question="this.$t('Are you sure to unassign all project periods ' +
          'from person? All history of entries will be deleted')"
        yes-color="red"
        @yes="toDeleteAll"
        @no="dialog = false"
      />
      <confirm-dialog
        v-model="dialogSingleVisible[0]"
        v-if="dialogSingleVisible[0]"
        :width="400"
        :title="this.$t('Unassign project from person')"
        :question="this.$t('Are you sure to unassign project from person? All history of entries will be deleted')"
        yes-color="red"
        @yes="toDeleteSingle(dialogSingleVisible[1])"
        @no="dialogSingleVisible = [false]"
      />
      <v-card-actions class="d-flex justify-center row">
        <v-btn color="red" text @click="dialog = true">
          {{ $t('Delete all') }}
        </v-btn>
        <v-spacer/>
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
        <v-btn color="blue" text
               :disabled="!(isValidate === true
                 && isDuplicate === true
                 && projectPeriod === true
                 && (changedFrom || changedTo))"
               @click="editEmployeeProject">
          {{ $t('Save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import { mapState } from 'vuex';

  export default {
    name: 'EditEmployeeProject',
    components: {
      ConfirmDialog: () => import('../utils/ConfirmDialog.vue'),
    },
    data() {
      return {
        locale: getSuggestedLanguage(),
        dialogVisible: false,
        dateStartHolder: [],
        dateEndHolder: [],
        visibleDateFromPicker: false,
        visibleDateToPicker: false,
        visibleEditFromPicker: [],
        visibleEditToPicker: [],
        employeeProject: {},
        dialog: false,
        dialogSingleVisible: [false],
        item: {},
        isDuplicate: true,
        isValidate: true,
        projectPeriod: true,
        changedFrom: false,
        changedTo: false,
        beforeEditDatesFrom: [],
        beforeEditDatesTo: [],
        rules: {
          hasChildInput: value => {
            return (value !== '') || '';
          },
        },
      };
    },
    computed: {
      ...mapState({
        allEmployees: state => state.Employees.employees,
        pairings: state => state.Employees.pairings,
      }),
      getStartDate() {
        return moment(`${moment(this.dateStartHolder).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
      },
      getEndDate() {
        return moment(`${moment(this.dateEndHolder).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
      },
      dateFrom() {
        const [year, month] = this.dateStartHolder.split('-');
        const monthId = parseInt(month) - 1;
        const monthLabel = this.$t(`date.months.${monthId}`);
        return `${monthLabel} ${year}`;
      },
      dateTo() {
        const [year, month] = this.dateEndHolder.split('-');
        const monthId = parseInt(month) - 1;
        const monthLabel = this.$t(`date.months.${monthId}`);
        return `${monthLabel} ${year}`;
      },
      datesFrom() {
        const datesFrom = [];
        this.dateStartHolder.forEach((val, idx) => {
          const [year, month] = val.split('-');
          const monthId = parseInt(month) - 1;
          const monthLabel = this.$t(`date.months.${monthId}`);
          datesFrom.push({ value: `${monthLabel} ${year}`, id: idx });
        });
        return datesFrom;
      },
      datesTo() {
        const datesTo = [];
        this.dateEndHolder.forEach(val => {
          const [year, month] = val.split('-');
          const monthId = parseInt(month) - 1;
          const monthLabel = this.$t(`date.months.${monthId}`);
          datesTo.push(`${monthLabel} ${year}`);
        });
        return datesTo;
      },
      chooseEmployee() {
        return this.allEmployees.map(val => `${val.lastName} ${val.name}`);
      },
    },
    watch: {
      dateStartHolder() {
        this.changedFrom = false;
        this.visibleDateFromPicker = false;
        const tmp = [];
        this.visibleEditFromPicker.forEach(() => {
          tmp.push(false);
        });
        this.visibleEditFromPicker = tmp;
        this.beforeEditDatesFrom.forEach((val, id) => {
          if (val !== this.dateStartHolder[id]) {
            this.changedFrom = true;
          }
        });
      },
      dateEndHolder() {
        this.changedTo = false;
        this.visibleDateToPicker = false;
        const tmp = [];
        this.visibleEditToPicker.forEach(() => {
          tmp.push(false);
        });
        this.visibleEditToPicker = tmp;
        this.beforeEditDatesTo.forEach((val, id) => {
          if (val !== this.dateEndHolder[id]) {
            this.changedTo = true;
          }
        });
      },
    },
    methods: {
      show(item) {
        this.item = item;
        if (item.isEdit !== undefined) {
          this.changedFrom = false;
          this.changedTo = false;
          this.beforeEditDatesTo = [];
          this.beforeEditDatesFrom = [];
          const employeeProjectId = this.pairings.filter(val => val.id === item.pairingId)
            .map(val => val.projectId)[0];
          this.employeeProject = this.pairings.filter(val => val.employeeId === this.item.employeeId
            && val.projectId === employeeProjectId)[0];
          this.item.name = this.employeeProject.projectName;
          this.item.employeeName = `${this.employeeProject.employeeName} ${this.employeeProject.employeeLastName}`;
          const tmpDateFrom = [];
          const tmpDateTo = [];

          this.employeeProject.dateFrom.forEach(val => {
            const [month, year] = val.split('-');
            tmpDateFrom.push(`${year}-${month}`);
          });
          this.dateStartHolder = tmpDateFrom;
          this.employeeProject.dateTo.forEach(val => {
            const [month, year] = val.split('-');
            tmpDateTo.push(`${year}-${month}`);
          });
          this.dateEndHolder = tmpDateTo;
        } else {
          this.dateStartHolder = moment()
            .format('YYYY-MM');
          this.dateEndHolder = moment()
            .format('YYYY-MM');
        }
        if (this.allEmployees.length === 0) {
          this.$store.dispatch('Employees/loadEmployees');
        }
        if (this.pairings.length === 0) {
          this.$store.dispatch('Employees/loadPairings');
        }
        this.dateStartHolder.forEach(val => this.beforeEditDatesFrom.push(val));
        this.dateEndHolder.forEach(val => this.beforeEditDatesTo.push(val));
        this.dialogVisible = true;
      },
      getStartMoment(value) {
        return moment(`${moment(value).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
      },
      getEndMoment(value) {
        return moment(`${moment(value).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
      },
      projectPeriodDatesEdit() {
        const checkSingleRange = [];
        const projectStartDate = this.getStartMoment(this.employeeProject.startedAt);
        const projectEndDate = this.getEndMoment(this.employeeProject.endedAt);
        this.employeeProject.dateFrom.forEach((value, index) => {
          const startDate = this.getStartMoment(this.dateStartHolder[index]);
          const endDate = this.getEndMoment(this.dateEndHolder[index]);
          if (this.employeeProject.startedAt && this.employeeProject.endedAt) {
            if (projectStartDate > startDate || projectEndDate < endDate) {
              checkSingleRange.push(false);
            }
          } else if (this.employeeProject.startedAt) {
            const projectStartDate = moment(`${moment(this.employeeProject.startedAt)
              .format('MM-YYYY')}`, 'MM-YYYY')
              .startOf('month');
            if (projectStartDate > startDate) {
              checkSingleRange.push(false);
            }
          }
        });
        if (checkSingleRange.length === 0) {
          this.projectPeriod = true;
          return this.projectPeriod;
        } else {
          return this.$t('The range goes beyond the duration of the project');
        }
      },
      dialogSingleDeletions(index) {
        this.dialogSingleVisible[0] = true;
        this.dialogSingleVisible.push(index);
      },
      async toDeleteSingle(index) {
        if (this.dateStartHolder.length === 1) {
          return this.toDeleteAll();
        }
        this.beforeEditDatesFrom.splice(index, 1);
        this.beforeEditDatesTo.splice(index, 1);
        const dateFrom = [];
        this.beforeEditDatesFrom.forEach((val, idx) => {
          if (index !== idx) {
            const [year, month] = val.split('-');
            dateFrom.push(`${month}-${year}`);
          }
        });
        const dateTo = [];
        this.beforeEditDatesTo.forEach((val, idx) => {
          if (index !== idx) {
            const [year, month] = val.split('-');
            dateTo.push(`${month}-${year}`);
          }
        });
        const data = {
          id: this.employeeProject.id,
          dateFrom,
          dateTo,
          deletions: true,
        };
        try {
          await this.$store.dispatch('Employees/editPairings', data);
          this.dialogSingleVisible = [false];
          this.dateEndHolder.splice(index, 1);
          this.dateStartHolder.splice(index, 1);
          return this.$store.commit('showSnackbar', {
            text: this.$t('Period in project has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.dialogSingleVisible = [false];
          return this.$store.commit('showSnackbar', {
            text: this.$t('Period in project can not be deleted'),
            color: 'error',
          });
        }
      },
      async toDeleteAll() {
        this.dialog = false;
        this.dialogVisible = false;
        try {
          await this.$store.dispatch('Employees/deletePairings', this.employeeProject.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('All person periods in project has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Those person periods can not be deleted'),
            color: 'error',
          });
        }
      },
      async editEmployeeProject() {
        const data = {
          id: this.employeeProject.id,
        };
        const dateFrom = [];
        this.dateStartHolder.forEach(val => {
          const [year, month] = val.split('-');
          dateFrom.push(`${month}-${year}`);
        });
        const dateTo = [];
        this.dateEndHolder.forEach(val => {
          const [year, month] = val.split('-');
          dateTo.push(`${month}-${year}`);
        });
        data.dateFrom = dateFrom;
        data.dateTo = dateTo;
        try {
          await this.$store.dispatch('Employees/editPairings', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Person period has been edited'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Person period can not be edited'),
            color: 'error',
          });
        }
        this.dialogVisible = false;
      },
      validateDates() {
        this.isValidate = this.validEditedDatesEdit;
        this.isDuplicate = this.isDuplicatePairingsEdit;
        this.projectPeriod = this.projectPeriodDatesEdit;
      },
      validEditedDatesEdit() {
        let tmp = true;
        this.dateStartHolder.forEach((val, idx) => {
          if (moment(val) > moment(this.dateEndHolder[idx])) {
            tmp = false;
          }
        });
        if (!tmp) {
          return this.$t('Incorrect dates');
        } else {
          this.isValidate = true;
          return this.isValidate;
        }
      },
      isDuplicatePairingsEdit() {
        let pairingsObject = false;
        this.employeeProject.dateFrom.forEach((value, index) => {
          const startDate = this.getStartMoment(this.dateStartHolder[index]);
          const endDate = this.getEndMoment(this.dateEndHolder[index]);
          this.employeeProject.dateFrom.forEach((val, idx) => {
            if (index !== idx) {
              const periodsOverlaps = moment.range(startDate, endDate)
                .overlaps(moment.range(this.getStartMoment(this.dateStartHolder[idx]),
                                       this.getEndMoment(this.dateEndHolder[idx])));
              if (periodsOverlaps) {
                pairingsObject = true;
              }
            }
          });
        });
        if (pairingsObject) {
          return this.$t('Other pairings at that periods already exist');
        } else {
          this.isDuplicate = true;
          return this.isDuplicate;
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.editEmployeeProject, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Add': 'Dodaj',
          'Save': 'Zapisz',
          'Back': 'Cofnij',
          'Delete': 'Usuń',
          'Delete all': 'Usuń wszystkie',
          'Close': 'Zamknij',
          'Assign person': 'Przypisz osobę',
          'No data available': 'Brak dostępnych danych',
          'Project': 'Projekt',
          'Person': 'Osoba',
          'Period in project has been deleted': 'Okres w projekcie został usunięty',
          'Period in project can not be deleted': 'Okres w projekcie nie został usunięty',
          'All person periods in project has been deleted': 'Wszystkie okresy osoby w projekcie zostały usunięte',
          'Those person periods can not be deleted': 'Okresy osoby nie mogą zostać usunięte',
          'Start of project': 'Start pracy w projekcie',
          'End of project': 'Koniec pracy w projekcie',
          'Incorrect dates': 'Niepoprawne daty',
          'Person period has been added': 'Okres osoby w projekcie został dodany',
          'Person period can not be added': 'Okres osoby w projekcie nie został dodany',
          'Person period has been edited': 'Okres osoby w projekcie został zmieniony',
          'Person period can not be edited': 'Okres osoby w projekcie nie został zmieniony',
          'Person - project assignation': 'Przydział osoba - projekt',
          'Person - project edition': 'Edycja osoba - projekt',
          'Other pairings at that periods already exist': 'Przydział w tym okresie już istnieje',
          'Pairings at that periods already exist': 'Przydział w tym okresie już istnieje',
          'Unassign all project periods from person': 'Odłącz wszystkie okresy projektu od osoby',
          'Unassign project from person': 'Odłącz okres projektu od osoby',
          'Are you sure to unassign project from person? All history of entries will be deleted':
            'Jesteś pewien że chcesz odłączyć okres projektu od osoby? Wpisy z tego okresu zostaną usunięte',
          'Are you sure to unassign all project periods from person? All history of entries will be deleted':
            'Czy na pewno chcesz odłączyć wszystkie okresy projektu od osoby? Cała historia wpisów zostanie usunięta',
          'The range goes beyond the duration of the project': 'Przedział wykracza poza czas trwania projektu',
        },
      },
    },
  };
</script>
<style scoped>
.toDelete {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
}
</style>
