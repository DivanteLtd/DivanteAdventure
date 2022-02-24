<template>
  <v-row no-gutters wrap>
    <v-col sm="12" :class="{'pb-2': $vuetify.breakpoint.smAndDown, 'pb-4': $vuetify.breakpoint.mdAndUp}">
      <v-tabs v-model="tab" centered>
        <v-tab>{{ $t('System configuration') }}</v-tab>
        <v-tab>{{ $t('Content') }}</v-tab>
        <v-tab>{{ $t('Public holidays') }}</v-tab>
        <v-tab>{{ $t('Other') }}</v-tab>
        <v-tab-item>
          <v-card>
            <v-card-title>
              <h1 class="headline">{{ $t('System configuration') }}</h1>
            </v-card-title>
            <v-divider/>
            <v-card-text class="pa-0">
              <config-table :content="false" :loading="loading"/>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card>
            <v-card-title>
              <h1 class="headline">{{ $t('Content') }}</h1>
            </v-card-title>
            <v-divider/>
            <v-card-text class="pa-0">
              <config-table :content="true" :loading="loading"/>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card>
            <v-card-title>
              <h1 class="headline">{{ $t('Public holidays') }}</h1>
              <v-spacer/>
              <new-free-day-dialog v-if="showNewFreeDayDialog" v-model="showNewFreeDayDialog"/>
              <v-menu offset-y>
                <template v-slot:activator="{ on }">
                  <v-btn v-on="on" icon>
                    <v-icon>more_vert</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item @click="showNewFreeDayDialog = true">
                    <v-list-item-title>{{ $t('Add new holiday') }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-card-title>
            <v-divider/>
            <v-card-text class="pa-0">
              <free-days-table :loading="loading"/>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <other-settings :loading="loading"/>
        </v-tab-item>
      </v-tabs>
    </v-col>
  </v-row>
</template>

<script>
  import ConfigTable from './ConfigTable';
  import FreeDaysTable from './FreeDaysTable';
  import NewFreeDayDialog from './NewFreeDayDialog';
  import OtherSettings from './OtherSettings';

  export default {
    name: 'ConfigPageCard',
    components: { OtherSettings, NewFreeDayDialog, FreeDaysTable, ConfigTable },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        tab: null,
        showNewFreeDayDialog: false,
      };
    },
    i18n: {
      messages: {
        pl: {
          'System configuration': 'Konfiguracja systemu',
          'Public holidays': 'Dni ustawowo wolne',
          'Add new holiday': 'Dodaj nowe święto',
          'Other': 'Inne',
          'Content': 'Treści',
        },
      },
    },
  };
</script>
