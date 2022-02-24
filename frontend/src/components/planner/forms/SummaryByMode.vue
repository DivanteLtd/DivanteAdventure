<template>
  <dialog-form :show-event="showEvent" :title="$t('SummaryByMode')" max-width="400" @show="show" @save="save">
    <v-col md="4">
      {{ $t('Date') }}
    </v-col>
    <v-col md="8">
      {{ displayDate }}
    </v-col>
    <v-col md="4">
      {{ viewMode === ViewMode.project ? $t('Project') : $t('Employee') }}
    </v-col>
    <v-col md="8">
      {{ viewMode.createElementLabel(primaryElement) }}
    </v-col>
    <v-col md="12">
      <v-divider></v-divider>
    </v-col>
    <v-col md="4">
      <b>{{ viewMode === ViewMode.project ? $t('Employee') : $t('Project') }}</b>
    </v-col>
    <v-col md="8">
      <b>{{ $t('Hours') }}</b>
    </v-col>
    <v-col md="12">
      <v-row no-gutters wrap class="pa-0">
        <v-col md="4" class="mt-2 pa-0">
          {{ viewMode.opposite.createElementLabel(connectedElement) }}
        </v-col>
        <v-col md="8" class="ma-0 pa-0">
          <v-autocomplete v-model="summaryByMode"
                          class="ma-0 pa-0"
                          :items="allowedSummaryByMode"
                          item-text="Description"
                          item-value="entry"
                          :no-data-text="$t('No data available')"
                          required/>
        </v-col>
      </v-row>
    </v-col>
  </dialog-form>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { eventNames } from '../../../eventbus';
  import ViewMode from '../../../util/planner/ViewMode';
  import DialogForm from './DialogForm';
  import DataMode from '../../../util/planner/DataMode';
  import { allModesMixin } from '../../../mixins/PlannerMixins';

  const SECONDS_IN_HOUR = 3600;

  export default {
    name: 'SummaryByMode',
    components: { DialogForm },
    mixins: [ allModesMixin ],
    data() { return {
      showEvent: eventNames.summaryByMode,
      startDate: moment(),
      endDate: moment(),
      businessDays: 0,
      availableWorkingTime: 0,
      summaryByMode: 0,
      primaryElement: {},
      connectedElement: {},
      shortDateFormat: 'DD.MM.YYYY',
      ViewMode,
    };},
    computed: {
      displayDate() {
        return this.startDate.format(this.shortDateFormat);
      },
      allowedSummaryByMode() {
        const allowedEntry = [];
        let jobTimeValue = this.availableWorkingTime / this.businessDays;
        if (this.dataMode === DataMode.worktime) {
          jobTimeValue *= 8;
        }
        for (let i = 0; i <= jobTimeValue; i++) {
          allowedEntry.push(this.businessDays * i);
        }
        return allowedEntry.map(entry => {
          const Description = this.$t('hours_per_day', [entry, entry / this.businessDays]);
          return Object.assign({}, { entry }, { Description });
        });
      },
    },
    methods: {
      save() {
        let jobTimeValue = this.availableWorkingTime / this.businessDays * 3600;
        if (this.dataMode === DataMode.worktime) {
          jobTimeValue *= 8;
        }
        const eventData = {
          entries: this.$store.state.Planner.entries,
          workTime: jobTimeValue,
          start: this.startDate,
          end: this.endDate,
          employeeId: this.viewMode === ViewMode.employee ? this.primaryElement.id : this.connectedElement.employeeId,
          projectId: this.viewMode !== ViewMode.employee ? this.primaryElement.id : this.connectedElement.projectId,
          occupancy: this.summaryByMode * SECONDS_IN_HOUR / this.businessDays,
        };
        this.$store.dispatch('Planner/prepareRangeEntries', eventData);
      },
      show(data) {
        this.endDate = data.endDate;
        this.startDate = data.startDate;
        this.businessDays = data.businessDays;
        this.primaryElement = data.primaryElement;
        this.connectedElement = data.connectedElement;
        this.summaryByMode = data.summaryByMode;
        this.availableWorkingTime = data.availableWorkingTime;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Date': 'Data',
          'Employee': 'Osoba',
          'hours_per_day': '{0} ({1} h dziennie)',
          'Hours': 'Godziny',
          'Invalid value': 'Niepoprawna wartość',
          'No data available': 'Wartość nie jest dostępna',
          'Project': 'Projekt',
          'SummaryByMode': 'Bilans',
        },
        en: {
          hours_per_day: '({0} ({1} h a day)',
        },
      },
    },
  };
</script>
