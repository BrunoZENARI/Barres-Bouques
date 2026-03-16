<template>
        <p-toolbar class="mb-6 rounded-none">
            <template #start>
                <p-button label="Nouvel emprunt" size="small" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
            </template>

            <template #end>
                <p-button label="Refresh" icon="pi pi-refresh" size="small" severity="info" @click="initTable()" />
            </template>
        </p-toolbar>

        <p-datatable
            ref="dt"
            :value="loans"
            dataKey="id"
            paginator
            :rows="10"
            lazy
            v-model:filters="filters"
            paginatorPosition="top"
            :rowsPerPageOptions="[5, 10, 25, 50, 100, 500]"
            :loading="loading_table"
            @page="onPage($event)"
            @sort="onSort($event)"
            @filter="onFilter($event)"
            filterDisplay="menu"
            sortField="loan_date"
            :sortOrder="-1"
            :totalRecords="totalRecords"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="{first} à {last} sur {totalRecords} emprunts"
            size="small"
            class="border"
            scrollable
            scrollHeight="65vh"
        >
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h1 class="my-0 mx-2 text-xl font-semibold">Suivi des emprunts</h1>
                </div>
            </template>
            <template #empty>Aucun emprunt trouvé</template>
            <p-column field="user.nom" header="Emprunteur" sortable style="min-width: 12rem">
                <template #body="{data}">
                    {{ data.user.prenom }} {{ data.user.nom }}
                </template>
            </p-column>
            <p-column field="book.title" header="Ouvrage" sortable style="min-width: 16rem">
                <template #body="{data}">
                    {{ data.book.title }}
                </template>
            </p-column>
            <p-column field="loan_date" header="Date d'emprunt" sortable style="min-width: 10rem">
                <template #body="{data}">
                    {{ formatDate(data.loan_date) }}
                </template>
            </p-column>
            <p-column field="due_date" header="Date de retour prévue" sortable style="min-width: 10rem">
                <template #body="{data}">
                    <span :class="isOverdue(data) ? 'text-red-500 font-semibold' : ''">
                        {{ formatDate(data.due_date) }}
                    </span>
                </template>
            </p-column>
            <p-column field="return_date" header="Retourné le" sortable style="min-width: 10rem">
                <template #body="{data}">
                    <span v-if="data.return_date">{{ formatDate(data.return_date) }}</span>
                    <span v-else class="text-orange-500">En cours</span>
                </template>
            </p-column>
            <p-column :exportable="false" style="min-width: 10rem">
                <template #body="slotProps">
                    <p-button
                        v-if="!slotProps.data.return_date"
                        icon="pi pi-check"
                        size="small"
                        text
                        rounded
                        severity="success"
                        class="mr-2"
                        @click="returnBook(slotProps.data)"
                    />
                    <p-button icon="pi pi-trash" size="small" text rounded severity="danger" @click="confirmDeleteLoan(slotProps.data)" />
                </template>
            </p-column>
        </p-datatable>

        <p-dialog v-model:visible="loanDialog" :style="{ width: '40vw' }" :breakpoints="{ '1199px': '60vw', '575px': '90vw' }" header="Nouvel emprunt" :modal="true">
            <form autocomplete="off">
                <div class="grid grid-cols-1 gap-4 mt-4">
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputnumber id="user_id" v-model="loan.user_id" :invalid="submitted && !loan.user_id" fluid />
                            <label for="user_id">ID Utilisateur</label>
                        </p-floatlabel>
                        <small v-if="submitted && !loan.user_id" class="text-red-500">Utilisateur obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputnumber id="book_id" v-model="loan.book_id" :invalid="submitted && !loan.book_id" fluid />
                            <label for="book_id">ID Ouvrage</label>
                        </p-floatlabel>
                        <small v-if="submitted && !loan.book_id" class="text-red-500">Ouvrage obligatoire.</small>
                    </div>
                </div>
            </form>

            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="hideDialog" />
                <p-button label="Enregistrer" icon="pi pi-check" @click="saveLoan" :loading="load_button_save" />
            </template>
        </p-dialog>

        <p-dialog v-model:visible="deleteLoanDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>êtes-vous sûr(e) de vouloir supprimer cet emprunt ?</span>
            </div>
            <template #footer>
                <p-button label="Non" icon="pi pi-times" text @click="deleteLoanDialog = false" />
                <p-button label="Oui" icon="pi pi-check" @click="deleteLoan" :loading="load_button_delete" />
            </template>
        </p-dialog>
</template>

<script>
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

export default {
    data() {
        return {
            loans: [],
            loan: {},
            lazyParams: {},
            filters: null,
            loading_table: true,
            load_button_save: false,
            load_button_delete: false,
            submitted: false,
            loanDialog: false,
            deleteLoanDialog: false,
            totalRecords: 0,
        };
    },
    mounted() {
        this.initTable();
    },
    created() {
        this.initFilters();
    },
    methods: {
        initTable() {
            this.initFilters();
            this.lazyParams = {
                first: 0,
                rows: this.$refs.dt.rows,
                sortField: 'loan_date',
                sortOrder: -1,
                filters: this.filters,
            };
            this.loadLazyData();
        },
        loadLazyData() {
            this.loading_table = true;
            this.$axios.post('/api/loans/search', { lazyEvent: JSON.stringify(this.lazyParams) })
                .then(response => {
                    this.loans = response.data.payload;
                    this.totalRecords = response.data.count;
                })
                .finally(() => {
                    this.loading_table = false;
                });
        },
        onPage(event) {
            this.lazyParams = event;
            this.loadLazyData();
        },
        onSort(event) {
            this.lazyParams = event;
            this.loadLazyData();
        },
        onFilter() {
            this.lazyParams.filters = this.filters;
            this.loadLazyData();
        },
        openNew() {
            this.loan = {};
            this.submitted = false;
            this.loanDialog = true;
        },
        hideDialog() {
            this.submitted = false;
            this.loanDialog = false;
        },
        saveLoan() {
            this.submitted = true;

            if (this.loan?.user_id && this.loan?.book_id) {
                this.load_button_save = true;
                this.$axios.post('/api/loans/', { loan: JSON.stringify(this.loan) })
                    .then(() => {
                        this.loadLazyData();
                        this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Emprunt enregistré.', life: 3000 });
                    })
                    .finally(() => {
                        this.load_button_save = false;
                        this.loanDialog = false;
                        this.loan = {};
                    });
            }
        },
        returnBook(loan) {
            this.$axios.put(`/api/loans/${loan.id}`)
                .then(() => {
                    this.loadLazyData();
                    this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Retour enregistré.', life: 3000 });
                });
        },
        confirmDeleteLoan(loan) {
            this.loan = loan;
            this.deleteLoanDialog = true;
        },
        deleteLoan() {
            this.load_button_delete = true;
            this.$axios.delete(`/api/loans/${this.loan.id}`)
                .then(() => {
                    this.loadLazyData();
                    this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Emprunt supprimé.', life: 3000 });
                })
                .finally(() => {
                    this.load_button_delete = false;
                });
            this.loan = {};
            this.deleteLoanDialog = false;
        },
        isOverdue(loan) {
            return !loan.return_date && new Date(loan.due_date) < new Date();
        },
        initFilters() {
            this.filters = {};
        },
        formatDate(value) {
            if (!value) return '';
            return new Date(value).toLocaleDateString();
        },
    },
};
</script>
