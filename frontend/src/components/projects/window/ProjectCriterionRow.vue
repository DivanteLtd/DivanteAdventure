<template>
  <tr>
    <td v-if="language === 'pl'">
      {{ criterion.namePl }}
    </td>
    <td v-if="language === 'en'">
      {{ criterion.nameEn }}
    </td>
    <td class="icons">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="editCriteria" icon>
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
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
      <confirm-dialog v-if="deleteDialogVisible"
                      v-model="deleteDialogVisible"
                      @yes="deleteCriteria"
                      :question="$t('Do you really want to delete this criterion?')"
                      yes-color="red"/>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import ConfirmDialog from '../../utils/ConfirmDialog';

  export default {
    name: 'ProjectCriterionRow',
    components: { ConfirmDialog },
    props: {
      criterion: { type: Object, required: true },
    },
    data() {
      return {
        language: getSuggestedLanguage(),
        deleteDialogVisible: false,
      };
    },
    methods: {
      editCriteria() {
        EventBus.$emit(eventNames.addCriteria, this.criterion);
      },
      async deleteCriteria() {
        this.deleteDialogVisible = false;
        try {
          await this.$store.dispatch('Criteria/deleteCriteria', this.criterion.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('Criterion has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Criterion cannot be deleted'),
            color: 'error',
          });
        }
        await this.$store.dispatch('Criteria/loadCriteria');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Edit': 'Edytuj',
          'Delete': 'Usuń',
          'Criterion has been deleted': 'Kryterium zostało usunięte',
          'Criterion cannot be deleted': 'Kryterium nie zostało usunięte',
          'Do you really want to delete this criterion?': 'Czy na pewno chcesz usunąć to kryterium?',
        },
      },
    },
  };
</script>
