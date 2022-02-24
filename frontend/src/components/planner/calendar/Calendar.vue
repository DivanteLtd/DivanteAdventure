<template>
  <div data-app>
    <filter-field/>
    <v-data-table mobile-breakpoint="0" :items="listStructure"
                  :custom-sort="sort"
                  :headers="headers"
                  @update:page="moveToTop"
                  ref="dTable"
                  show-expand hide-default-header>
      <template slot="pageText" slot-scope="props">{{ $t('page-text', props) }}</template>
      <v-alert slot="no-data" :value="true" type="error" color="red">{{ $t('No data available') }}</v-alert>
      <template v-slot:header="{ props: { headers } }">
        <header-date-row :items="headers"/>
      </template>
      <template v-slot:item="{ item, expand, isExpanded }">
        <primary-row :element="item"
                     :expandable="item.children.length > 0"
                     @switched-visibility="expand(!isExpanded)"/>
      </template>
      <template v-slot:expanded-item="{ item, headers }">
        <td :colspan="headers.length" class="pa-0">
          <v-data-table mobile-breakpoint="0" v-if="item.children.length > 0"
                        :items="item.children"
                        hide-default-header hide-default-footer no-data-text="">
            <template v-slot:body="{ items }">
              <secondary-row v-for="(childItem, index) in items"
                             :key="index" :element="childItem"
                             :parent-element="item"/>
            </template>
          </v-data-table>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import { viewModeMixin, currentDateMixin, isoFreeDaysMixin, timeModeMixin } from '../../../mixins/PlannerMixins';
  import { mapGetters } from 'vuex';
  import moment from '@divante-adventure/work-moment';
  import ViewMode from '../../../util/planner/ViewMode';
  import PrimaryRow from './PrimaryRow';
  import SecondaryRow from './SecondaryRow';
  import HeaderDateRow from './HeaderDateRow';
  import FilterField from '../filters/FilterField';

  export default {
    name: 'Calendar',
    components: { HeaderDateRow, SecondaryRow, PrimaryRow, FilterField },
    mixins: [ viewModeMixin, currentDateMixin, isoFreeDaysMixin, timeModeMixin ],
    data() {
      return {
        pagination: {
          sortBy: 'name',
          descending: false,
          rowsPerPage: 10,
        },
      };
    },
    computed: {
      ...mapGetters({
        headersByView: 'Planner/Time/headersByView',
        listStructure: 'Planner/listStructure',
      }),
      headers() {
        const headers = this.timeMode.displayControl.createHeaders(moment(this.currentDate), this.isoFreeDays);
        const mainHeader = [
          { text: '', value: 'name', sortable: true },
          { text: this.$t('Summary'), value: 'summary', sortable: true },
        ];
        return mainHeader.concat(headers);
      },
      expanded() {
        return this.listStructure.filter(val => val.children.length > 0);
      },
    },
    watch: {
      viewMode() {
        this.collapseAll();
      },
    },
    methods: {
      collapseAll() {
        for (let i = 0; i < this.listStructure.length; i += 1) {
          const item = this.listStructure[i];
          this.$set(this.$refs.dTable.expanded, item.id, false);
        }
      },
      updateOrder(data) {
        this.pagination.sortBy = data.sortBy;
        this.pagination.descending = data.descending;
      },
      sort(items, index, isDescending) {
        switch(index) {
          case 'name': return this.sortByName(items, isDescending);
          case 'summary': return this.sortBySummary(items, isDescending);
          default: return items;
        }
      },
      sortByName(items, isDescending) {
        if (this.viewMode === ViewMode.employee) {
          return items.sort((a, b) => {
            const lastNameCompare = a.lastName.localeCompare(b.lastName);
            if (lastNameCompare === 0) {
              return a.name.localeCompare(b.name) * (isDescending ? -1 : 1);
            }
            return lastNameCompare * (isDescending ? -1 : 1);
          });
        } else {
          return items.sort((a, b) => a.name.localeCompare(b.name) * (isDescending ? -1 : 1));
        }
      },
      sortBySummary(items, isDescending) {
        return items.sort((a, b) => {
          const currentDate = moment(this.currentDate);
          const hoursA = this.timeMode.getWorkingHoursFromStore(currentDate, a, this.$store);
          const hoursB = this.timeMode.getWorkingHoursFromStore(currentDate, b, this.$store);
          return (hoursA - hoursB) * (isDescending ? -1 : 1);
        });
      },
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: {
      messages: {
        pl: {
          'No data available': 'Brak dostępnych danych',
          'Summary': 'Bilans',
          'All': 'Wszystkie',
          'Rows per page:': 'Wierszy na stronę:',
          'page-text': 'Wiersze {pageStart}-{pageStop} z {itemsLength}',
        },
        en: {
          'page-text': 'Rows {pageStart}-{pageStop} of {itemsLength}',
        },
      },
    },
  };
</script>
