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
            :value="permissions"
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
            sortField="updated_at"
            :sortOrder="-1"
            csvSeparator=";"
            :totalRecords="totalRecords"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="{first} à {last} sur {totalRecords} permissions"
            size="small"
            class="border"
            scrollable
            scrollHeight="65vh"
        >
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h1 class="my-0 mx-2 text-xl font-semibold">Liste des permissions</h1>
                </div>
            </template>
            <template #empty>Pas de données trouvées</template>
            <p-column field="slug" header="Tag" filterField="slug" dataType="text" sortable style="min-width: 12rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="name" header="Libelle" sortable style="min-width: 16rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="category" header="Categorie" sortable style="min-width: 8rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="updated_at" header="Dernière modification" dataType="date" sortable style="min-width: 8rem" :showFilterOperator="false" :showAddButton="false">
                <template #body="{data}">
                    {{formatDate(data.updated_at)}}
                </template>
                <template #filter="{filterModel}">
                    <p-datepicker v-model="filterModel.value" dateFormat="dd/mm/yy" placeholder="dd/mm/yyyy" />
                </template>
            </p-column>
            <p-column :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <p-button icon="pi pi-pencil" size="small" text rounded class="mr-2" @click="editPermission(slotProps.data)" />
                    <p-button icon="pi pi-trash" size="small" text rounded severity="danger" class="text-xs" @click="confirmDeletePermission(slotProps.data)" />
                </template>
            </p-column>
        </p-datatable>

        <p-dialog v-model:visible="permissionDialog" :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" header="Permission" :modal="true">
            <form autocomplete="off">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p-floatlabel class="mb-4 mt-5">
                            <p-inputtext id="slug" v-model.trim="permission.slug" required="true" autofocus :invalid="submitted && !permission.slug" fluid />
                            <label for="slug">Tag</label>
                        </p-floatlabel>
                        <small v-if="submitted && !permission.slug" class="text-red-500">Tag obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4 mt-5">
                            <p-inputtext id="name" v-model.trim="permission.name" required="true" :invalid="submitted && !permission.name" fluid />
                            <label for="name">Libelle</label>
                        </p-floatlabel>
                        <small v-if="submitted && !permission.name" class="text-red-500">Libelle obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputtext id="category" v-model.trim="permission.category" required="true" :invalid="submitted && !permission.category" fluid />
                            <label for="category">Categorie</label>
                        </p-floatlabel>
                        <small v-if="submitted && !permission.category" class="text-red-500">Categorie obligatoire.</small>
                    </div>
                </div>
            </form>

            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="hideDialog" />
                <p-button label="Sauvegarder" icon="pi pi-check" @click="savePermission" :loading="load_button_save" />
            </template>
        </p-dialog>

        <p-dialog v-model:visible="deletePermissionDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="permission">êtes-vous sûr(e) de vouloir supprimer <b>{{ permission.name }}</b> ?</span>
            </div>
            <template #footer>
                <p-button label="Non" icon="pi pi-times" text @click="deletePermissionDialog = false" />
                <p-button label="Oui" icon="pi pi-check" @click="deletePermission" :loading="load_button_delete" />
            </template>
        </p-dialog>
</template>

<script>
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

export default {
    data() {
        return {
            permissions: [],
            permission: {},
            lazyParams: {},
            lazyParamsEXPORT: {},
            filters: null,
            loading_table: true,
            load_button_export: false,
            load_button_save: false,
            load_button_delete: false,
            submitted: false,
            permissionDialog: false,
            deletePermissionDialog: false,
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
            this.$refs.dt.value.sortField = 'updated_at';
            this.$refs.dt.value.sortOrder = -1;
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
            this.loading = true;
            this.$axios.post('/api/admin/permissions/search', { lazyEvent: JSON.stringify(this.lazyParams) })
                .then(response => {
                    this.permissions = response.data.payload;
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
            this.permission = {};
            this.submitted = false;
            this.permissionDialog = true;
        },
        hideDialog() {
            this.submitted = false;
            this.permissionDialog = false;
        },
        savePermission() {
            this.submitted = true;

            if (this.permission?.slug?.trim() && this.permission?.name?.trim() && this.permission?.category?.trim()) {
                this.load_button_save = true;

                if (this.permission.id) {
                    this.$axios.put(`/api/admin/permissions/${this.permission.id}`, { permission: JSON.stringify(this.permission) })
                        .then(() => {
                            this.loadLazyData();
                            this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Permission mise à jour.', life: 3000 });
                        })
                        .finally(() => {
                            this.load_button_save = false;
                            this.permissionDialog = false;
                            this.permission = {};
                        });
                } else {
                    this.$axios.post('/api/admin/permissions/', { permission: JSON.stringify(this.permission) })
                        .then(() => {
                            this.loadLazyData();
                            this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Permission créée.', life: 3000 });
                        })
                        .finally(() => {
                            this.load_button_save = false;
                            this.permissionDialog = false;
                            this.permission = {};
                        });
                }
            }
        },
        editPermission(permission) {
            this.permission = { ...permission };
            this.permissionDialog = true;
        },
        confirmDeletePermission(permission) {
            this.permission = permission;
            this.deletePermissionDialog = true;
        },
        deletePermission() {
            this.load_button_delete = true;
            this.$axios.delete(`/api/admin/permissions/${this.permission.id}`)
                .then(() => {
                    this.loadLazyData();
                    this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Permission supprimée.', life: 3000 });
                })
                .finally(() => {
                    this.load_button_delete = false;
                });
            this.permission = {};
            this.deletePermissionDialog = false;
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
            this.$axios.post('/api/admin/permissions/search', { lazyEvent: JSON.stringify(this.lazyParamsEXPORT) })
                .then(response => {
                    this.$refs.dt.exportCSV(null, response.data.payload);
                })
                .finally(() => {
                    this.load_button_export = false;
                });
        },
        initFilters() {
            this.filters = {
                slug: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                category: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                updated_at: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
            };
        },
        formatDate(value) {
            return (new Date(value).toLocaleDateString()) + ' ' + (new Date(value).toLocaleTimeString());
        },
    },
};
</script>
