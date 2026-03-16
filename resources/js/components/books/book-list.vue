<template>
        <p-toolbar class="mb-6 rounded-none">
            <template #start>
                <p-button label="Nouveau" size="small" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" :loading="load_button_export" />
            </template>

            <template #end>
                <p-button label="Export" size="small" icon="pi pi-upload" class="mr-2" severity="help" @click="exportCSV($event)" />
                <p-button label="Refresh" icon="pi pi-refresh" size="small" severity="info" @click="initTable()" />
            </template>
        </p-toolbar>

        <p-datatable
            ref="dt"
            :value="books"
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
            sortField="title"
            :sortOrder="1"
            csvSeparator=";"
            :totalRecords="totalRecords"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="{first} à {last} sur {totalRecords} ouvrages"
            size="small"
            class="border"
            scrollable
            scrollHeight="65vh"
        >
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h1 class="my-0 mx-2 text-xl font-semibold">Catalogue des ouvrages</h1>
                </div>
            </template>
            <template #empty>Aucun ouvrage trouvé</template>
            <p-column field="title" header="Titre" filterField="title" sortable style="min-width: 16rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="author" header="Auteur" sortable style="min-width: 12rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="isbn" header="ISBN" sortable style="min-width: 10rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="genre" header="Genre" sortable style="min-width: 8rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="stock" header="Stock" sortable style="min-width: 6rem"></p-column>
            <p-column :exportable="false" style="min-width: 10rem">
                <template #body="slotProps">
                    <p-button icon="pi pi-pencil" size="small" text rounded class="mr-2" @click="editBook(slotProps.data)" />
                    <p-button icon="pi pi-trash" size="small" text rounded severity="danger" @click="confirmDeleteBook(slotProps.data)" />
                </template>
            </p-column>
        </p-datatable>

        <p-dialog v-model:visible="bookDialog" :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" header="Ouvrage" :modal="true">
            <form autocomplete="off">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p-floatlabel class="mb-4 mt-5">
                            <p-inputtext id="title" v-model.trim="book.title" required="true" autofocus :invalid="submitted && !book.title" fluid />
                            <label for="title">Titre</label>
                        </p-floatlabel>
                        <small v-if="submitted && !book.title" class="text-red-500">Titre obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4 mt-5">
                            <p-inputtext id="author" v-model.trim="book.author" required="true" :invalid="submitted && !book.author" fluid />
                            <label for="author">Auteur</label>
                        </p-floatlabel>
                        <small v-if="submitted && !book.author" class="text-red-500">Auteur obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputtext id="isbn" v-model.trim="book.isbn" fluid />
                            <label for="isbn">ISBN</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputtext id="publisher" v-model.trim="book.publisher" fluid />
                            <label for="publisher">Éditeur</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputtext id="genre" v-model.trim="book.genre" fluid />
                            <label for="genre">Genre</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputnumber id="stock" v-model="book.stock" :min="0" fluid />
                            <label for="stock">Stock</label>
                        </p-floatlabel>
                    </div>
                </div>
            </form>

            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="hideDialog" />
                <p-button label="Sauvegarder" icon="pi pi-check" @click="saveBook" :loading="load_button_save" />
            </template>
        </p-dialog>

        <p-dialog v-model:visible="deleteBookDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="book">êtes-vous sûr(e) de vouloir supprimer <b>{{ book.title }}</b> ?</span>
            </div>
            <template #footer>
                <p-button label="Non" icon="pi pi-times" text @click="deleteBookDialog = false" />
                <p-button label="Oui" icon="pi pi-check" @click="deleteBook" :loading="load_button_delete" />
            </template>
        </p-dialog>
</template>

<script>
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

export default {
    data() {
        return {
            books: [],
            book: {},
            lazyParams: {},
            lazyParamsEXPORT: {},
            filters: null,
            loading_table: true,
            load_button_export: false,
            load_button_save: false,
            load_button_delete: false,
            submitted: false,
            bookDialog: false,
            deleteBookDialog: false,
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
            this.$refs.dt.value.sortField = 'title';
            this.$refs.dt.value.sortOrder = 1;
            this.lazyParams = {
                first: 0,
                rows: this.$refs.dt.rows,
                sortField: this.$refs.dt.sortField,
                sortOrder: this.$refs.dt.sortOrder,
                filters: this.filters,
            };
            this.loadLazyData();
        },
        loadLazyData() {
            this.loading_table = true;
            this.$axios.post('/api/books/search', { lazyEvent: JSON.stringify(this.lazyParams) })
                .then(response => {
                    this.books = response.data.payload;
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
            this.book = { stock: 1 };
            this.submitted = false;
            this.bookDialog = true;
        },
        hideDialog() {
            this.submitted = false;
            this.bookDialog = false;
        },
        saveBook() {
            this.submitted = true;

            if (this.book?.title?.trim() && this.book?.author?.trim()) {
                this.load_button_save = true;

                if (this.book.id) {
                    this.$axios.put(`/api/books/${this.book.id}`, { book: JSON.stringify(this.book) })
                        .then(() => {
                            this.loadLazyData();
                            this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Ouvrage mis à jour.', life: 3000 });
                        })
                        .finally(() => {
                            this.load_button_save = false;
                            this.bookDialog = false;
                            this.book = {};
                        });
                } else {
                    this.$axios.post('/api/books/', { book: JSON.stringify(this.book) })
                        .then(() => {
                            this.loadLazyData();
                            this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Ouvrage créé.', life: 3000 });
                        })
                        .finally(() => {
                            this.load_button_save = false;
                            this.bookDialog = false;
                            this.book = {};
                        });
                }
            }
        },
        editBook(book) {
            this.book = { ...book };
            this.bookDialog = true;
        },
        confirmDeleteBook(book) {
            this.book = book;
            this.deleteBookDialog = true;
        },
        deleteBook() {
            this.load_button_delete = true;
            this.$axios.delete(`/api/books/${this.book.id}`)
                .then(() => {
                    this.loadLazyData();
                    this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Ouvrage supprimé.', life: 3000 });
                })
                .finally(() => {
                    this.load_button_delete = false;
                });
            this.book = {};
            this.deleteBookDialog = false;
        },
        exportCSV() {
            this.load_button_export = true;
            this.lazyParamsEXPORT = {
                first: null,
                rows: null,
                sortField: null,
                sortOrder: null,
                filters: this.lazyParams.filters,
            };
            this.$axios.post('/api/books/search', { lazyEvent: JSON.stringify(this.lazyParamsEXPORT) })
                .then(response => {
                    this.$refs.dt.exportCSV(null, response.data.payload);
                })
                .finally(() => {
                    this.load_button_export = false;
                });
        },
        initFilters() {
            this.filters = {
                title: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                author: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                isbn: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                genre: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
            };
        },
        formatDate(value) {
            return (new Date(value).toLocaleDateString()) + ' ' + (new Date(value).toLocaleTimeString());
        },
    },
};
</script>
