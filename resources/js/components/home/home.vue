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
                        @click="$router.push('/admin/users')"
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
</template>

<script>
export default {
    name: 'home',
    data() {
        return {
            overdueCount: 0,
            dueTodayLoans: [],
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
        }
    }
}
</script>
