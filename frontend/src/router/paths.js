/* eslint-disable */
export default [
    {
        path: '*',
        meta: { public: true },
        redirect: { path: '/404' }
    },{
        path: '/404',
        meta: { public: true },
        name: 'NotFound',
        component: () => import(`@/pages/NotFound.vue`)
    },{
        path: '/403',
        meta: { public: true },
        name: 'AccessDenied',
        component: () => import(`@/pages/Deny.vue`)
    },{
        path: '/500',
        meta: { public: true },
        name: 'ServerError',
        component: () => import(`@/pages/Error.vue`)
    },{
        path: '/login',
        meta: { public: true },
        name: 'Login',
        component: () => import(`@/pages/Login.vue`)
    },{
        path: '/logout',
        meta: { public: true },
        name: 'Logout',
        component: () => import('@/pages/Login.vue')
    },{
        path: '/',
        name: 'Root',
        redirect: { name: 'Dashboard'}
    },{
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import(`@/pages/Dashboard.vue`),
    },{
        path: '/firm/projects/:id?',
        name: 'firm/projects',
        component: () => import(`@/pages/firm/Projects.vue`)
    },{
        path: '/firm/employees/:id?',
        name: 'firm/employees',
        component: () => import(`@/pages/firm/Employees.vue`)
    },{
        path: '/employee/:id',
        name: 'firm/employee',
        redirect: '/firm/employees/:id'
    },{
        path: '/firm/tribes/:id?',
        name: 'firm/tribes',
        component: () => import(`@/pages/firm/Tribes.vue`)
    },{
        path: '/faq/:id?',
        name: 'faq',
        component: () => import('@/pages/Faq.vue')
    },{
        path: '/evidence',
        name: 'evidence',
        meta: { getter: 'Evidences/canUseEvidences' },
        component: () => import('@/pages/Evidences.vue')
    },{
        path: '/overtime',
        name: 'overtime',
        meta: { getter: 'Evidences/canUseOvertime' },
        component: () => import('@/pages/Overtime.vue')
    },{
        path: '/agreements/general',
        name: 'agreements/general',
        component: () => import('@/pages/agreements/General.vue')
    },{
        path: '/agreements/marketing',
        name: 'agreements/marketing',
        component: () => import('@/pages/agreements/Marketing.vue')
    },{
        path: '/agreements/acceptations',
        name: 'agreements/acceptations',
        meta: { role: 'ROLE_HR' },
        component: () => import('../pages/agreements/AgreementAcceptation.vue')
    },{
        path: '/free-days',
        name: 'free-days',
        component: () => import('@/pages/freeDays/OwnPeriods.vue')
    },{
        path: '/free-days/:id',
        name: 'free-days-id',
        meta: { role: 'ROLE_MANAGER' },
        component: () => import('@/pages/freeDays/PeriodsById.vue')
    },{
        path: '/requests',
        name: 'requests',
        meta: { role: 'ROLE_MANAGER' },
        component: () => import('../pages/RequestsList')
    },{
        path: '/free-days-dashboard',
        name: 'free-days-dashboard',
        component: () => import(`@/pages/freeDays/Dashboard.vue`)
    },{
        path: '/positions',
        name: 'positions',
        meta: { role: 'ROLE_TRIBE_MASTER' },
        component: () => import('../pages/Positions.vue')
    },{
        path: '/scheduler',
        name: '@divante-adventure/planner',
        meta: { role: 'ROLE_MANAGER' },
        component: () => import(`@/pages/scheduler/Scheduler.vue`)
    },{
        path: '/scheduler-stats',
        name: 'schedulerStats',
        meta: { role: 'ROLE_MANAGER' },
        component: () => import(`@/pages/scheduler/Stats.vue`)
    },{
        path: '/hr/personsList',
        name: 'personsList',
        meta: { role: 'ROLE_HR' },
        component: () => import(`@/pages/hr/PersonsList.vue`)
    }, {
        path: '/hr/monthly',
        meta: { breadcrumb: true, role: 'ROLE_HR' },
        name: 'monthlyReport',
        component: () => import('@/pages/hr/MonthlyReport.vue')
    }, {
        path: '/hr/tribeStats',
        meta: { breadcrumb: true, role: 'ROLE_HR' },
        name: 'tribeStats',
        component: () => import('@/pages/hr/TribeStats.vue')
    }, {
        path: '/hr/rotation',
        meta: { breadcrumb: true, role: 'ROLE_HR' },
        name: 'rotationCard',
        component: () => import('@/pages/hr/RotationCard.vue')
    }, {
        path: '/hr/checklists',
        meta: { breadcrumb: true, role: 'ROLE_HR' },
        name: 'checklists',
        component: () => import('@/pages/hr/ChecklistTemplates.vue')
    }, {
        path: '/checklists',
        name: 'checklists',
        component: () => import('@/pages/Checklists.vue')
    }, {
        path: '/myChecklists',
        name: 'myChecklists',
        component: () => import('@/pages/MyChecklists.vue')
    },
    {
        path: '/question/update/:checklistId?/:taskId?',
        name: 'question/update',
        component: () => import(`@/pages/Dashboard.vue`),
    },
    {
        path: '/hardware',
        name: 'hardware',
        meta: { role: 'ROLE_HELPDESK' },
        component: () => import(`@/pages/hardware/HardwareAgreements.vue`),
    },
    {
        path: '/feedback',
        name: 'feedback',
        component: () => import(`@/pages/Feedback.vue`),
    },
    {
        path: '/configuration',
        name: 'configuration',
        meta: { role: 'ROLE_SUPER_ADMIN' },
        component: () => import(`@/pages/Configuration.vue`),
    },
    {
        path: '/dictionaries',
        name: 'dictionaries',
        meta: { role: 'ROLE_SUPER_ADMIN' },
        component: () => import(`@/pages/Dictionaries.vue`),
    },
    {
        path: '/thanks',
        name: 'thanks',
        meta: { public: true },
        component: () => import(`@/pages/Thanks.vue`)
    },
];
