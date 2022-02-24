<template>
  <tr>
    <td class="centered">
      {{ item.code }}
    </td>
    <td class="centered">
      {{ item.name }}
    </td>
    <td class="centered">
      {{ item.description }}
    </td>
  </tr>
</template>

<script>
  export default {
    name: 'ContractsTypeRow',
    props: {
      item: { type: Object, required: true },
    },
  };
</script>
