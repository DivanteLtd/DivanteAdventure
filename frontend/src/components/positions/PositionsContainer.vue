<template>
  <v-card>
    <v-app-bar color="transparent" flat dense>
      <v-tabs v-model="selectedTab" centered>
        <v-tab>{{ $t('Leveling structures') }}</v-tab>
        <v-tab>{{ $t('Positions') }}</v-tab>
      </v-tabs>
      <v-spacer/>
      <v-menu offset-y>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" icon>
            <v-icon>more_vert</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="EventBus.$emit(eventNames.createStrategy)">
            {{ $t('Create new leveling structure') }}
          </v-list-item>
          <v-list-item @click="EventBus.$emit(eventNames.createPosition)">
            {{ $t('Create new position') }}
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-divider/>
    <v-card-text class="pa-0">
      <v-tabs-items touchless v-model="selectedTab">
        <v-tab-item>
          <strategies-tab :loading="loading"/>
        </v-tab-item>
        <v-tab-item>
          <positions-tab id="tab-positions" :loading="loading"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
  import StrategiesTab from './strategies/StrategiesTab';
  import PositionsTab from './positions/PositionsTab';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'PositionsContainer',
    components: { PositionsTab, StrategiesTab },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        selectedTab: 0,
        EventBus,
        eventNames,
      };
    },
    i18n: { messages: {
      pl: {
        'Leveling structures': 'Struktury poziomów',
        'Positions': 'Stanowiska',
        'Create new leveling structure': 'Utwórz nową strukturę poziomów',
        'Create new position': 'Utwórz nowe stanowisko',
      },
    },
    },
  };
</script>
