import { shallowMount, mount } from '@vue/test-utils';
import ChecklistTemplatesListMenu
    from '../../../src/components/hr/checklistTemplates/checklistTemplatesView/ListMenu.vue';
import Vuetify from 'vuetify';
import Vue from 'vue';
import i18n from '../../../src/i18n/i18n';

Vue.use(Vuetify);

function createConfig(overrides) {
    const mocks = {
        props: {
            visible: false,
        },
        i18n,
    };
    return Object.assign({ mocks }, overrides);
}

describe('ChecklistTemplatesCard.vue', () => {
    test('selector exists', () => {
        const config = createConfig();
        const wrapper = shallowMount(ChecklistTemplatesListMenu, config);
        expect(wrapper.find('#checklist-templates-list-menu').exists()).toBeTruthy();
    });
    test('renders correctly', () => {
        const config = createConfig();
        const wrapper = shallowMount(ChecklistTemplatesListMenu, config);
        expect(wrapper.element).toMatchSnapshot();
    });
    test('dialog dont exist, visible set to false', () => {
        const config = createConfig();
        const wrapper = shallowMount(ChecklistTemplatesListMenu, config);
        expect(wrapper.find('#checklist-templates-list-menu').exists()).toBeTruthy();
    });
    test('dialog visible correctly, visible set to true', () => {
        const wrapper = mount(ChecklistTemplatesListMenu, { mocks: {
            props: {
                    visible: true,
            },
        } });
        expect(wrapper.find('#loading-dialog').exists()).toBeTruthy();
    });
});
