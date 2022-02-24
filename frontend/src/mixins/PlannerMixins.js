import { mapState } from 'vuex';

export const dataModeMixin = {
  computed: {
    ...mapState({
      dataMode: state => state.Planner.dataMode,
    }),
  },
};

export const viewModeMixin = {
  computed: {
    ...mapState({
      viewMode: state => state.Planner.viewMode,
    }),
  },
};

export const timeModeMixin = {
  computed: {
    ...mapState({
      timeMode: state => state.Planner.Time.timeMode,
    }),
  },
};

export const allModesMixin = {
  computed: {
    ...mapState({
      dataMode: state => state.Planner.dataMode,
      viewMode: state => state.Planner.viewMode,
      timeMode: state => state.Planner.Time.timeMode,
    }),
  },
};

export const currentDateMixin = {
  computed: {
    ...mapState({
      currentDate: state => state.Planner.Time.currentDate,
    }),
  },
};

export const isoFreeDaysMixin = {
  computed: {
    ...mapState({
      isoFreeDays: state => state.Planner.Time.isoFreeDays,
    }),
  },
};
