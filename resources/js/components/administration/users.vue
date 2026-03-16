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
            :value="users"
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
            currentPageReportTemplate="{first} à {last} sur {totalRecords} utilisateurs"
            size="small"
            class="border"
            scrollable
            scrollHeight="65vh"
        >
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h1 class="my-0 mx-2 text-xl font-semibold">Liste des utilisateurs</h1>
                </div>
            </template>
            <template #empty>Pas de données trouvées</template>
            <p-column field="nom" header="Nom" filterField="nom" dataType="text" sortable style="min-width: 12rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="prenom" header="Prénom" sortable style="min-width: 16rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="username" header="Username" sortable style="min-width: 8rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column field="email" header="Email" sortable style="min-width: 10rem" :showFilterOperator="false" :showAddButton="false">
                <template #filter="{ filterModel }">
                    <p-inputtext v-model="filterModel.value" type="text" />
                </template>
            </p-column>
            <p-column filterMatchMode="in" :showFilterMatchModes="false" field="role_id" header="Role" style="min-width: 10rem" :showFilterOperator="false" :showAddButton="false">
                <template #body="{data}">
                    {{data.role.name}}
                </template>
                <template #filter="{ filterModel }">
                    <p-multiselect v-model="filterModel.value" :options="roles" optionValue="id" optionLabel="name" placeholder="Tous"></p-multiselect>
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
                    <p-button icon="pi pi-pencil" size="small" text rounded class="mr-2" @click="editUser(slotProps.data)" />
                    <p-button icon="pi pi-key" size="small" text rounded severity="warn" class="mr-2 text-xs" @click="editPasswordUser(slotProps.data)" />
                    <p-button icon="pi pi-trash" size="small" text rounded severity="danger" class="text-xs" @click="confirmDeleteUser(slotProps.data)" />
                </template>
            </p-column>
        </p-datatable>

        <p-dialog v-model:visible="userDialog" :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" header="Utilisateur" :modal="true">
            <form autocomplete="off">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p-floatlabel class="mb-4 mt-5">
                            <p-inputtext id="nom" v-model.trim="user.nom" required="true" autofocus :invalid="submitted && !user.nom" fluid />
                            <label for="nom">Nom</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.nom" class="text-red-500">Nom obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4 mt-5">
                            <p-inputtext id="prenom" v-model.trim="user.prenom" required="true" :invalid="submitted && !user.prenom" fluid />
                            <label for="prenom">Prenom</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.prenom" class="text-red-500">Prénom obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputtext id="username" v-model.trim="user.username" required="true" :invalid="submitted && !user.username" fluid />
                            <label for="username">Username</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.username" class="text-red-500">Username obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-inputtext id="email" v-model.trim="user.email" required="true" :invalid="submitted && !user.email" fluid />
                            <label for="email">Email</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.email" class="text-red-500">Email obligatoire.</small>
                    </div>
                    <div v-if="!user.id">
                        <p-floatlabel class="mb-4">
                            <p-password id="password" v-model="user.password" promptLabel="Mot de passe" weakLabel="Trop simple" mediumLabel="Moyen" strongLabel="Fort" :invalid="submitted && !user.password" fluid :inputProps="{ autocomplete: 'off' }" />
                            <label for="password">Mot de passe</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.password" class="text-red-500">Mot de passe obligatoire.</small>
                    </div>
                    <div v-if="!user.id">
                        <p-floatlabel class="mb-4">
                            <p-password id="confirm_password" v-model="user.cpassword" :feedback="false" :invalid="submitted && user.password != user.cpassword" fluid :inputProps="{ autocomplete: 'off' }" />
                            <label for="confirm_password">Confirmation de mot de passe</label>
                        </p-floatlabel>
                        <small v-if="submitted && user.password != user.cpassword" class="text-red-500">Identique au mot de passe.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-4">
                            <p-select id="role" v-model="user.role" :options="roles" optionLabel="name" placeholder="Sélectionnez un role" :invalid="submitted && !user.role" fluid></p-select>
                            <label for="role">Role</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.role" class="text-red-500">Role obligatoire.</small>
                    </div>
                </div>
            </form>

            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="hideDialog" />
                <p-button label="Sauvegarder" icon="pi pi-check" @click="saveUser" :loading="load_button_save" />
            </template>
        </p-dialog>

        <p-dialog v-model:visible="deleteUserDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="user">êtes-vous sûr(e) de vouloir supprimer <b>{{ user.username }}</b> ?</span>
            </div>
            <template #footer>
                <p-button label="Non" icon="pi pi-times" text @click="deleteUserDialog = false" />
                <p-button label="Oui" icon="pi pi-check" @click="deleteUser" :loading="load_button_delete" />
            </template>
        </p-dialog>

        <p-dialog v-model:visible="userPasswordDialog" :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" header="Changement mot de passe" :modal="true">
            <form autocomplete="off">
                <div class="grid grid-cols-2 gap-4">
                    <div v-if="user.id">
                        <p-floatlabel class="mb-4">
                            <p-password id="password" v-model="user.password" promptLabel="Mot de passe" weakLabel="Trop simple" mediumLabel="Moyen" strongLabel="Fort" :invalid="submitted && !user.password" fluid :inputProps="{ autocomplete: 'off' }" />
                            <label for="password">Mot de passe</label>
                        </p-floatlabel>
                        <small v-if="submitted && !user.password" class="text-red-500">Mot de passe obligatoire.</small>
                    </div>
                    <div v-if="user.id">
                        <p-floatlabel class="mb-4">
                            <p-password id="confirm_password" v-model="user.cpassword" :feedback="false" :invalid="submitted && user.password != user.cpassword" fluid :inputProps="{ autocomplete: 'off' }" />
                            <label for="confirm_password">Confirmation de mot de passe</label>
                        </p-floatlabel>
                        <small v-if="submitted && user.password != user.cpassword" class="text-red-500">Identique au mot de passe.</small>
                    </div>
                </div>
            </form>

            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="hideDialog" />
                <p-button label="Sauvegarder" icon="pi pi-check" @click="savePasswordUser" :loading="load_button_save" />
            </template>
        </p-dialog>
</template>

<script>
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

export default {
    data() {
        return {
            users: [],
            roles: [],
            user: {},
            lazyParams: {},
            lazyParamsEXPORT: {},
            filters: null,
            loading_table: true,
            load_button_export: false,
            load_button_save: false,
            load_button_delete: false,
            submitted: false,
            userDialog: false,
            userPasswordDialog: false,
            deleteUserDialog: false,
            totalRecords: 0,
        };
    },
    mounted() {
        this.initTable();
    },
    created() {
        this.initRoles();
        this.initFilters();
    },
    methods: {
        initRoles() {
            this.$axios.get('/api/admin/users/roles')
                .then(response => {
                    this.roles = response.data.roles;
                });
        },
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
            this.$axios.post('/api/admin/users/search', { lazyEvent: JSON.stringify(this.lazyParams) })
                .then(response => {
                    this.users = response.data.payload;
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
            this.user = {};
            this.submitted = false;
            this.userDialog = true;
        },
        hideDialog() {
            this.submitted = false;
            this.userDialog = false;
            this.userPasswordDialog = false;
        },
        saveUser() {
            this.submitted = true;

            if (this.user?.username?.trim()
                && this.user?.nom?.trim()
                && this.user?.email?.trim()
                && this.user?.prenom?.trim()
                && this.user?.role) {
                this.load_button_save = true;

                if (this.user.id) {
                    this.$axios.put(`/api/admin/users/${this.user.id}`, { user: JSON.stringify(this.user) })
                        .then(() => {
                            this.loadLazyData();
                            this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Utilisateur mis à jour.', life: 3000 });
                        })
                        .finally(() => {
                            this.load_button_save = false;
                            this.userDialog = false;
                            this.user = {};
                        });
                } else {
                    if (this.user?.password && this.user?.cpassword && this.user?.password === this.user?.cpassword) {
                        this.$axios.post('/api/admin/users/', { user: JSON.stringify(this.user) })
                            .then(() => {
                                this.loadLazyData();
                                this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Utilisateur créé.', life: 3000 });
                            })
                            .finally(() => {
                                this.load_button_save = false;
                                this.userDialog = false;
                                this.user = {};
                            });
                    } else {
                        this.load_button_save = false;
                    }
                }
            }
        },
        savePasswordUser() {
            this.submitted = true;
            this.load_button_save = true;

            if (this.user.id && this.user?.password && this.user?.cpassword && this.user?.password === this.user?.cpassword) {
                this.$axios.patch(`/api/admin/users/${this.user.id}/password`, { user: JSON.stringify(this.user) })
                    .then(() => {
                        this.loadLazyData();
                        this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Mot de passe mis à jour.', life: 3000 });
                    })
                    .finally(() => {
                        this.load_button_save = false;
                        this.userPasswordDialog = false;
                        this.user = {};
                    });
            } else {
                this.load_button_save = false;
            }
        },
        editUser(user) {
            this.user = { ...user };
            this.userDialog = true;
        },
        editPasswordUser(user) {
            this.user = { ...user };
            this.userPasswordDialog = true;
        },
        confirmDeleteUser(user) {
            this.user = user;
            this.deleteUserDialog = true;
        },
        deleteUser() {
            this.load_button_delete = true;
            this.$axios.delete(`/api/admin/users/${this.user.id}`)
                .then(() => {
                    this.loadLazyData();
                    this.$toast.add({ severity: 'success', summary: 'Successful', detail: 'Utilisateur supprimé.', life: 3000 });
                })
                .finally(() => {
                    this.load_button_delete = false;
                });
            this.user = {};
            this.deleteUserDialog = false;
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
            this.$axios.post('/api/admin/users/search', { lazyEvent: JSON.stringify(this.lazyParamsEXPORT) })
                .then(response => {
                    this.$refs.dt.exportCSV(null, response.data.payload);
                })
                .finally(() => {
                    this.load_button_export = false;
                });
        },
        initFilters() {
            this.filters = {
                nom: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                prenom: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                username: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                email: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
                role_id: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.IN }] },
                updated_at: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
            };
        },
        formatDate(value) {
            return (new Date(value).toLocaleDateString()) + ' ' + (new Date(value).toLocaleTimeString());
        },
    },
};
</script>
