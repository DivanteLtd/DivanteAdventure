<template>
  <tr>
    <td class="text-center">
      <v-tooltip left max-width="300">
        <template v-slot:activator="{ on }">
          <span v-on="on">
            {{ item.namePl }}
          </span>
        </template>
        {{ item.namePl }}
      </v-tooltip>
    </td>
    <td class="text-center">
      <v-tooltip left max-width="300">
        <template v-slot:activator="{ on }">
          <span v-on="on">
            {{ item.nameEn }}
          </span>
        </template>
        {{ item.nameEn }}
      </v-tooltip>
    </td>
    <td class="text-center">
      <v-tooltip left max-width="300">
        <template v-slot:activator="{ on }">
          <span v-on="on">
            {{ item.descriptionPl }}
          </span>
        </template>
        {{ item.descriptionPl }}
      </v-tooltip>
    </td>
    <td class="text-center">
      <v-tooltip left max-width="300">
        <template v-slot:activator="{ on }">
          <span v-on="on">
            {{ item.descriptionEn }}
          </span>
        </template>
        {{ item.descriptionEn }}
      </v-tooltip>
    </td>
    <td v-if="!united" class="text-center">
      <v-tooltip left max-width="300">
        <template v-slot:activator="{ on }">
          <span v-on="on">
            {{ item.responsible.name }} {{ item.responsible.lastName }}
          </span>
        </template>
        {{ item.responsible.name }} {{ item.responsible.lastName }}
      </v-tooltip>
    </td>
    <td class="text-center">
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
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="deleteDialogVisible = true" icon><v-icon>delete</v-icon></v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import ConfirmDialog from '../../../utils/ConfirmDialog.vue';

  export default {
    name: 'QuestionRow',
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
          'Delete': 'Usuń',
          'Edit': 'Edytuj',
          'Do you really want to delete this task?': 'Czy na pewno chcesz usunąć to zadanie',
        },
      },
    },
  };
</script>
<style scoped>
  td {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 230px;
  }
</style>
