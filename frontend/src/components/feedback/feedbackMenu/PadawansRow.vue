<template>
  <v-list-item class="pt-2" >
    <employee-chip v-if="typeof(item) === 'object'" :employee="item" />
    <v-tooltip right>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" :loading="loading()" @click="confirmDialogVisible = true" icon>
          <v-icon>delete</v-icon>
        </v-btn>
      </template>
      {{ $t('Delete') }}
    </v-tooltip>
    <confirm-dialog v-if="confirmDialogVisible"
                    v-model="confirmDialogVisible"
                    :question="$t('Are you sure you want to delete this person from Leader structure?')"
                    @yes="deletePadawan"
                    yes-color="red"/>
  </v-list-item>
</template>

<script>
  import EmployeeChip from '../../utils/EmployeeChip';
  import ConfirmDialog from '../../utils/ConfirmDialog';

  export default {
    name: 'PadawansRow',
    components: { EmployeeChip, ConfirmDialog },
    props: {
      item: { type: Object, required: true },
      leaderId: { type: Number, required: true },
    },
    data() {
      return {
        flag: false,
        confirmDialogVisible: false,
        deleteLoading: false,
        id: -1,
      };
    },
    methods: {
      loading() {
        if (this.id === 'undefined') {
          this.id = -1;
        }
        return this.item.id === this.id ? this.deleteLoading : false;
      },
      deletePadawan() {
        this.deleteLoading = true;
        this.id = this.item.id;
        const data = {
          padawanId: this.item.id,
          leaderId: this.leaderId,
        };
        this.$emit('delete', data);
      },
    },
    i18n: { messages: {
      pl: {
        'Delete': 'Usuń',
        'Are you sure you want to delete this person from Leader structure?': 'Czy na pewno chcesz usunąć tą osobę ze struktury lidera?',
      },
    },
    },
  };
</script>
