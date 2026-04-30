<template>
    <div class="max-w-4xl mx-auto p-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Mon espace adhérent</h1>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 mb-6">
            <button
                v-for="tab in tabs" :key="tab.id"
                @click="activeTab = tab.id"
                :class="['px-6 py-3 text-sm font-medium border-b-2 -mb-px transition-colors',
                    activeTab === tab.id
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700']"
            >
                <i :class="tab.icon + ' mr-2'"></i>{{ tab.label }}
                <span v-if="tab.id === 'reservations' && pendingCount > 0"
                    class="ml-2 bg-orange-100 text-orange-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                    {{ pendingCount }}
                </span>
            </button>
        </div>

        <!-- Tab: Réservations -->
        <div v-if="activeTab === 'reservations'">
            <div v-if="loadingRes" class="text-center py-10 text-gray-400"><i class="pi pi-spin pi-spinner text-2xl"></i></div>
            <div v-else-if="reservations.length === 0" class="text-center py-10 text-gray-400">Aucune réservation.</div>
            <div v-else class="flex flex-col gap-3">
                <div v-for="r in reservations" :key="r.id"
                    class="bg-white border rounded-xl p-4 flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 truncate">{{ r.book.title }}</div>
                        <div class="text-sm text-gray-500">{{ r.book.author }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ formatDate(r.created_at) }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="statusClass(r.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                            {{ statusLabel(r.status) }}
                        </span>
                        <p-button
                            v-if="r.status === 'pending' || r.status === 'ready'"
                            icon="pi pi-times" size="small" text rounded severity="danger"
                            v-tooltip="'Annuler'" @click="cancelReservation(r.id)"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Emprunts -->
        <div v-if="activeTab === 'loans'">
            <div v-if="loadingLoans" class="text-center py-10 text-gray-400"><i class="pi pi-spin pi-spinner text-2xl"></i></div>
            <div v-else-if="loans.length === 0" class="text-center py-10 text-gray-400">Aucun emprunt.</div>
            <div v-else class="flex flex-col gap-3">
                <div v-for="loan in loans" :key="loan.id"
                    class="bg-white border rounded-xl p-4 flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 truncate">{{ loan.book.title }}</div>
                        <div class="text-sm text-gray-500">{{ loan.book.author }}</div>
                        <div class="text-xs text-gray-400 mt-1">
                            Emprunté le {{ formatDate(loan.loan_date) }} · Retour prévu {{ formatDate(loan.due_date) }}
                        </div>
                    </div>
                    <span :class="loanStatusClass(loan.status)" class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                        {{ loanStatusLabel(loan.status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tab: Profil -->
        <div v-if="activeTab === 'profile'" class="flex flex-col gap-8">
            <!-- Infos personnelles -->
            <div class="bg-white border rounded-xl p-6">
                <h2 class="text-base font-semibold mb-4 text-gray-700">Informations personnelles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Prénom</label>
                        <p-inputtext v-model="profile.prenom" fluid />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Nom</label>
                        <p-inputtext v-model="profile.nom" fluid />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm text-gray-500 mb-1">Email</label>
                        <p-inputtext v-model="profile.email" type="email" fluid />
                    </div>
                </div>
                <p-button label="Enregistrer" icon="pi pi-check" class="mt-4" size="small" @click="saveProfile" :loading="savingProfile" />
            </div>

            <!-- Changement de mot de passe -->
            <div class="bg-white border rounded-xl p-6">
                <h2 class="text-base font-semibold mb-4 text-gray-700">Changer le mot de passe</h2>
                <div class="flex flex-col gap-4 max-w-sm">
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Mot de passe actuel</label>
                        <p-password v-model="pwd.current" :feedback="false" toggleMask fluid />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Nouveau mot de passe</label>
                        <p-password v-model="pwd.password" :feedback="true" toggleMask fluid />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Confirmer le nouveau mot de passe</label>
                        <p-password v-model="pwd.password_confirmation" :feedback="false" toggleMask fluid />
                    </div>
                    <small v-if="pwdError" class="text-red-500">{{ pwdError }}</small>
                </div>
                <p-button label="Changer le mot de passe" icon="pi pi-lock" class="mt-4" size="small" @click="changePassword" :loading="savingPwd" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            activeTab: 'reservations',
            tabs: [
                { id: 'reservations', label: 'Mes réservations', icon: 'pi pi-shopping-bag' },
                { id: 'loans',        label: 'Mes emprunts',      icon: 'pi pi-bookmark' },
                { id: 'profile',      label: 'Mon profil',        icon: 'pi pi-user' },
            ],
            reservations: [],
            loans: [],
            loadingRes: true,
            loadingLoans: true,
            profile: { nom: '', prenom: '', email: '' },
            savingProfile: false,
            pwd: { current: '', password: '', password_confirmation: '' },
            savingPwd: false,
            pwdError: '',
        };
    },
    computed: {
        pendingCount() {
            return this.reservations.filter(r => r.status === 'pending' || r.status === 'ready').length;
        },
    },
    mounted() {
        this.loadReservations();
        this.loadLoans();
        this.loadProfile();
    },
    methods: {
        loadReservations() {
            this.loadingRes = true;
            this.$axios.get('/api/account/reservations')
                .then(r => { this.reservations = r.data; })
                .finally(() => { this.loadingRes = false; });
        },
        loadLoans() {
            this.loadingLoans = true;
            this.$axios.get('/api/account/loans')
                .then(r => { this.loans = r.data; })
                .finally(() => { this.loadingLoans = false; });
        },
        loadProfile() {
            this.$axios.get('/api/account/profile').then(r => { this.profile = { ...r.data }; });
        },
        cancelReservation(id) {
            this.$axios.delete(`/api/account/reservations/${id}`)
                .then(() => {
                    this.$toast.add({ severity: 'success', summary: 'Annulé', detail: 'Réservation annulée.', life: 3000 });
                    this.loadReservations();
                });
        },
        saveProfile() {
            this.savingProfile = true;
            this.$axios.put('/api/account/profile', this.profile)
                .then(() => { this.$toast.add({ severity: 'success', summary: 'Succès', detail: 'Profil mis à jour.', life: 3000 }); })
                .catch(e => { this.$toast.add({ severity: 'error', summary: 'Erreur', detail: e?.data?.message || 'Erreur de validation.', life: 4000 }); })
                .finally(() => { this.savingProfile = false; });
        },
        changePassword() {
            this.pwdError = '';
            this.savingPwd = true;
            this.$axios.patch('/api/account/password', this.pwd)
                .then(() => {
                    this.$toast.add({ severity: 'success', summary: 'Succès', detail: 'Mot de passe modifié.', life: 3000 });
                    this.pwd = { current: '', password: '', password_confirmation: '' };
                })
                .catch(e => { this.pwdError = e?.data?.message || 'Erreur.'; })
                .finally(() => { this.savingPwd = false; });
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
            }[s] || 'bg-gray-100 text-gray-500';
        },
        loanStatusLabel(s) {
            return { active: 'En cours', overdue: 'En retard', returned: 'Rendu' }[s] || s;
        },
        loanStatusClass(s) {
            return {
                active:   'bg-blue-100 text-blue-700',
                overdue:  'bg-red-100 text-red-600',
                returned: 'bg-green-100 text-green-700',
            }[s] || 'bg-gray-100 text-gray-500';
        },
    },
};
</script>
