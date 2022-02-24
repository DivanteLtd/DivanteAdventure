<template>
  <div>
    <day-occupancy/>
    <pairings-form/>
    <range-occupancy/>
    <summary-by-mode/>
    <save-filters/>
    <load-filter/>
  </div>
</template>

<script>
  import DayOccupancy from './DayOccupancy';
  import PairingsForm from './PairingsForm';
  import LoadFilter from './LoadFilter';
  import SaveFilters from './SaveFilters';
  import RangeOccupancy from './RangeOccupancy';
  import SummaryByMode from './SummaryByMode';

  export default {
    name: 'FormsContainer',
    components: { SummaryByMode, RangeOccupancy, SaveFilters, LoadFilter, PairingsForm, DayOccupancy },
  };
</script>
