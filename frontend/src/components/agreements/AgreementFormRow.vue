<template>
  <tr>
    <td>
      {{ language === 'pl' ? agreement.descriptionPl : agreement.descriptionEn }}
    </td>
    <td>
      <div class="d-flex justify-center align-center">
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="editAgreement(agreement)" icon>
              <v-icon>edit</v-icon>
            </v-btn>
          </template>
          {{ $t('Edit') }}
        </v-tooltip>
        <v-tooltip right>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="deleteDialogVisible = true" icon>
              <v-icon>delete</v-icon>
            </v-btn>
            <confirm-dialog v-model="deleteDialogVisible"
                            v-if="deleteDialogVisible"
                            @yes="deleteAgreement(agreement.id)"
                            :question="$t('Are you sure you want to delete this agreement?')"
                            yes-color="red"/>
          </template>
          {{ $t('Delete') }}
        </v-tooltip>
      </div>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import ConfirmDialog from '../utils/ConfirmDialog';

  export default {
    name: 'AgreementFormRow',
    components: { ConfirmDialog },
    props: {
      agreement: { type: Object, required: true },
    },
    data() {
      return {
        language: getSuggestedLanguage(),
        deleteDialogVisible: false,
      };
    },
    methods: {
      async deleteAgreement(id) {
        try {
          await this.$store.dispatch('Agreements/deleteAgreement', id);
          this.$emit('reload');
          this.$store.commit('showSnackbar', {
            text: this.$t('Agreement has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Agreement can not be deleted'),
            color: 'error',
          });
        }
      },
      editAgreement(item) {
        EventBus.$emit(eventNames.agreementAttachmentEdit, item);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Agreement has been deleted': 'Zgoda została usunięta',
          'Agreement can not be deleted': 'Zgoda nie została usunięta',
          'Are you sure you want to delete this agreement?': 'Jesteś pewien, że chcesz usunąć tą zgodę?',
          'Delete': 'Usuń',
          'Edit': 'Edytuj',
        },
      },
    },
  };
</script>
