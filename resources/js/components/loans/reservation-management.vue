<template>
    <p-toolbar class="mb-6 rounded-none">
        <template #start>
            <div class="flex gap-2">
                <button v-for="f in statusFilters" :key="f.value" @click="activeFilter = f.value; loadReservations()"
                    :class="['px-4 py-1.5 rounded-full text-sm font-medium transition-colors',
                        activeFilter === f.value ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
                    {{ f.label }}
                    <span v-if="f.value === 'pending' && pendingCount > 0"
                        class="ml-1 bg-orange-500 text-white text-xs rounded-full px-1.5">{{ pendingCount }}</span>
                </button>
            </div>
        </template>
        <template #end>
            <p-button label="Refresh" icon="pi pi-refresh" size="small" severity="info" @click="loadReservations" />
        </template>
    </p-toolbar>

    <p-datatable :value="reservations" :loading="loading" size="small" class="border" scrollable scrollHeight="70vh">
        <template #empty>Aucune réservation.</template>
        <p-column header="Adhérent" style="min-width: 12rem">
            <template #body="{data}">
                <div class="font-medium">{{ data.user.prenom }} {{ data.user.nom }}</div>
                <div class="text-xs text-gray-400">{{ data.user.email }}</div>
            </template>
        </p-column>
        <p-column header="Ouvrage" style="min-width: 14rem">
            <template #body="{data}">
                <div class="font-medium">{{ data.book.title }}</div>
                <div class="text-xs text-gray-400">{{ data.book.author }}</div>
            </template>
        </p-column>
        <p-column header="Statut" style="min-width: 8rem">
            <template #body="{data}">
                <span :class="statusClass(data.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                    {{ statusLabel(data.status) }}
                </span>
            </template>
        </p-column>
        <p-column field="created_at" header="Date" style="min-width: 8rem">
            <template #body="{data}">{{ formatDate(data.created_at) }}</template>
        </p-column>
        <p-column header="Actions" style="min-width: 14rem">
            <template #body="{data}">
                <div class="flex gap-1">
                    <p-button v-if="data.status === 'pending'"
                        label="Prêt" icon="pi pi-check-circle" size="small" severity="info"
                        @click="markReady(data.id)" :loading="actionLoading === data.id + '_ready'" />
                    <p-button v-if="data.status === 'ready'"
                        label="Valider retrait" icon="pi pi-check" size="small" severity="success"
                        @click="openCollectDialog(data)" />
                    <p-button v-if="data.status === 'pending' || data.status === 'ready'"
                        icon="pi pi-times" size="small" text rounded severity="danger"
                        v-tooltip="'Annuler'" @click="reject(data.id)" :loading="actionLoading === data.id + '_reject'" />
                </div>
            </template>
        </p-column>
    </p-datatable>

    <!-- Dialog validation retrait -->
    <p-dialog v-model:visible="collectDialog" header="Valider le retrait" :modal="true" style="width: 400px">
        <div class="flex flex-col gap-4 mt-2">
            <p class="text-sm text-gray-600">
                Créer un emprunt pour <strong>{{ collectTarget?.user?.prenom }} {{ collectTarget?.user?.nom }}</strong>
                — <em>{{ collectTarget?.book?.title }}</em>
            </p>
            <div>
                <label class="block text-sm text-gray-500 mb-1">Date de retour prévue</label>
                <p-datepicker v-model="collectDueDate" dateFormat="dd/mm/yy" showIcon fluid :minDate="tomorrow" />
            </div>
        </div>
        <template #footer>
            <p-button label="Annuler" icon="pi pi-times" text @click="collectDialog = false" />
            <p-button label="Valider" icon="pi pi-check" severity="success" @click="collect" :loading="collecting" />
        </template>
    </p-dialog>
</template>

<script>
export default {
    data() {
        return {
            reservations: [],
            loading: false,
            activeFilter: 'pending',
            actionLoading: null,
            collectDialog: false,
            collectTarget: null,
            collectDueDate: null,
            collecting: false,
            statusFilters: [
                { value: 'pending',   label: 'En attente' },
                { value: 'ready',     label: 'Prêt à retirer' },
                { value: 'all',       label: 'Toutes' },
                { value: 'collected', label: 'Retirées' },
                { value: 'cancelled', label: 'Annulées' },
            ],
            tomorrow: (() => { const d = new Date(); d.setDate(d.getDate() + 1); return d; })(),
        };
    },
    computed: {
        pendingCount() { return this.reservations.filter(r => r.status === 'pending').length; },
    },
    mounted() { this.loadReservations(); },
    methods: {
        loadReservations() {
            this.loading = true;
            this.$axios.get('/api/reservations', { params: { status: this.activeFilter } })
                .then(r => { this.reservations = r.data; })
                .finally(() => { this.loading = false; });
        },
        markReady(id) {
            this.actionLoading = id + '_ready';
            this.$axios.patch(`/api/reservations/${id}/ready`)
                .then(() => {
                    this.$toast.add({ severity: 'success', summary: 'OK', detail: 'Marqué prêt à retirer.', life: 3000 });
                    this.loadReservations();
                })
                .finally(() => { this.actionLoading = null; });
        },
        openCollectDialog(reservation) {
            this.collectTarget = reservation;
            const d = new Date(); d.setDate(d.getDate() + 14);
            this.collectDueDate = d;
            this.collectDialog = true;
        },
        collect() {
            this.collecting = true;
            this.$axios.post(`/api/reservations/${this.collectTarget.id}/collect`, {
                due_date: this.collectDueDate?.toISOString().slice(0, 10),
            })
                .then(() => {
                    this.$toast.add({ severity: 'success', summary: 'Emprunt créé', detail: 'Retrait validé, emprunt enregistré.', life: 4000 });
                    this.collectDialog = false;
                    this.loadReservations();
                })
                .finally(() => { this.collecting = false; });
        },
        reject(id) {
            this.actionLoading = id + '_reject';
            this.$axios.delete(`/api/reservations/${id}`)
                .then(() => {
                    this.$toast.add({ severity: 'warn', summary: 'Annulé', detail: 'Réservation annulée.', life: 3000 });
                    this.loadReservations();
                })
                .finally(() => { this.actionLoading = null; });
        },
        formatDate(v) { return v ? new Date(v).toLocaleDateString('fr-FR') : ''; },
        statusLabel(s) {
            return { pending: 'En attente', ready: 'Prêt à retirer', collected: 'Retiré', cancelled: 'Annulé' }[s] || s;
        },
        statusClass(s) {
            return {
                pending:   'bg-orange-100 text-orange-700',
                ready:     'bg-blue-100 text-blue-700',
                collected: 'bg-green-100 text-green-700',
                cancelled: 'bg-gray-100 text-gray-500',
            }[s];
        },
    },
};
</script>
