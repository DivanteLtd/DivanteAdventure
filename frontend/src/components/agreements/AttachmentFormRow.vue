<template>
  <tr>
    <td>
      <span>
        {{ item.name }}
      </span>
    </td>
    <td class="pa-0">
      <confirm-dialog v-if="confirmDialogVisible"
                      v-model="confirmDialogVisible"
                      :question="$t('Are you sure you want to delete this attachment?')"
                      @yes="deleteAttachment"
                      yes-color="red"/>
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" :loading="deleteLoading" @click="confirmDialogVisible = true" icon>
            <v-icon>delete</v-icon>
          </v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import ConfirmDialog from '../utils/ConfirmDialog';

  export default {
    name: 'AttachmentFormRow',
    components: { ConfirmDialog },
    props: {
      item: { type: Object, required: true },
    },
    data() {
      return {
        confirmDialogVisible: false,
        deleteLoading: false,
      };
    },
    methods: {
      async deleteAttachment() {
        this.deleteLoading = true;
        try {
          await this.$store.dispatch('Agreements/deleteAttachment', this.item.id);
          this.$emit('deleted');
          this.$store.commit('showSnackbar', {
            text: this.$t('Attachment has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Attachment cannot be deleted'),
            color: 'error',
          });
        }
        this.deleteLoading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Delete': 'Usuń',
          'Attachment has been deleted': 'Załącznik został usunięty',
          'Attachment cannot be deleted': 'Załącznik nie został usunięty',
          'Are you sure you want to delete this attachment?': 'Czy na pewno chcesz usunąć ten załącznik?',
        },
      },
    },
  };
</script>
