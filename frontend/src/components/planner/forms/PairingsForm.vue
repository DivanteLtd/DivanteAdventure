<template>
  <dialog-form :show-event="showEvent"
               :title="isEdit ? $t('Employee - project edition') : $t('Employee - project assignation')"
               :max-width="isEdit ? 800 : 400"
               :to-delete="isEdit"
               @show="show"
               @save="save"
               @toDelete="dialog = true">
    <v-col md="12">
      <v-text-field :value="viewMode.createElementLabel(element)"
                    :label="viewMode === ViewMode.project ? $t('Project') : $t('Employee')"
                    disabled/>
    </v-col>
    <v-col v-if="!isEdit" md="12">
      <v-autocomplete v-model="childInput"
                      :label="viewMode === ViewMode.project ? $t('Employee') : $t('Project')"
                      :no-data-text="$t('No data available')"
                      :rules="[rules.hasChildInput, isDuplicate, projectPeriod]"
                      :items="allowedChildrenLabels"/>
    </v-col>
    <v-col v-if="isEdit" md="12">
      <v-text-field :value="viewMode.createChildElementLabel(element)"
                    :label="viewMode === ViewMode.project ? $t('Employee') : $t('Project')"
                    disabled/>
    </v-col>
    <v-menu v-if="!isEdit" v-model="visibleDateFromPicker" offset-y full-width :close-on-content-click="false">
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
                     @change="validateMyInput"
                     landscape/>
    </v-menu>
    <v-menu v-if="!isEdit" v-model="visibleDateToPicker" offset-y full-width :close-on-content-click="false">
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
                     @change="validateMyInput"
                     landscape/>
    </v-menu>
    <v-col v-if="isEdit" md="5">
      <v-menu v-for="(dateFrom1, index) in datesFrom" :key="index" v-model="visibleEditFromPicker[index]"
              offset-y full-width :close-on-content-click="false">
        <template v-slot:activator="{ on }">
          <v-text-field v-on="on"
                        :key="index"
                        :label="$t('Start of project')"
                        :rules="[isValidate, isDuplicate, projectPeriod]"
                        v-model="datesFrom[index].value"
                        prepend-icon="date_range"
                        readonly/>
        </template>
        <v-date-picker color="indigo" type="month"
                       :key="index"
                       v-model="dateStartHolder[index]"
                       :locale="locale"
                       @change="validateMyInput"
                       landscape/>
      </v-menu>
    </v-col>
    <v-col v-if="isEdit" md="5">
      <v-menu v-for="(dateTo1, index) in datesTo" :key="index" v-model="visibleEditToPicker[index]"
              offset-y full-width :close-on-content-click="false">
        <template v-slot:activator="{ on }">
          <v-text-field v-on="on"
                        :label="$t('End of project')"
                        :rules="[isValidate, isDuplicate, projectPeriod]"
                        v-model="datesTo[index]"
                        prepend-icon="date_range"
                        readonly/>
        </template>
        <v-date-picker color="indigo" type="month"
                       v-model="dateEndHolder[index]"
                       :locale="locale"
                       @change="validateMyInput"
                       landscape/>
        <v-spacer></v-spacer>
      </v-menu>
    </v-col>
    <v-col v-if="isEdit" md="2" class="toDelete">
      <template v-slot:activator="{ on }">
        <v-btn v-for="(dateTo1, index) in datesTo" :key="index"
               small text icon v-on="on" @click="dialogSingleDeletions(index)">
          <v-icon size="25">delete</v-icon>
        </v-btn>
      </template>
    </v-col>
    <v-dialog v-model="dialog" max-width="290">
      <v-card>
        <v-card-title class="headline">
          {{ $t(`Unassign all ${viewMode.opposite.value} from ${viewMode.value}`) }}
        </v-card-title>
        <v-card-text>
          {{ $t(`Are you sure you want to unassign all ${viewMode.opposite.value} from ${viewMode.value}?`) }}
          {{ $t('All history of entries will be deleted') }}
        </v-card-text>
        <v-card-actions>
          <v-btn text="text" @click="dialog = false">
            {{ $t(`Back`) }}
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="red" flat="text" @click="toDeleteAll">
            {{ $t(`Delete`) }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogSingleVisible[0]" max-width="290">
      <v-card id="pairings-form">
        <v-card-title class="headline">
          {{ $t(`Unassign ${viewMode.opposite.value} from ${viewMode.value}`) }}
        </v-card-title>
        <v-card-text>
          {{ $t(`Are you sure you want to unassign ${viewMode.opposite.value} from ${viewMode.value}?`) }}
          {{ $t('All history of entries will be deleted') }}
        </v-card-text>
        <v-card-actions>
          <v-btn text="text" @click="dialogSingleVisible = [false]">
            {{ $t(`Back`) }}
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="red" flat="text" @click="toDeleteSingle(dialogSingleVisible[1])">
            {{ $t(`Delete`) }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </dialog-form>
</template>

<script>
  import DialogForm from './DialogForm';
  import { EventBus, eventNames } from '../../../eventbus';
  import { viewModeMixin } from '../../../mixins/PlannerMixins';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import moment from '@divante-adventure/work-moment';
  import ViewMode from '../../../util/planner/ViewMode';
  import { mapState } from 'vuex';

  export default {
    name: 'PairingsForm',
    components: { DialogForm },
    mixins: [viewModeMixin],
    data() {
      return {
        showEvent: eventNames.addEditDeleteChild,
        dateStartHolder: moment().format('YYYY-MM'),
        dateEndHolder: moment().format('YYYY-MM'),
        locale: getSuggestedLanguage(),
        visibleDateFromPicker: false,
        visibleDateToPicker: false,
        visibleEditFromPicker: [],
        visibleEditToPicker: [],
        isValidate: true,
        isDuplicate: true,
        projectPeriod: true,
        isEdit: false,
        dialog: false,
        dialogSingleVisible: [false],
        childElement: {},
        parentElement: {},
        childInput: '',
        element: {},
        rules: {
          hasChildInput: value => {
            return (value !== '') || '';
          },
        },
        ViewMode,
      };
    },
    computed: {
      ...mapState({
        pairings: state => state.Planner.pairings,
        currentDate: state => state.Planner.Time.currentDate,
      }),
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
      allowedChildren() {
        return this.viewMode.opposite.getElementsFromStore(this.$store);
      },
      allowedChildrenLabels() {
        if (this.viewMode === this.ViewMode.project) {
          const currentDate = moment(this.currentDate);
          return this.allowedChildren
            .filter(element => moment(element.hiredTo) > moment(`${currentDate.format('YYYY-MM-DD')}`, 'YYYY-MM-DD'))
            .map(element => this.viewMode.opposite.createElementLabel(element));
        } else {
          return this.allowedChildren
            .filter(element => element.delete === false)
            .map(element => this.viewMode.opposite.createElementLabel(element));
        }
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
      validEditedDates() {
        if (this.isEdit === false) {
          if (moment(this.dateStartHolder) > moment(this.dateEndHolder)) {
            return this.$t('Incorrect dates');
          } else {
            this.isValidate = true;
            return true;
          }
        }
        if (this.isEdit === true) {
          let tmp = true;
          this.dateStartHolder.forEach((val, idx) => {
            if (moment(val) > moment(this.dateEndHolder[idx])) {
              tmp = false;
            }
          });
          if (tmp === false) {
            return this.$t('Incorrect dates');
          } else {
            this.isValidate = true;
            return true;
          }
        }
        return false;
      },
      isDuplicatePairings() {
        if (this.isEdit) {
          let pairingsObject = false;
          this.pairings.forEach(pair => {
            if (pair.id === this.element.id) {
              pair.dateFrom.forEach((value, index) => {
                const startDate = moment(`${moment(this.dateStartHolder[index]).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
                const endDate = moment(`${moment(this.dateEndHolder[index]).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
                pair.dateFrom.forEach((val, idx) => {
                  if (index !== idx) {
                    const periodsOverlaps = moment.range(startDate, endDate).overlaps(moment.range(
                      moment(`${moment(this.dateStartHolder[idx]).format('MM-YYYY')}`, 'MM-YYYY').startOf('month'),
                      moment(`${moment(this.dateEndHolder[idx]).format('MM-YYYY')}`, 'MM-YYYY').endOf('month'),
                    ));
                    if (periodsOverlaps === true) {
                      pairingsObject = true;
                    }
                  }
                });
              });
            }
          });
          if (pairingsObject === true) {
            return this.$t('Other pairings at that periods already exist');
          } else {
            this.isDuplicate = true;
            return true;
          }
        }
        if (this.childInput) {
          const elementFieldName = `${this.viewMode.value}Id`;
          const childFieldName = elementFieldName === 'employeeId' ? 'projectId' : 'employeeId';
          const startDate = moment(`${moment(this.dateStartHolder).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
          const endDate = moment(`${moment(this.dateEndHolder).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
          const result = this.allowedChildren
            .filter(element => this.viewMode.opposite.createElementLabel(element) === this.childInput);
          if (result.length === 1) {
            let pairingsObject = false;
            this.pairings.forEach(pair => {
              if (pair[elementFieldName] === this.element.id && pair[childFieldName] === result[0].id) {
                const datesFrom = pair.dateFrom || [];
                datesFrom.forEach((val, idx) => {
                  const periodsOverlaps = moment.range(startDate, endDate).overlaps(moment.range(
                    moment(`${val}`, 'MM-YYYY').startOf('month'),
                    moment(`${pair.dateTo[idx]}`, 'MM-YYYY').endOf('month'),
                  ));
                  if (periodsOverlaps === true) {
                    pairingsObject = true;
                  }
                });
              }
            });
            if (pairingsObject === true) {
              return this.$t('Pairings at that periods already exist');
            } else {
              this.isDuplicate = true;
              return true;
            }
          }
        } else {
          this.isDuplicate = true;
          return true;
        }
        return false;
      },
      getProject(projectName) {
        return this.allowedChildren.filter(val => val.name === projectName)[0];
      },
      projectPeriodDates() {
        const projectName = this.isEdit ? this.element.projectName : this.childInput;
        const projectData = this.viewMode === this.ViewMode.project ? this.element : this.getProject(projectName);
        if (this.isEdit) {
          let checkRanges = true;
          this.pairings.forEach(pair => {
            if (pair.id === this.element.id) {
              const checkSingleRange = [];
              const projectStartDate = moment(`${moment(projectData.startedAt).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
              const projectEndDate = moment(`${moment(projectData.endedAt).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
              pair.dateFrom.forEach((value, index) => {
                const startDate = moment(`${moment(this.dateStartHolder[index]).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
                const endDate = moment(`${moment(this.dateEndHolder[index]).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
                if (projectData.startedAt && projectData.endedAt) {
                  if (projectStartDate > startDate || projectEndDate < endDate) {
                    checkSingleRange.push(false);
                  }
                } else if (projectData.startedAt) {
                  const projectStartDate = moment(`${moment(projectData.startedAt).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
                  if (projectStartDate > startDate) {
                    checkSingleRange.push(false);
                  }
                }
              });
              if (checkSingleRange.length > 0) {
                checkRanges = false;
              }
            }
          });
          if (checkRanges) {
            this.projectPeriod = true;
            return true;
          } else {
            return this.$t('The range goes beyond the duration of the project');
          }
        }
        if (this.childInput) {
          const startDate = moment(`${moment(this.dateStartHolder).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
          const endDate = moment(`${moment(this.dateEndHolder).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
          if (projectData.startedAt && projectData.endedAt) {
            const projectStartDate = moment(`${moment(projectData.startedAt).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
            const projectEndDate = moment(`${moment(projectData.endedAt).format('MM-YYYY')}`, 'MM-YYYY').endOf('month');
            if (projectStartDate <= startDate && projectEndDate >= endDate) {
              this.projectPeriod = true;
              return true;
            } else {
              return this.$t('The range goes beyond the duration of the project');
            }
          } else if (projectData.startedAt) {
            const projectStartDate = moment(`${moment(projectData.startedAt).format('MM-YYYY')}`, 'MM-YYYY').startOf('month');
            if (projectStartDate <= startDate) {
              this.projectPeriod = true;
              return true;
            } else {
              return this.$t('The range goes beyond the duration of the project');
            }
          } else {
            this.projectPeriod = true;
            return true;
          }
        } else {
          this.projectPeriod = true;
          return true;
        }
      },
      validateMyInput() {
        this.isValidate = this.validEditedDates;
        this.isDuplicate = this.isDuplicatePairings;
        this.projectPeriod = this.projectPeriodDates;
      },
      save() {
        let pair = {};
        if (this.isEdit) {
          pair.id = this.element.id;
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
          pair.dateFrom = dateFrom;
          pair.dateTo = dateTo;
          this.$store.dispatch('Planner/editPairing', pair);
        }
        if (!this.isEdit) {
          const result = this.allowedChildren
            .filter(element => this.viewMode.opposite.createElementLabel(element) === this.childInput);
          if (result.length === 1) {
            const objectToPair = result[0];
            const elementFieldName = this.viewMode.value;
            const childFieldName = elementFieldName === 'employee' ? 'project' : 'employee';
            const elementFieldName2 = `${this.viewMode.value}Id`;
            const childFieldName2 = elementFieldName2 === 'employeeId' ? 'projectId' : 'employeeId';
            const existPairings = this.pairings.filter(pairings => pairings[elementFieldName2] === this.element.id
              && pairings[childFieldName2] === objectToPair.id);
            if (existPairings.length > 0) { // if employeeProject pair already exist
              pair.dateFrom = [];
              pair.dateTo = [];
              const oldDatesFrom = existPairings[0].dateFrom || [];
              for (const dateFrom of oldDatesFrom) {
                pair.dateFrom.push(dateFrom);
              }
              const oldDatesTo = existPairings[0].dateTo || [];
              for (const dateTo of oldDatesTo) {
                pair.dateTo.push(dateTo);
              }
              pair.dateFrom.push(moment(this.dateStartHolder).format('MM-YYYY'));
              pair.dateTo.push(moment(this.dateEndHolder).format('MM-YYYY'));
              pair.id = existPairings[0].id;
              this.$store.dispatch('Planner/addPairing', pair);
            } else {
              pair = {
                dateFrom: moment(this.dateStartHolder).format('MM-YYYY'),
                dateTo: moment(this.dateEndHolder).format('MM-YYYY'),
                [elementFieldName]: this.element,
                [childFieldName]: objectToPair,
              };
              this.$store.dispatch('Planner/createPairing', pair);
            }
          }
        }
      },
      toDeleteAll() {
        const pair = { id: this.element.id };
        this.dialog = false;
        EventBus.$emit(eventNames.escapePressed);
        this.$store.dispatch('Planner/deletePairing', pair);
      },
      dialogSingleDeletions(index) {
        this.dialogSingleVisible[0] = true;
        this.dialogSingleVisible.push(index);
      },
      toDeleteSingle(index) {
        if (this.dateStartHolder.length === 1) {
          this.toDeleteAll();
          return;
        }
        const dateFrom = [];
        this.dateStartHolder.forEach((val, idx) => {
          if (index !== idx) {
            const [year, month] = val.split('-');
            dateFrom.push(`${month}-${year}`);
          }
        });
        const dateTo = [];
        this.dateEndHolder.forEach((val, idx) => {
          if (index !== idx) {
            const [year, month] = val.split('-');
            dateTo.push(`${month}-${year}`);
          }
        });
        const pair = {
          id: this.element.id,
          dateFrom,
          dateTo,
          element: this.element,
          parentElement: this.parentElement,
        };
        EventBus.$emit(eventNames.escapePressed);
        this.$store.dispatch('Planner/editPairing', pair);
        this.dialogSingleVisible = [false];
      },
      show(data) {
        this.isEdit = false;
        this.dateStartHolder = moment().format('YYYY-MM');
        this.dateEndHolder = moment().format('YYYY-MM');
        if (typeof (data.parentElement) !== 'undefined') {
          this.parentElement = data.parentElement;
          this.isEdit = true;
          const tmpDateFrom = [];
          const tmpDateTo = [];
          data.element.dateFrom.forEach(val => {
            const [month, year] = val.split('-');
            tmpDateFrom.push(`${year}-${month}`);
          });
          this.dateStartHolder = tmpDateFrom;
          data.element.dateTo.forEach(val => {
            const [month, year] = val.split('-');
            tmpDateTo.push(`${year}-${month}`);
          });
          this.dateEndHolder = tmpDateTo;
        }
        this.element = data.element;
        this.childInput = '';
      },
    },
    i18n: {
      messages: {
        pl: {
          'Are you sure you want to unassign all project from employee?':
            'Jesteś pewien, że chcesz odłączyć wszystkie okresy projektu od osoby?',
          'Are you sure you want to unassign all employee from project? ':
            'Jesteś pewien, że chcesz odłączyć wszystkie okresy osoby od projektu?',
          'Are you sure you want to unassign project from employee?':
            'Jesteś pewien, że chcesz odłączyć okres projektu od osoby?',
          'Are you sure you want to unassign employee from project?':
            'Jesteś pewien, że chcesz odłączyć okres osoby od projektu?',
          'All history of entries will be deleted': 'Historia wpisów zostanie usunięta',
          'Back': 'Cofnij',
          'Delete': 'Usuń',
          'Employee': 'Osoba',
          'Employee - project assignation': 'Przydział osoba - projekt',
          'Employee - project edition': 'Edycja osoba - projekt',
          'End of project': 'Koniec pracy w projekcie',
          'Incorrect dates': 'Niepoprawne daty',
          'Month': 'Miesiąc',
          'Pairings at that periods already exist': 'Przydział w tym okresie już istnieje',
          'Project': 'Projekt',
          'Unassign all project from employee': 'Odłącz wszystkie okresy projektu od osoby',
          'Unassign all employee from project': 'Odłącz wszystkie okresy osoby od projektu',
          'Unassign project from employee': 'Odłącz okres projektu od osoby',
          'Unassign employee from project': 'Odłącz okres osoby od projektu',
          'Other pairings at that periods already exist': 'Przydział w tym okresie już istnieje',
          'Start of project': 'Start pracy w projekcie',
          'Year': 'Rok',
          'Wrong date': 'Niepoprawna data',
          'No data available': 'Brak dostępnych danych',
          'The range goes beyond the duration of the project': 'Przedział wykracza poza czas trwania projektu',
          'date': {
            months: [
              'Styczeń',
              'Luty',
              'Marzec',
              'Kwiecień',
              'Maj',
              'Czerwiec',
              'Lipiec',
              'Sierpień',
              'Wrzesień',
              'Październik',
              'Listopad',
              'Grudzień',
            ],
          },
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
