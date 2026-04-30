/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { ref } from 'vue'
import { useAuthStore } from '@/store/auth'
import { definePreset } from '@primevue/themes';

import '../css/app.css';
import 'primeicons/primeicons.css'

import App from './components/app.vue';
import router from './router';
import store from './store';
import axios from 'axios';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Menubar from 'primevue/menubar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import FloatLabel from 'primevue/floatlabel';
import Toolbar from 'primevue/toolbar';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import MultiSelect from 'primevue/multiselect';
import DatePicker from 'primevue/datepicker';
import Checkbox from 'primevue/checkbox';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import AutoComplete from 'primevue/autocomplete';

import { root } from 'postcss';


const app = createApp(App);
app.config.globalProperties.$axios = axios;

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

app.use(router);
app.use(store);

app.config.globalProperties.$store = useAuthStore();
app.config.globalProperties.$hasPermission = function (value) {
    const authStore = useAuthStore()
    let res = false;
    authStore.permissions.forEach(item => {
        if (item === value) {
            res = true;
        }
    });
    return res;
};

app.use(PrimeVue, {
    theme: {
        preset: definePreset(Aura, {
            primitive: {
                emerald: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22' },
                green: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d', 950: '#052e16' },
                lime: { 50: '#f7fee7', 100: '#ecfccb', 200: '#d9f99d', 300: '#bef264', 400: '#a3e635', 500: '#84cc16', 600: '#65a30d', 700: '#4d7c0f', 800: '#3f6212', 900: '#365314', 950: '#1a2e05' },
                red: { 50: '#fef2f2', 100: '#fee2e2', 200: '#fecaca', 300: '#fca5a5', 400: '#f87171', 500: '#ef4444', 600: '#dc2626', 700: '#b91c1c', 800: '#991b1b', 900: '#7f1d1d', 950: '#450a0a' },
                orange: { 50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74', 400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c', 800: '#9a3412', 900: '#7c2d12', 950: '#431407' },
                amber: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f', 950: '#451a03' },
                yellow: { 50: '#fefce8', 100: '#fef9c3', 200: '#fef08a', 300: '#fde047', 400: '#facc15', 500: '#eab308', 600: '#ca8a04', 700: '#a16207', 800: '#854d0e', 900: '#713f12', 950: '#422006' },
                teal: { 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a', 950: '#042f2e' },
                cyan: { 50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc', 300: '#67e8f9', 400: '#22d3ee', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490', 800: '#155e75', 900: '#164e63', 950: '#083344' },
                sky: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' },
                blue: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#172554' },
                indigo: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b' },
                violet: { 50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9', 800: '#5b21b6', 900: '#4c1d95', 950: '#2e1065' },
                purple: { 50: '#faf5ff', 100: '#f3e8ff', 200: '#e9d5ff', 300: '#d8b4fe', 400: '#c084fc', 500: '#a855f7', 600: '#9333ea', 700: '#7e22ce', 800: '#6b21a8', 900: '#581c87', 950: '#3b0764' },
                fuchsia: { 50: '#fdf4ff', 100: '#fae8ff', 200: '#f5d0fe', 300: '#f0abfc', 400: '#e879f9', 500: '#d946ef', 600: '#c026d3', 700: '#a21caf', 800: '#86198f', 900: '#701a75', 950: '#4a044e' },
                pink: { 50: '#fdf2f8', 100: '#fce7f3', 200: '#fbcfe8', 300: '#f9a8d4', 400: '#f472b6', 500: '#ec4899', 600: '#db2777', 700: '#be185d', 800: '#9d174d', 900: '#831843', 950: '#500724' },
                rose: { 50: '#fff1f2', 100: '#ffe4e6', 200: '#fecdd3', 300: '#fda4af', 400: '#fb7185', 500: '#f43f5e', 600: '#e11d48', 700: '#be123c', 800: '#9f1239', 900: '#881337', 950: '#4c0519' },
                slate: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a', 950: '#020617' },
                gray: { 50: '#f9fafb', 100: '#f3f4f6', 200: '#e5e7eb', 300: '#d1d5db', 400: '#9ca3af', 500: '#6b7280', 600: '#4b5563', 700: '#374151', 800: '#1f2937', 900: '#111827', 950: '#030712' },
                zinc: { 50: '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7', 300: '#d4d4d8', 400: '#a1a1aa', 500: '#71717a', 600: '#52525b', 700: '#3f3f46', 800: '#27272a', 900: '#18181b', 950: '#09090b' },
                neutral: { 50: '#fafafa', 100: '#f5f5f5', 200: '#e5e5e5', 300: '#d4d4d4', 400: '#a3a3a3', 500: '#737373', 600: '#525252', 700: '#404040', 800: '#262626', 900: '#171717', 950: '#0a0a0a' },
                stone: { 50: '#fafaf9', 100: '#f5f5f4', 200: '#e7e5e4', 300: '#d6d3d1', 400: '#a8a29e', 500: '#78716c', 600: '#57534e', 700: '#44403c', 800: '#292524', 900: '#1c1917', 950: '#0c0a09' },
                custom_p: { 50: '#bfbfbf', 100: '#aeb4b7', 200: '#9da8af', 300: '#8b9da8', 400: '#7a91a0', 500: '#698698', 600: '#587b90', 700: '#476f88', 800: '#356481', 900: '#245879', 950: '#134d71' },
            },
            semantic: {
                primary: {
                    50: '{blue.50}',
                    100: '{blue.100}',
                    200: '{blue.200}',
                    300: '{blue.300}',
                    400: '{blue.400}',
                    500: '{blue.500}',
                    600: '{blue.600}',
                    700: '{blue.700}',
                    800: '{blue.800}',
                    900: '{blue.900}',
                    950: '{blue.950}'
                },
            },
        }),
        options: {
            cssLayer: {
                name: 'primevue',
                order: 'tailwind-base, primevue, tailwind-utilities'
            },
            darkModeSelector: '.system',
        }
    },
    locale: {
        startsWith: 'Commence par',
        contains: 'Contient',
        notContains: 'Ne contient pas',
        endsWith: 'Fini par',
        equals: 'Egal',
        notEquals: 'N\'est pas égal',
        noFilter: 'Pas de filtre',
        lt: 'Plus petit que',
        lte: 'Plus petit que ou égal à',
        gt: 'Plus grand que',
        gte: 'Plus grand que ou égal à',
        dateIs: 'Date égal à',
        dateIsNot: 'Date différente de',
        dateBefore: 'Date antérieure à',
        dateAfter: 'Date supérieure à',
        clear: 'Nettoyer',
        apply: 'Appliquer',
        matchAll: 'Correspond à tous',
        matchAny: 'Correspond à au moins un',
        addRule: 'Ajouter règle',
        removeRule: 'Supprimer règle',
        accept: 'Oui',
        reject: 'Non',
        choose: 'Parcourir',
        upload: 'Upload',
        cancel: 'Annuler',
        dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
        dayNamesShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
        monthNames: ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
        monthNamesShort: ["Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jul", "Aou", "Sep", "Oct", "Nov", "Dec"],
        today: 'Aujourd\'hui',
        weekHeader: 'Se',
        firstDayOfWeek: 1,
        dateFormat: 'dd/mm/yy',
        weak: 'Semaine',
        medium: 'Moyen',
        strong: 'Fort',
        passwordPrompt: 'Entrez un mot de passe',
        searchMessage: '{0} résultat(s) trouvé(s)',
        selectionMessage: '{0} séléctioné(s)',
        emptyFilterMessage: 'Pas de résultat trouvé',
        emptyMessage: 'Aucun option disponible',
        aria: {
            trueLabel: 'True',
            falseLabel: 'False',
            nullLabel: 'Pas sélécetioné',
            star: '1 étoile',
            stars: '{star} étoiles',
            selectAll: 'Tous séléctioné',
            unselectAll: 'Tous déséléctioné',
            close: 'Fermer',
            previous: 'Précedent',
            next: 'Suivant',
            navigation: 'Navigation',
            scrollTop: 'Scroll Top',
            moveTop: 'Move Top',
            moveUp: 'Move Up',
            moveDown: 'Move Down',
            moveBottom: 'Move Bottom',
            moveToTarget: 'Move to Target',
            moveToSource: 'Move to Source',
            moveAllToTarget: 'Move All to Target',
            moveAllToSource: 'Move All to Source',
            pageLabel: '{page}',
            firstPageLabel: 'First Page',
            lastPageLabel: 'Last Page',
            nextPageLabel: 'Next Page',
            prevPageLabel: 'Previous Page',
            rowsPerPageLabel: 'Rows per page',
            jumpToPageDropdownLabel: 'Jump to Page Dropdown',
            jumpToPageInputLabel: 'Jump to Page Input',
            selectRow: 'Row Selected',
            unselectRow: 'Row Unselected',
            expandRow: 'Row Expanded',
            collapseRow: 'Row Collapsed',
            showFilterMenu: 'Show Filter Menu',
            hideFilterMenu: 'Hide Filter Menu',
            filterOperator: 'Filter Operator',
            filterConstraint: 'Filter Constraint',
            editRow: 'Row Edit',
            saveEdit: 'Save Edit',
            cancelEdit: 'Cancel Edit',
            listView: 'List View',
            gridView: 'Grid View',
            slide: 'Slide',
            slideNumber: '{slideNumber}',
            zoomImage: 'Zoom Image',
            zoomIn: 'Zoom In',
            zoomOut: 'Zoom Out',
            rotateRight: 'Rotate Right',
            rotateLeft: 'Rotate Left'
        }
    },
    pt: {
        menubar: {
            root: 'rounded-none'
        },
        select: {
            label: 'py-1 px-1.5'
        },
        datatable: {
            tbody: 'text-sm',
            column: {
                pcColumnFilterButton: pcColumnFilterButton,
            }
        },
    }
});
app.use(ToastService);

// Custom component


app.component('p-inputtext', InputText);
app.component('p-password', Password);
app.component('p-button', Button);
app.component('p-menubar', Menubar)
app.component('p-datatable', DataTable)
app.component('p-column', Column)
app.component('p-toast', Toast);
app.component('p-floatlabel', FloatLabel)
app.component('p-toolbar', Toolbar)
app.component('p-dialog', Dialog)
app.component('p-select', Select)
app.component('p-multiselect', MultiSelect)
app.component('p-datepicker', DatePicker)
app.component('p-checkbox', Checkbox)
app.component('p-inputnumber', InputNumber)
app.component('p-textarea', Textarea)
app.component('p-autocomplete', AutoComplete)
app.directive('tooltip', Tooltip);

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    function (error) {
        if (error.response.status === 401 || error.response.status === 419) {
            const authStore = useAuthStore()
            authStore.logout();
        }
        if (error.response.status === 500) {
            app.config.globalProperties.$toast.add({ severity: 'error', summary: 'Erreur', detail: 'Une erreur s\'est produite sur le serveur, merci de contacter un administrateur' });
        }
        else {
            return Promise.reject(error.response)
        }
        //return Promise.reject(error.response);
    }
);

function pcColumnFilterButton(context) {
    //console.log(context)
    return {
        style: context.instance.hasFilter() ? 'background: var(--p-primary-100)' : '',
        class: context.instance.hasFilter() ? 'p-1' : 'p-1'
    }
}

app.mount('#app');

