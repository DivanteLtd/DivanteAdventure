<template>
  <tr>
    <td>{{ item.fullName }}</td>

    <td>{{ item.workHours }}</td>

    <td>{{ item.plannedHours }}</td>

    <td>{{ item.billableHours }}</td>

    <td>{{ item.noBillableHours }}</td>

    <td>{{ item.ratio }} %</td>
  </tr>
</template>

<script>
  export default {
    name: 'PlannerStatsTableRow',
    props: {
      item: { type: Object, required: true },
    },
  };
</script>
