<template>
  <v-card-text :class="{'card-mobile': $vuetify.breakpoint.xs}">
    <v-text-field v-model="nameVal" :label="$t('Structure name')"/>
    <v-app-bar >
      <v-btn @click="levelsVal.push({ id: -1, name: '', employeeCount: 0 })" icon>
        <v-icon color="green">add</v-icon>
      </v-btn>
      <h2>{{ $t('Levels') }}</h2>
    </v-app-bar>
    <v-list>
      <v-list-item v-for="(level, index) in levelsVal" :key="index">
        <v-text-field solo v-model="level.name">
          <template slot="append">
            <v-btn :disabled="index === levelsVal.length - 1"
                   @click.stop="moveDown(index)"
                   icon>
              <v-icon>arrow_downward</v-icon>
            </v-btn>
            <v-btn :disabled="index === 0"
                   @click.stop="moveUp(index)"
                   icon>
              <v-icon>arrow_upward</v-icon>
            </v-btn>
            <v-btn v-if="canDelete"
                   :disabled="level.employeeCount > 0"
                   @click.stop="deleteLevel(index)"
                   icon>
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
        </v-text-field>
      </v-list-item>
    </v-list>
  </v-card-text>
</template>

<script>
  import { mapGetters } from 'vuex';

  function arrayMove(arr, oldIndex, newIndex) {
    if (newIndex >= arr.length) {
      let k = newIndex - arr.length + 1;
      while (k--) {
        arr.push(undefined);
      }
    }
    arr.splice(newIndex, 0, arr.splice(oldIndex, 1)[0]);
    return arr;
  }

  export default {
    name: 'StrategyWindowContent',
    props: {
      name: { type: String, default: '' },
      levels: { type: Array, default: () => ([]) },
      deletedLevels: { type: Array, default: () => ([]) },
    },
    computed: {
      ...mapGetters({
        canDelete: 'Authorization/isTribeMaster',
      }),
      nameVal: {
        get() {
          return this.name;
        },
        set(val) {
          this.$emit('update:name', val);
        },
      },
      levelsVal: {
        get() {
          return this.levels;
        },
        set(val) {
          this.$emit('update:levels', val);
        },
      },
      deletedLevelsVal: {
        get() {
          return this.deletedLevels;
        },
        set(val) {
          this.$emit('updated:deleted-levels', val);
        },
      },
    },
    methods: {
      deleteLevel(index) {
        const deleted = this.levelsVal.splice(index, 1);
        this.deletedLevelsVal.push(...deleted);
      },
      moveUp(index) {
        this.levelsVal = arrayMove(this.levelsVal, index, index - 1);
      },
      moveDown(index) {
        this.levelsVal = arrayMove(this.levelsVal, index, index + 1);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Structure name': 'Nazwa struktury',
          'Levels': 'Poziomy',
        },
      },
    },
  };
</script>
<style>
  .card-mobile .v-app-bar__content{
    padding: 0;
  }
  .card-mobile .v-list__tile{
    padding: 0;
  }
</style>
