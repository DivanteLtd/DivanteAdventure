<template>
  <tr>
    <td>{{ item.name }}</td>
    <td>{{ item.came }}</td>
    <td>{{ item.left }}</td>
    <td>{{ item.current }}</td>
  </tr>
</template>

<script>
  export default {
    name: 'TribeStatsRow',
    props: {
      item: { type: Object, required: true },
    },
  };
</script>
 <style>
     @media only screen and (max-width: 600px) {
         .tribe-stats-table th {
             padding: 0 4px !important;
         }
         .tribe-stats-table td {
             padding: 0 4px !important;
             text-align: center;
         }
         .tribe-stats-table .tribe-name {
             margin: 0 !important;
             padding: 4px;
             display: flex;
         }
         .tribe-stats-table{
             margin-bottom: 12px;
         }
     }
     .tribe-stats-table td {
         text-align: center;
     }
 </style>
