<template>
  <tr style="border-bottom: none; background: none;">
    <td class="title">
      {{ category[$t('name-key')] }}
    </td>
    <td class="icons">
      <v-spacer/>
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="editCategory" icon>
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
                      @yes="deleteCategory"
                      :question="$t('Do you really want to delete this category? ' +
                        'If the category contains questions, they will also be deleted.')"
                      yes-color="red"/>
    </td>
  </tr>
</template>

<script>
  import ConfirmDialog from '../utils/ConfirmDialog';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'FAQCategoryRow',
    components: { ConfirmDialog },
    props: {
      category: { type: Object, required: true },
    },
    data() {
      return {
        deleteDialogVisible: false,
      };
    },
    methods: {
      editCategory() {
        return EventBus.$emit(eventNames.fAQCategoryForm, this.category);
      },
      async deleteCategory() {
        try {
          await this.$store.dispatch('FAQ/deleteFAQCategory', this.category.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('FAQ category have been deleted'),
            color: 'success',
          });
          EventBus.$emit(eventNames.reloadFAQContent);
          await this.$store.dispatch('FAQ/loadFAQCategories');
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('FAQ category cannot be deleted'),
            color: 'error',
          });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'name-key': 'namePl',
          'Do you really want to delete this category? If the category contains questions, they will also be deleted.':
            'Czy na pewno chcesz usunąć tą kategorię? Jeśli kategoria zawiera pytania, one również zostaną usunięte.',
          'FAQ category have been deleted': 'Kategoria FAQ została usunięta',
          'FAQ category cannot be deleted': 'Kategoria FAQ nie została usunięta',
        },
        en: {
          'name-key': 'nameEn',
        },
      },
    },

  };
</script>

<style>
  .icons {
    justify-content: flex-end;
    display: flex;
  }
</style>
