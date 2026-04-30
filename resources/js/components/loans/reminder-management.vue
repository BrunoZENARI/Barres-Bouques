<template>
    <p-toolbar class="mb-6 rounded-none">
        <template #start>
            <p-button
                label="Lancer tous les rappels"
                size="small"
                icon="pi pi-send"
                severity="warning"
                @click="sendAll"
                :loading="sendingAll"
            />
        </template>
        <template #end>
            <p-button label="Refresh" icon="pi pi-refresh" size="small" severity="info" @click="loadData()" />
        </template>
    </p-toolbar>

    <div class="bg-white border rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Configuration des rappels</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <p-checkbox v-model="settings.reminder_due_soon_enabled" :binary="true" inputId="due_soon_enabled" />
                    <label for="due_soon_enabled" class="cursor-pointer">Rappels retours imminents activés</label>
                </div>
                <div class="flex items-center gap-3">
                    <p-checkbox v-model="settings.reminder_overdue_enabled" :binary="true" inputId="overdue_enabled" />
                    <label for="overdue_enabled" class="cursor-pointer">Rappels retards activés</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jours avant retour pour déclencher le rappel
                    </label>
                    <p-inputnumber
                        v-model="settings.days_before_due"
                        :min="1"
                        :max="30"
                        showButtons
                        buttonLayout="horizontal"
                        :step="1"
                        style="width: 10rem"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email du bibliothécaire</label>
                    <p-inputtext
                        v-model="settings.librarian_email"
                        placeholder="gisele@bibliotheque.fr"
                        class="w-full md:w-80"
                    />
                </div>
                <div>
                    <p-button
                        label="Enregistrer"
                        icon="pi pi-save"
                        size="small"
                        @click="saveSettings"
                        :loading="savingSettings"
                    />
                </div>
            </div>
        </div>
    </div>

    <p-datatable
        :value="overdueLoans"
        :loading="loadingOverdue"
        class="border mb-6"
        size="small"
        scrollable
        scrollHeight="30vh"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-semibold">Emprunts en retard</h1>
                <span v-if="overdueLoans.length > 0" class="text-sm font-semibold text-red-500">
                    ({{ overdueLoans.length }})
                </span>
            </div>
        </template>
        <template #empty>Aucun emprunt en retard</template>
        <p-column header="Emprunteur" style="min-width: 12rem">
            <template #body="{data}">
                {{ data.user.prenom }} {{ data.user.nom }}
            </template>
        </p-column>
        <p-column field="user.email" header="Email" style="min-width: 14rem" />
        <p-column field="book.title" header="Ouvrage" style="min-width: 16rem" />
        <p-column field="due_date" header="Date prévue" style="min-width: 10rem">
            <template #body="{data}">
                <span class="text-red-500 font-semibold">{{ formatDate(data.due_date) }}</span>
            </template>
        </p-column>
        <p-column header="Retard" style="min-width: 8rem">
            <template #body="{data}">
                <span class="text-red-500 font-semibold">{{ daysLate(data.due_date) }} jour(s)</span>
            </template>
        </p-column>
        <p-column :exportable="false" style="min-width: 10rem">
            <template #body="slotProps">
                <p-button
                    icon="pi pi-envelope"
                    label="Rappel"
                    size="small"
                    severity="danger"
                    :loading="sendingLoanId === slotProps.data.id"
                    @click="sendReminder(slotProps.data)"
                />
            </template>
        </p-column>
    </p-datatable>

    <p-datatable
        :value="dueSoonLoans"
        :loading="loadingDueSoon"
        class="border"
        size="small"
        scrollable
        scrollHeight="30vh"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-semibold">Retours prévus prochainement</h1>
                <span v-if="dueSoonLoans.length > 0" class="text-sm font-semibold text-orange-500">
                    ({{ dueSoonLoans.length }})
                </span>
            </div>
        </template>
        <template #empty>Aucun retour imminent</template>
        <p-column header="Emprunteur" style="min-width: 12rem">
            <template #body="{data}">
                {{ data.user.prenom }} {{ data.user.nom }}
            </template>
        </p-column>
        <p-column field="user.email" header="Email" style="min-width: 14rem" />
        <p-column field="book.title" header="Ouvrage" style="min-width: 16rem" />
        <p-column field="due_date" header="Date de retour" style="min-width: 10rem">
            <template #body="{data}">
                <span class="text-orange-500 font-semibold">{{ formatDate(data.due_date) }}</span>
            </template>
        </p-column>
        <p-column :exportable="false" style="min-width: 10rem">
            <template #body="slotProps">
                <p-button
                    icon="pi pi-envelope"
                    label="Rappel"
                    size="small"
                    severity="warning"
                    :loading="sendingLoanId === slotProps.data.id"
                    @click="sendReminder(slotProps.data)"
                />
            </template>
        </p-column>
    </p-datatable>
</template>

<script>
export default {
    data() {
        return {
            settings: {
                days_before_due: 3,
                reminder_due_soon_enabled: true,
                reminder_overdue_enabled: true,
                librarian_email: '',
            },
            overdueLoans: [],
            dueSoonLoans: [],
            loadingOverdue: true,
            loadingDueSoon: true,
            savingSettings: false,
            sendingAll: false,
            sendingLoanId: null,
        };
    },
    mounted() {
        this.loadData();
    },
    methods: {
        loadData() {
            this.loadSettings();
            this.loadOverdueLoans();
            this.loadDueSoonLoans();
        },
        loadSettings() {
            this.$axios.get('/api/reminders/settings')
                .then(response => {
                    this.settings = response.data;
                });
        },
        loadOverdueLoans() {
            this.loadingOverdue = true;
            this.$axios.get('/api/reminders/overdue')
                .then(response => {
                    this.overdueLoans = response.data;
                })
                .finally(() => {
                    this.loadingOverdue = false;
                });
        },
        loadDueSoonLoans() {
            this.loadingDueSoon = true;
            this.$axios.get('/api/reminders/due-soon')
                .then(response => {
                    this.dueSoonLoans = response.data;
                })
                .finally(() => {
                    this.loadingDueSoon = false;
                });
        },
        saveSettings() {
            this.savingSettings = true;
            this.$axios.put('/api/reminders/settings', this.settings)
                .then(() => {
                    this.$toast.add({ severity: 'success', summary: 'Enregistré', detail: 'Paramètres des rappels mis à jour.', life: 3000 });
                    this.loadDueSoonLoans();
                })
                .finally(() => {
                    this.savingSettings = false;
                });
        },
        sendAll() {
            this.sendingAll = true;
            this.$axios.post('/api/reminders/send-all')
                .then(response => {
                    this.$toast.add({ severity: 'success', summary: 'Rappels envoyés', detail: response.data.message, life: 4000 });
                    this.loadData();
                })
                .finally(() => {
                    this.sendingAll = false;
                });
        },
        sendReminder(loan) {
            this.sendingLoanId = loan.id;
            this.$axios.post(`/api/reminders/${loan.id}/send`)
                .then(() => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Rappel envoyé',
                        detail: `Rappel envoyé à ${loan.user.prenom} ${loan.user.nom}.`,
                        life: 3000,
                    });
                })
                .finally(() => {
                    this.sendingLoanId = null;
                });
        },
        daysLate(dueDate) {
            const diff = Math.floor((new Date() - new Date(dueDate)) / (1000 * 60 * 60 * 24));
            return diff > 0 ? diff : 0;
        },
        formatDate(value) {
            if (!value) return '';
            return new Date(value).toLocaleDateString('fr-FR');
        },
    },
};
</script>
