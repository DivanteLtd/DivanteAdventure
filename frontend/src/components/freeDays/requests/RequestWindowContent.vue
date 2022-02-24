<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col cols="12" md="6">
        <request-timeline :request="request"/>
        <v-spacer cols="1"/>
        <v-textarea
          class="mr-2"
          :disabled="!commentEditingEnabled"
          v-model="request.comment"
          :label="$t('Comment')"
          rows="1"
          counter="100"
          auto-grow
          @keydown="limit( $event, 100)"
          single-line
        >
        </v-textarea>
      </v-col>
      <v-col cols="12" md="6">
        <request-days
          @dialogVisible="$emit('dialogVisible')"
          :resign-mode="resignMode"
          :request="request"
          @update="updateDay"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import RequestTimeline from './RequestTimeline';
  import RequestDays from './RequestDays';

  export default {
    name: 'RequestWindowContent',
    components: { RequestDays, RequestTimeline },
    props: {
      request: { type: Object, required: true },
      acceptanceMode: { type: Boolean, required: true },
      resignMode: { type: Boolean, required: true },
    },
    computed: {
      commentEditingEnabled() {
        return this.acceptanceMode || this.resignMode;
      },
    },
    methods: {
      updateDay(day) {
        this.request.days.forEach(val => {
          if (val === day) {
            val.deleted = !val.deleted;
            this.$emit('resignButton');
            return val;
          } else {
            return val;
          }
        });
      },
      limit(event, limit) {
        const ALLOWED_KEY = [8, 46];
        if (!ALLOWED_KEY.includes(event.keyCode)) {
          if (this.request.comment.length >= limit) {
            event.preventDefault();
          }
        }
      },
    },
    i18n: { messages: {
      pl: {
        Comment: 'Uwagi',
      },
    } },
  };
</script>
