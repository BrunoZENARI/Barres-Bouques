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
            <p-column field="user.nom" header="Emprunteur" sortable filterField="user_search" style="min-width: 12rem" :showFilterOperator="false" :showAddButton="false">
                <template #body="{data}">
                    {{ data.user.prenom }} {{ data.user.nom }}
                </template>
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" placeholder="Nom ou prénom..." />
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

        <p-dialog v-model:visible="loanDialog" :style="{ width: '50vw' }" :breakpoints="{ '1199px': '70vw', '575px': '95vw' }" header="Nouvel emprunt" :modal="true">
            <div class="flex flex-col gap-5 mt-4">

                <!-- Adhérent -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adhérent <span class="text-red-500">*</span></label>
                    <p-autocomplete
                        v-model="loan.user"
                        :suggestions="userSuggestions"
                        @complete="searchUsers"
                        :optionLabel="u => `${u.nom} ${u.prenom} — ${u.email}`"
                        placeholder="Rechercher par nom ou email..."
                        :invalid="submitted && !loan.user"
                        fluid
                    />
                    <small v-if="submitted && !loan.user" class="text-red-500">Adhérent obligatoire.</small>
                </div>

                <!-- Ouvrages -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ouvrages <span class="text-red-500">*</span></label>
                    <p-autocomplete
                        v-model="bookSearch"
                        :suggestions="bookSuggestions"
                        @complete="searchBooks"
                        @item-select="addBook"
                        :optionLabel="b => `${b.title} — ${b.author}`"
                        placeholder="Rechercher un ouvrage disponible..."
                        :invalid="submitted && loan.books.length === 0"
                        fluid
                    />
                    <small v-if="submitted && loan.books.length === 0" class="text-red-500">Au moins un ouvrage obligatoire.</small>
                    <div v-if="loan.books.length > 0" class="mt-3 flex flex-col gap-2">
                        <div
                            v-for="book in loan.books"
                            :key="book.id"
                            class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2"
                        >
                            <span class="text-sm"><span class="font-medium">{{ book.title }}</span> <span class="text-gray-500">— {{ book.author }}</span></span>
                            <p-button icon="pi pi-times" text rounded size="small" severity="danger" @click="removeBook(book)" />
                        </div>
                    </div>
                </div>

                <!-- Date de retour -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de retour prévue <span class="text-red-500">*</span></label>
                    <p-datepicker
                        v-model="loan.due_date"
                        dateFormat="dd/mm/yy"
                        :minDate="minDueDate"
                        showIcon
                        :invalid="submitted && !loan.due_date"
                        fluid
                    />
                    <small v-if="submitted && !loan.due_date" class="text-red-500">Date de retour obligatoire.</small>
                </div>

            </div>

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
            loan: { books: [] },
            lazyParams: {},
            filters: null,
            loading_table: true,
            load_button_save: false,
            load_button_delete: false,
            submitted: false,
            loanDialog: false,
            deleteLoanDialog: false,
            totalRecords: 0,
            userSuggestions: [],
            bookSuggestions: [],
            bookSearch: null,
            minDueDate: (() => { const d = new Date(); d.setDate(d.getDate() + 1); return d; })(),
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
            this.loan = { books: [] };
            this.bookSearch = null;
            this.submitted = false;
            this.loanDialog = true;
        },
        hideDialog() {
            this.submitted = false;
            this.loanDialog = false;
        },
        searchUsers(event) {
            this.$axios.get('/api/loans/autocomplete/users', { params: { q: event.query } })
                .then(response => { this.userSuggestions = response.data; });
        },
        searchBooks(event) {
            this.$axios.get('/api/loans/autocomplete/books', { params: { q: event.query } })
                .then(response => { this.bookSuggestions = response.data; });
        },
        addBook(event) {
            const book = event.value;
            if (!this.loan.books.find(b => b.id === book.id)) {
                this.loan.books.push(book);
            }
            this.$nextTick(() => { this.bookSearch = null; });
        },
        removeBook(book) {
            this.loan.books = this.loan.books.filter(b => b.id !== book.id);
        },
        saveLoan() {
            this.submitted = true;

            if (this.loan?.user && this.loan.books.length > 0 && this.loan?.due_date) {
                this.load_button_save = true;
                this.$axios.post('/api/loans', { loan: JSON.stringify(this.loan) })
                    .then(() => {
                        this.loadLazyData();
                        this.$toast.add({ severity: 'success', summary: 'Enregistré', detail: 'Emprunt(s) créé(s) avec succès.', life: 3000 });
                        this.loanDialog = false;
                        this.loan = { books: [] };
                    })
                    .finally(() => {
                        this.load_button_save = false;
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
            this.filters = {
                user_search: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
            };
        },
        formatDate(value) {
            if (!value) return '';
            return new Date(value).toLocaleDateString();
        },
    },
};
</script>
