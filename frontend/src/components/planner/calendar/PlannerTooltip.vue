<template>
  <v-tooltip v-if="tooltipText !== ''"
             v-model="displayTooltip"
             bottom
  >
    <template v-slot:activator="{ on }">
      <div v-on="on"></div>
    </template>
    {{ tooltipText }}
  </v-tooltip>
</template>

<script>
  export default {
    name: 'PlannerTooltip',
    props: {
      cssClass: { type: String, required: true },
      timeMode: { type: Object, required: true },
      displayTooltip: { type: Boolean, required: true },
    },
    data() {
      return {
        tooltipPosition: { x: -1, y: -1 },
      };
    },
    computed: {
      tooltipText() {
        switch(this.cssClass) {
          case 'freeDay': return this.$t('Holiday');
          case 'sick-leave': return this.$t('Sick leave');
          case 'sick-leave-planned': return this.$t('Pending sick leave');
          case 'day-off': return this.$t('Day off');
          case 'day-off-planned': return this.$t('Pending day off request');
          case 'today':
            if (this.timeMode.value === 'day') {
              return this.$t('Today');
            } else if (this.timeMode.value === 'week') {
              return this.$t('This week');
            } else {
              return this.$t('This month');
            }
          default: return '';
        }
      },
    },
    watch: {
      displayTooltip() {
        if (typeof this.$el.getBoundingClientRect === 'function') {
          const viewportOffset = this.$el.getBoundingClientRect();
          this.tooltipPosition.x = viewportOffset.x;
          this.tooltipPosition.y = viewportOffset.y;
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Holiday': 'Dzień ustawowo wolny od pracy',
          'Sick leave': 'Zwolnienie lekarskie',
          'Pending sick leave': 'Oczekujące zwolnienie lekarskie',
          'Day off': 'Dzień wolny',
          'Pending day off request': 'Oczekujący wniosek urlopowy',
          'Today': 'Dzisiaj',
          'This week': 'Obecny tydzień',
          'This month': 'Obecny miesiąc',
        },
      },
    },
  };
</script>
