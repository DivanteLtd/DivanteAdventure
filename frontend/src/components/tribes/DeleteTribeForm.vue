<template>
  <confirm-dialog
    v-if="dialogVisible"
    v-model="dialogVisible"
    :width="400"
    :title="this.$t('Delete tribe')"
    :question="this.$t('Are you sure, you want to delete this tribe? ' +
      'All people from the tribe will remain unassigned.')"
    @yes="deleteTribe"
    yes-color="red"
    @no="dialogVisible = false"
  />
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import ConfirmDialog from '../utils/ConfirmDialog.vue';

  export default {
    name: 'DeleteTribeForm',
    components: { ConfirmDialog },
    data() { return {
      dialogVisible: false,
      id: 0,
      formText: '',
      actionText: '',
    };},
    methods: {
      show(id) {
        this.id = id;
        this.dialogVisible = true;
      },
      async deleteTribe() {
        try {
          await this.$store.dispatch('Tribes/delete', this.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('Tribe has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Tribe can not be deleted'),
            color: 'error',
          });
        }
        this.dialogVisible = false;
        EventBus.$emit(eventNames.escapePressed);
      },
    },
    mounted() {
      EventBus.$on(eventNames.deleteTribeForm, this.show);
    },
    i18n: { messages: {
      pl: {
        'Delete tribe': 'Usuń plemię',
        'Cancel': 'Anuluj',
        'Are you sure, you want to delete this tribe? All people from the tribe will remain unassigned.': 'Czy na pewno chcesz usunąć to plemię? Wszystkie osoby z plemienia pozostaną bez przypisania.',
        'Delete': 'Usuń',
        'Tribe has been deleted': 'Plemię zostało usunięte',
        'Tribe can not be deleted': 'Plemię nie zostało usunięte',
      },
    } },
  };
</script>
