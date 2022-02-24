<template>
  <tr>
    <template v-if="loading">
      <td colspan="10">
        <v-progress-linear height="6" indeterminate/>
      </td>
    </template>
    <template v-else >
      <td class="pointer" @click="editChecklistTemplates">
        {{ item[nameInSuggestedLanguage] }}
      </td>
      <td class="pointer" @click="editChecklistTemplates">
        {{ displayedType }}
      </td>
      <td class="icons">
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" icon @click="showDeleteDialog">
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          <span>{{ $t('Delete') }}</span>
        </v-tooltip>
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="getTasks" icon>
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <checklist-template-assign-dialog @editTemplate="editChecklistTemplates"
                                            v-if="showAssignDialog"
                                            :template="item"
                                            v-model="showAssignDialog"
          />
          <span>{{ $t('Assign to person') }}</span>
        </v-tooltip>
      </td>
    </template>
  </tr>
</template>
<script>
  import { ChecklistType } from '../../../../util/checklists';
  import { EventBus, eventNames } from '../../../../eventbus';
  import ChecklistTemplateAssignDialog from './AssignDialog';

  export default {
    name: 'ChecklistsListRow',
    components: { ChecklistTemplateAssignDialog },
    props: {
      item: { type: Object, required: true },
      checklists: { type: Boolean, default: false },
      language: { type: String, required: true },
    },
    data() {
      return {
        loading: false,
        showAssignDialog: false,
      };
    },
    computed: {
      nameInSuggestedLanguage() {
        return this.language === 'pl' ? 'namePl' : 'nameEn';
      },
      displayedType() {
        switch (this.item.type) {
          case ChecklistType.distributed: return this.$t('Distributed');
          case ChecklistType.united: return this.$t('United');
          default: return 'N/A';
        }
      },
    },
    methods: {
      async getTasks() {
        await this.$store.dispatch('Checklist/getQuestionsFromTemplate', this.item);
        this.showAssignDialog = true;
      },
      showDeleteDialog() {
        this.$emit('show-delete-dialog', this.item);
      },
      editChecklistTemplates() {
        EventBus.$emit(eventNames.showChecklistTemplatesWindow, this.item);
      },
    },
    i18n: { messages: {
      pl: {
        'Assign to person': 'Przypisz do osoby',
        'Distributed': 'Rozdzielona',
        'United': 'Złączona',
        'Delete': 'Usuń',
      },
    } },
  };
</script>
<style scoped>
    td{
        text-align: center;
    }
    .pointer{
        cursor: pointer;
    }
  .icons{
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
