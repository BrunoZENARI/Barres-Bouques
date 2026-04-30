<template>
    <p-toolbar class="mb-6 rounded-none">
        <template #start>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-600">Période :</span>
                <p-select
                    v-model="period"
                    :options="periodOptions"
                    optionLabel="label"
                    optionValue="value"
                    size="small"
                    @change="loadCharts"
                />
            </div>
        </template>
        <template #end>
            <p-button label="Export CSV" icon="pi pi-download" size="small" severity="help" @click="exportCsv" :loading="exporting" />
            <p-button label="Refresh" icon="pi pi-refresh" size="small" severity="info" class="ml-2" @click="loadAll" />
        </template>
    </p-toolbar>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white border rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ kpis.total ?? '—' }}</div>
            <div class="text-sm text-gray-500 mt-1">Total emprunts</div>
        </div>
        <div class="bg-white border rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ kpis.active ?? '—' }}</div>
            <div class="text-sm text-gray-500 mt-1">En cours</div>
        </div>
        <div class="bg-white border rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ kpis.overdue ?? '—' }}</div>
            <div class="text-sm text-gray-500 mt-1">En retard</div>
        </div>
        <div class="bg-white border rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-gray-700">{{ kpis.returned ?? '—' }}</div>
            <div class="text-sm text-gray-500 mt-1">Rendus</div>
        </div>
        <div class="bg-white border rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-emerald-600">{{ kpis.return_rate ?? '—' }}%</div>
            <div class="text-sm text-gray-500 mt-1">Taux de retour</div>
        </div>
    </div>

    <!-- Graphiques ligne 1 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Emprunts dans le temps -->
        <div class="md:col-span-2 bg-white border rounded-lg p-4">
            <h2 class="text-base font-semibold mb-4">Emprunts dans le temps</h2>
            <div class="relative h-44">
                <canvas ref="chartLoans"></canvas>
            </div>
        </div>

        <!-- Répartition statuts -->
        <div class="bg-white border rounded-lg p-4">
            <h2 class="text-base font-semibold mb-4">Répartition des statuts</h2>
            <div class="relative h-44">
                <canvas ref="chartStatus"></canvas>
            </div>
        </div>
    </div>

    <!-- Graphiques ligne 2 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Top livres -->
        <div class="bg-white border rounded-lg p-4">
            <h2 class="text-base font-semibold mb-4">Top 10 livres les plus empruntés</h2>
            <div class="relative h-64">
                <canvas ref="chartTopBooks"></canvas>
            </div>
        </div>

        <!-- Livres peu empruntés -->
        <div class="bg-white border rounded-lg p-4">
            <h2 class="text-base font-semibold mb-4">
                Livres peu ou jamais empruntés
                <span v-if="lowActivityBooks.length > 0" class="ml-2 text-sm font-normal text-orange-500">({{ lowActivityBooks.length }})</span>
            </h2>
            <p-datatable
                :value="lowActivityBooks"
                :loading="loadingLow"
                size="small"
                scrollable
                scrollHeight="280px"
                class="border text-sm"
            >
                <template #empty>Tous les livres ont été empruntés au moins 2 fois.</template>
                <p-column field="title" header="Titre" style="min-width: 12rem" />
                <p-column field="author" header="Auteur" style="min-width: 10rem" />
                <p-column field="genre" header="Genre" style="min-width: 8rem" />
                <p-column field="loan_count" header="Emprunts" style="min-width: 5rem; text-align: center">
                    <template #body="{data}">
                        <span :class="data.loan_count === 0 ? 'text-red-500 font-semibold' : 'text-orange-500 font-semibold'">
                            {{ data.loan_count }}
                        </span>
                    </template>
                </p-column>
            </p-datatable>
        </div>
    </div>
</template>

<script>
import {
    Chart,
    BarElement, BarController,
    LineElement, LineController, PointElement,
    ArcElement, DoughnutController,
    CategoryScale, LinearScale,
    Tooltip, Legend,
} from 'chart.js';

Chart.register(
    BarElement, BarController,
    LineElement, LineController, PointElement,
    ArcElement, DoughnutController,
    CategoryScale, LinearScale,
    Tooltip, Legend
);

const PALETTE = {
    blue:   'rgba(59, 130, 246, 0.8)',
    green:  'rgba(34, 197, 94, 0.8)',
    red:    'rgba(239, 68, 68, 0.8)',
    orange: 'rgba(249, 115, 22, 0.8)',
    purple: 'rgba(168, 85, 247, 0.8)',
    teal:   'rgba(20, 184, 166, 0.8)',
};

export default {
    data() {
        return {
            period: 'month',
            periodOptions: [
                { label: 'Par semaine (12 sem.)',  value: 'week' },
                { label: 'Par mois (12 mois)',     value: 'month' },
                { label: 'Par trimestre (8 trim.)', value: 'quarter' },
                { label: 'Par année (5 ans)',       value: 'year' },
            ],
            kpis: {},
            lowActivityBooks: [],
            loadingLow: true,
            exporting: false,
            chartLoans: null,
            chartStatus: null,
            chartTopBooks: null,
        };
    },
    mounted() {
        this.loadAll();
    },
    beforeUnmount() {
        [this.chartLoans, this.chartStatus, this.chartTopBooks].forEach(c => c?.destroy());
    },
    methods: {
        loadAll() {
            this.loadCharts();
            this.loadLowActivityBooks();
        },
        loadKpis() {
            this.$axios.get('/api/stats/kpis', { params: { period: this.period } }).then(r => { this.kpis = r.data; });
        },
        loadCharts() {
            this.loadKpis();
            this.loadLoansChart();
            this.loadStatusChart();
            this.loadTopBooksChart();
        },
        loadLoansChart() {
            this.$axios.get('/api/stats/loans-by-period', { params: { period: this.period } })
                .then(r => {
                    this.renderChart('chartLoans', 'bar', {
                        labels: r.data.labels,
                        datasets: [{
                            label: 'Emprunts',
                            data: r.data.data,
                            backgroundColor: PALETTE.blue,
                            borderRadius: 4,
                        }],
                    }, {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    });
                });
        },
        loadStatusChart() {
            this.$axios.get('/api/stats/loan-status', { params: { period: this.period } })
                .then(r => {
                    this.renderChart('chartStatus', 'doughnut', {
                        labels: r.data.labels,
                        datasets: [{
                            data: r.data.data,
                            backgroundColor: [PALETTE.green, PALETTE.blue, PALETTE.red],
                        }],
                    }, {
                        plugins: { legend: { position: 'bottom' } },
                    });
                });
        },
        loadTopBooksChart() {
            this.$axios.get('/api/stats/top-books', { params: { period: this.period } })
                .then(r => {
                    this.renderChart('chartTopBooks', 'bar', {
                        labels: r.data.labels,
                        datasets: [{
                            label: 'Emprunts',
                            data: r.data.data,
                            backgroundColor: PALETTE.teal,
                            borderRadius: 4,
                        }],
                    }, {
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    });
                });
        },
        loadLowActivityBooks() {
            this.loadingLow = true;
            this.$axios.get('/api/stats/low-activity-books')
                .then(r => { this.lowActivityBooks = r.data; })
                .finally(() => { this.loadingLow = false; });
        },
        renderChart(ref, type, data, options = {}) {
            if (this[ref]) {
                this[ref].destroy();
                this[ref] = null;
            }
            this.$nextTick(() => {
                const canvas = this.$refs[ref];
                if (!canvas) return;
                this[ref] = new Chart(canvas, {
                    type,
                    data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        ...options,
                    },
                });
            });
        },
        exportCsv() {
            this.exporting = true;
            this.$axios.get('/api/stats/export-csv', { responseType: 'blob' })
                .then(r => {
                    const url  = URL.createObjectURL(new Blob([r.data]));
                    const link = document.createElement('a');
                    link.href     = url;
                    link.download = `statistiques_emprunts_${new Date().toISOString().slice(0, 10)}.csv`;
                    link.click();
                    URL.revokeObjectURL(url);
                })
                .finally(() => { this.exporting = false; });
        },
    },
};
</script>
