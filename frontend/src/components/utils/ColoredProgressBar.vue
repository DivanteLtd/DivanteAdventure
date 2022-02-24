<template>
  <v-progress-linear height="6" :value="progressValue" :color="progressColor"/>
</template>

<script>
  import { getColorPercentage } from '../../util/colors';

  export default {
    name: 'ColoredProgressBar',
    props: {
      colors: { type: Array, default: () => ([ '#FF0000', '#FFFF00', '#00FF00' ]) },
      max: { type: Number, default: 100 },
      value: { type: Number, required: true },
    },
    computed: {
      progressValue() {
        return (this.value * 100) / this.max;
      },
      progressColor() {
        return getColorPercentage(this.progressValue, this.colors);
      },
    },
  };
</script>
