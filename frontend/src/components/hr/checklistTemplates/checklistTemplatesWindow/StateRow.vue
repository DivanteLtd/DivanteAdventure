<template>
  <tr>
    <td class="text-xs-center">{{ item.label_pl }}</td>
    <td class="text-xs-center">{{ item.label_en }}</td>
    <td class="text-xs-center">
      {{ item.default ? $t('Yes') : $t('No') }}
    </td>
    <td class="text-xs-center">
      {{ item.done ? $t('Yes') : $t('No') }}
    </td>
    <td class="text-xs-center"><v-icon :color="item.color">{{ item.icon }}</v-icon></td>
    <td class="justify-center layout px-0">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="editItem(item)" icon><v-icon>edit</v-icon></v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
      <confirm-dialog v-if="deleteDialogVisible"
                      v-model="deleteDialogVisible"
                      @yes="deleteItem(item)"
                      :question="$t('Do you really want to delete this task?')"
                      yes-color="red"/>
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="deleteDialogVisible = true" icon><v-icon>delete</v-icon></v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import ConfirmDialog from '../../../utils/ConfirmDialog';

  export default {
    name: 'StateRow',
    components: { ConfirmDialog },
    props: {
      item: { type: Object, required: true },
      united: { required: true, type: Boolean },
    },
    data() {
      return {
        deleteDialogVisible: false,
      };
    },
    methods: {
      editItem(item) {
        this.$emit('editItem', item);
      },
      deleteItem(item) {
        this.$emit('deleteItem', item);
      },
    },
    i18n: {
      messages: {
        pl: {
          Delete: 'Usuń',
          Edit: 'Edytuj',
          Yes: 'Tak',
          No: 'Nie',
        },
      },
    },
  };
</script>
