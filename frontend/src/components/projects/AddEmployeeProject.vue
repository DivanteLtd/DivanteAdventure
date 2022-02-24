<template>
  <v-dialog v-model="dialogVisible" width="800">
    <v-card class="pa-4">
      <v-card-title class="headline">
        <span>{{ $t('Person - project assignation') }}</span>
      </v-card-title>
      <v-text-field
        :value="item.name"
        :label="$t('Project')"
        disabled/>
      <v-autocomplete
        v-model="childInput"
        :label="$t('Person')"
        :rules="[rules.hasChildInput, isDuplicate, projectPeriod]"
        :no-data-text="$t('No data available')"
        :items="chooseEmployee"/>
      <v-menu v-model="visibleDateFromPicker" offset-y full-width :close-on-content-click="false">
        <template v-slot:activator="{ on }">
          <v-text-field v-on="on"
                        :label="$t('Start of project')"
                        :rules="[isValidate, isDuplicate, projectPeriod]"
                        v-model="dateFrom"
                        prepend-icon="date_range"
                        readonly/>
        </template>
        <v-date-picker color="indigo" type="month"
                       v-model="dateStartHolder"
                       :locale="locale"
                       @change="validateDates"
                       landscape/>
      </v-menu>
      <v-menu v-model="visibleDateToPicker" offset-y full-width :close-on-content-click="false">
        <template v-slot:activator="{ on }">
          <v-text-field v-on="on"
                        :label="$t('End of project')"
                        :rules="[isValidate, isDuplicate, projectPeriod]"
                        v-model="dateTo"
                        flex: inline
                        prepend-icon="date_range"
                        readonly/>
        </template>
        <v-date-picker color="indigo" type="month"
                       v-model="dateEndHolder"
                       :locale="locale"
                       @change="validateDates"
                       landscape/>
      </v-menu>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue" text
               :disabled="!((childInput !== '')
                 && isValidate === true
                 && isDuplicate === true
                 && projectPeriod === true)"
               @click="addEmployeeProject">
          {{ $t('Add') }}
        </v-btn>
        <v-btn color="black" text @click="dialogVisible = false">
          {{ $t('Close') }}
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
    name: 'AddEmployeeProject',
    data() {
      return {
        locale: getSuggestedLanguage(),
        dialogVisible: false,
        childInput: '',
        dateStartHolder: moment().format('YYYY-MM'),
        dateEndHolder: moment().format('YYYY-MM'),
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
      childInput() {
        this.isDuplicate = this.isDuplicatePairings;
        this.projectPeriod = this.projectPeriodDates;
      },
      dateStartHolder() {
        this.visibleDateFromPicker = false;
        const tmp = [];
        this.visibleEditFromPicker.forEach(() => {
          tmp.push(false);
        });
        this.visibleEditFromPicker = tmp;
      },
      dateEndHolder() {
        this.visibleDateToPicker = false;
        const tmp = [];
        this.visibleEditToPicker.forEach(() => {
          tmp.push(false);
        });
        this.visibleEditToPicker = tmp;
      },
    },
    methods: {
      show(item) {
        this.item = item;
        this.childInput = '';
        if (item.isEdit !== undefined) {
          const employeeProjectId = this.pairings.filter(val => val.id === item.pairingId).map(val => val.projectId)[0];
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
          this.dateStartHolder = moment().format('YYYY-MM');
          this.dateEndHolder = moment().format('YYYY-MM');
        }
        if (this.allEmployees.length === 0) {
          this.$store.dispatch('Employees/loadEmployees');
        }
        if (this.pairings.length === 0) {
          this.$store.dispatch('Employees/loadPairings');
        }
        this.dialogVisible = true;
      },
      projectPeriodDates() {
        const startDate = moment(`${moment(this.dateStartHolder).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
        const endDate = moment(`${moment(this.dateEndHolder).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
        const projectStartDate = moment(`${moment.unix(this.item.started_at).format('MM-YYYY')}`, 'MM-YYYY')
          .endOf('month');
        const projectEndDate = moment(`${moment.unix(this.item.ended_at).format('MM-YYYY')}`, 'MM-YYYY')
          .endOf('month');
        if ((this.item.started_at !== -1 && this.item.ended_at !== -1 && this.childInput)
          && (projectStartDate > startDate || projectEndDate < endDate)) {
          return this.$t('The range goes beyond the duration of the project');
        } else if ((this.item.started_at !== -1 && this.childInput)
          && (this.item.started_at > startDate)) {
          return this.$t('The range goes beyond the duration of the project');
        } else {
          this.projectPeriod = true;
          return this.projectPeriod;
        }
      },
      dialogSingleDeletions(index) {
        this.dialogSingleVisible[0] = true;
        this.dialogSingleVisible.push(index);
      },
      async addEmployeeProject() {
        const employeeId = this.allEmployees
          .filter(val => `${val.lastName} ${val.name}` === this.childInput)
          .map(val => val.id)[0];
        const pair = this.pairings.filter(val => val.employeeId === employeeId
          && val.projectId === this.item.id)[0];
        if (pair !== undefined) {
          pair.dateFrom.push(moment(this.dateStartHolder).format('MM-YYYY'));
          pair.dateTo.push(moment(this.dateEndHolder).format('MM-YYYY'));
          const data = {
            id: pair.id,
            dateFrom: pair.dateFrom,
            dateTo: pair.dateTo,
            adding: true,
          };
          try {
            await this.$store.dispatch('Employees/editPairings', data);
            this.$store.commit('showSnackbar', {
              text: this.$t('Person period has been added'),
              color: 'success',
            });
            EventBus.$emit(eventNames.refreshProjectWindow);
          } catch (e) {
            this.$store.commit('showSnackbar', {
              text: this.$t('Person period can not be added'),
              color: 'error',
            });
          }
        } else {
          const data = {
            employeeId,
            projectId: this.item.id,
            dateFrom: [moment(this.dateStartHolder).format('MM-YYYY')],
            dateTo: [moment(this.dateEndHolder).format('MM-YYYY')],
          };
          try {
            await this.$store.dispatch('Employees/addPairings', data);
            this.$store.commit('showSnackbar', {
              text: this.$t('Person period has been added'),
              color: 'success',
            });
          } catch (e) {
            this.$store.commit('showSnackbar', {
              text: this.$t('Person period can not be added'),
              color: 'error',
            });
          }
        }
        this.dialogVisible = false;
      },
      validateDates() {
        this.isValidate = this.validEditedDates;
        this.isDuplicate = this.isDuplicatePairings;
        this.projectPeriod = this.projectPeriodDates;
      },
      validEditedDates() {
        this.isValidate = moment(this.dateStartHolder) > moment(this.dateEndHolder)
          ? this.$t('Incorrect dates') : true;
        return this.isValidate;
      },
      isDuplicatePairings() {
        let pairingsObject = false;
        const startDate = moment(`${moment(this.dateStartHolder).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
        const endDate = moment(`${moment(this.dateEndHolder).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
        const result = this.pairings
          .filter(element => `${element.employeeLastName} ${element.employeeName}` === this.childInput
            && element.projectId === this.item.id);
        if (result.length === 1) {
          const datesFrom = result[0].dateFrom || [];
          datesFrom.forEach((val, idx) => {
            const periodsOverlaps = moment.range(startDate, endDate).overlaps(moment.range(
              moment(`${val}`, 'MM-YYYY').startOf('month'),
              moment(`${result[0].dateTo[idx]}`, 'MM-YYYY').endOf('month'),
            ));
            if (periodsOverlaps) {
              pairingsObject = true;
            }
          });
          this.isDuplicate = pairingsObject ? this.$t('Pairings at that periods already exist') : true;
        } else {
          this.isDuplicate = true;
        }
        return this.isDuplicate;
      },
    },
    mounted() {
      EventBus.$on(eventNames.addEmployeeProject, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Add': 'Dodaj',
          'Delete all': 'Usuń wszystkie',
          'Close': 'Zamknij',
          'Project': 'Projekt',
          'Person': 'Osoba',
          'Person - project assignation': 'Przydział osoba - projekt',
          'No data available': 'Brak dostępnych danych',
          'Start of project': 'Start pracy w projekcie',
          'End of project': 'Koniec pracy w projekcie',
          'Pairings at that periods already exist': 'Przydział w tym okresie już istnieje',
          'Person period has been added': 'Okres osoby w projekcie został dodany',
          'Person period can not be added': 'Okres osoby w projekcie nie został dodany',
          'Incorrect dates': 'Niepoprawne daty',
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
