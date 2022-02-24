<template>
  <div id="checklist-templates-list-menu">
    <v-menu offset-y>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item @click="createNewChecklistTemplate">
          <v-list-item-title>{{ $t('Add new checklist template') }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <loading-dialog :visible="visible"/>
  </div>
</template>

<script>
  import LoadingDialog from '../../../utils/LoadingDialog';
  import { EventBus, eventNames } from '../../../../eventbus';

  export default {
    name: 'ChecklistTemplateListMenu',
    components: { LoadingDialog },
    data() {
      return {
        visible: false,
      };
    },
    methods: {
      createNewChecklistTemplate() {
        EventBus.$emit(eventNames.showChecklistTemplatesWindow);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Add new checklist template': 'Dodaj nowy szablon checklist',
        },
      },
    },
  };
</script>
