<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-4">

        <!-- Colonne gauche -->
        <div class="flex flex-col gap-8">

            <!-- Alertes urgentes -->
            <section>
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="pi pi-exclamation-circle text-red-500 text-2xl"></i>
                    Alertes urgentes
                </h2>
                <div class="flex flex-col gap-3">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg px-5 py-4">
                        <p class="font-semibold text-red-800">{{ overdueCount }} livre{{ overdueCount > 1 ? 's' : '' }} en retard</p>
                        <p class="text-red-600 text-sm mt-1">Rappels à envoyer</p>
                    </div>
                </div>
            </section>

            <!-- Actions rapides -->
            <section>
                <h2 class="text-xl font-bold mb-4">Actions rapides</h2>
                <div class="flex flex-col gap-3">
                    <button
                        @click="$router.push('/loans')"
                        class="flex items-center gap-4 bg-[#6CA885] hover:bg-[#5a9070] text-white font-semibold text-lg px-6 py-5 rounded-2xl transition-colors"
                    >
                        <i class="pi pi-book text-2xl"></i>
                        Nouvel emprunt
                    </button>
                    <button
                        @click="$router.push('/loans')"
                        class="flex items-center gap-4 bg-[#4a8a72] hover:bg-[#3d7560] text-white font-semibold text-lg px-6 py-5 rounded-2xl transition-colors"
                    >
                        <i class="pi pi-replay text-2xl"></i>
                        Retour de livre
                    </button>
                    <button
                        @click="openInscription"
                        class="flex items-center gap-4 bg-[#6CA885] hover:bg-[#5a9070] text-white font-semibold text-lg px-6 py-5 rounded-2xl transition-colors"
                    >
                        <i class="pi pi-user-plus text-2xl"></i>
                        Inscrire un usager
                    </button>
                </div>
            </section>
        </div>

        <!-- Colonne droite : emprunts à rendre aujourd'hui -->
        <div>
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <i class="pi pi-box text-xl"></i>
                Retours attendus aujourd'hui
            </h2>
            <div v-if="dueTodayLoans.length === 0" class="text-gray-400 text-sm">Aucun retour prévu aujourd'hui.</div>
            <div class="flex flex-col gap-3">
                <div
                    v-for="loan in dueTodayLoans"
                    :key="loan.id"
                    class="bg-gray-50 border border-gray-200 rounded-xl px-5 py-4"
                >
                    <p class="font-bold text-gray-900">{{ loan.user?.prenom }} {{ loan.user?.nom }}</p>
                    <p class="text-gray-500 text-sm mt-1">{{ loan.book?.title }}</p>
                    <p class="text-gray-400 text-sm mt-1">À retirer aujourd'hui</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Dialog inscription usager -->
    <p-dialog v-model:visible="inscriptionDialog" :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" header="Inscrire un usager" :modal="true">
        <form autocomplete="off">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p-floatlabel class="mb-4 mt-5">
                        <p-inputtext id="nom" v-model.trim="newUser.nom" :invalid="submitted && !newUser.nom" fluid />
                        <label for="nom">Nom</label>
                    </p-floatlabel>
                    <small v-if="submitted && !newUser.nom" class="text-red-500">Nom obligatoire.</small>
                </div>
                <div>
                    <p-floatlabel class="mb-4 mt-5">
                        <p-inputtext id="prenom" v-model.trim="newUser.prenom" :invalid="submitted && !newUser.prenom" fluid />
                        <label for="prenom">Prénom</label>
                    </p-floatlabel>
                    <small v-if="submitted && !newUser.prenom" class="text-red-500">Prénom obligatoire.</small>
                </div>
                <div class="col-span-2">
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="email" v-model.trim="newUser.email" :invalid="submitted && !newUser.email" fluid />
                        <label for="email">Email</label>
                    </p-floatlabel>
                    <small v-if="submitted && !newUser.email" class="text-red-500">Email obligatoire.</small>
                </div>
                <div>
                    <p-floatlabel class="mb-4">
                        <p-datepicker id="date_naissance" v-model="newUser.date_naissance" dateFormat="dd/mm/yy" :maxDate="new Date()" showIcon fluid />
                        <label for="date_naissance">Date de naissance</label>
                    </p-floatlabel>
                </div>
                <div>
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="telephone" v-model.trim="newUser.telephone" fluid />
                        <label for="telephone">Téléphone</label>
                    </p-floatlabel>
                </div>
                <div>
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="adresse_numero" v-model.trim="newUser.adresse_numero" fluid />
                        <label for="adresse_numero">N°</label>
                    </p-floatlabel>
                </div>
                <div>
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="adresse_rue" v-model.trim="newUser.adresse_rue" fluid />
                        <label for="adresse_rue">Rue</label>
                    </p-floatlabel>
                </div>
                <div class="col-span-2">
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="adresse_complement1" v-model.trim="newUser.adresse_complement1" fluid />
                        <label for="adresse_complement1">Complément 1</label>
                    </p-floatlabel>
                </div>
                <div class="col-span-2">
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="adresse_complement2" v-model.trim="newUser.adresse_complement2" fluid />
                        <label for="adresse_complement2">Complément 2</label>
                    </p-floatlabel>
                </div>
                <div>
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="adresse_code_postal" v-model.trim="newUser.adresse_code_postal" fluid />
                        <label for="adresse_code_postal">Code postal</label>
                    </p-floatlabel>
                </div>
                <div>
                    <p-floatlabel class="mb-4">
                        <p-inputtext id="adresse_ville" v-model.trim="newUser.adresse_ville" fluid />
                        <label for="adresse_ville">Ville</label>
                    </p-floatlabel>
                </div>
            </div>
        </form>

        <template #footer>
            <p-button label="Annuler" icon="pi pi-times" text @click="hideInscription" />
            <p-button label="Inscrire" icon="pi pi-check" @click="saveInscription" :loading="loadSave" />
        </template>
    </p-dialog>
</template>

<script>
export default {
    name: 'home',
    data() {
        return {
            overdueCount: 0,
            dueTodayLoans: [],
            inscriptionDialog: false,
            submitted: false,
            loadSave: false,
            newUser: {},
        }
    },
    async mounted() {
        await this.fetchStats()
    },
    methods: {
        async fetchStats() {
            try {
                const { data } = await axios.get('/api/loans/stats')
                this.overdueCount = data.overdue_count
                this.dueTodayLoans = data.due_today_loans
            } catch (e) {
                // silencieux
            }
        },
        openInscription() {
            this.newUser = {}
            this.submitted = false
            this.inscriptionDialog = true
        },
        hideInscription() {
            this.submitted = false
            this.inscriptionDialog = false
        },
        saveInscription() {
            this.submitted = true

            if (
                this.newUser?.nom?.trim() &&
                this.newUser?.prenom?.trim() &&
                this.newUser?.email?.trim()
            ) {
                this.loadSave = true
                this.$axios.post('/api/admin/users', { user: JSON.stringify(this.newUser) })
                    .then(() => {
                        this.$toast.add({ severity: 'success', summary: 'Inscription réussie', detail: `${this.newUser.prenom} ${this.newUser.nom} a été inscrit(e).`, life: 3000 })
                        this.hideInscription()
                    })
                    .finally(() => {
                        this.loadSave = false
                    })
            }
        },
    }
}
</script>
