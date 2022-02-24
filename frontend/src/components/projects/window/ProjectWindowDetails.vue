<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col v-if="project.photo !== ''" cols="12" sm="6" md="5">
        <img v-if="project.photo"
             :src="project.photo"
             :alt="project.name"
             :title="project.name"
             @error="project.photo = ''"
             class="logo"/>
      </v-col>
      <v-col cols="12" sm="6" md="7">
        <v-list four-line>
          <v-list-item>
            <v-list-item-action><v-icon>web</v-icon></v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ project.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ project.description }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="project.url" :href="project.url">
            <v-list-item-action><v-icon>link</v-icon></v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ project.url }}</v-list-item-title>
              <v-list-item-subtitle>{{ $t('Web page') }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <project-time-info :project="project"/>
          <project-type-info :project="project"/>
          <v-list-item v-if="project.code">
            <v-list-item-action><v-icon>vpn_key</v-icon></v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ project.code }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item>
            <v-list-item-action>
              <v-icon v-if="project.billable">attach_money</v-icon>
              <v-icon v-if="!project.billable">money_off</v-icon>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import ProjectTimeInfo from './ProjectTimeInfo';
  import ProjectTypeInfo from './ProjectTypeInfo';

  export default {
    name: 'ProjectWindowDetails',
    components: { ProjectTypeInfo, ProjectTimeInfo },
    props: {
      project: { type: Object, required: true },
    },
    i18n: {
      messages: {
        pl: {
          'Web page': 'Strona internetowa',
        },
      },
    },
  };
</script>

<style scoped>
  img.logo {
    max-width: 100%;
    max-height: 300px;
  }
</style>
