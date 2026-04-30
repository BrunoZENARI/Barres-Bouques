<template>
    <div class="p-4 max-w-4xl mx-auto">

        <!-- Barre d'outils (staff seulement) -->
        <div v-if="$hasPermission('can_create_books')" class="flex justify-end mb-4">
            <p-button label="Ajouter un ouvrage" icon="pi pi-plus" severity="success" size="small" @click="openNew" />
        </div>

        <!-- Barre de recherche -->
        <div class="relative mb-4">
            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
                v-model="search"
                type="text"
                placeholder="Rechercher un livre..."
                class="w-full pl-10 pr-4 py-3 rounded-2xl border border-gray-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-green-300"
            />
        </div>

        <!-- Filtres par genre -->
        <div class="flex items-center gap-2 flex-wrap mb-6">
            <i class="pi pi-filter text-gray-400"></i>
            <button
                v-for="g in genres"
                :key="g"
                @click="selectedGenre = g"
                :class="[
                    'px-4 py-1.5 rounded-full text-sm font-medium transition-colors',
                    selectedGenre === g
                        ? 'bg-[#6CA885] text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                ]"
            >{{ g }}</button>
        </div>

        <!-- Liste des ouvrages -->
        <div v-if="loading" class="text-center text-gray-400 py-10">
            <i class="pi pi-spin pi-spinner text-2xl"></i>
        </div>
        <div v-else-if="filteredBooks.length === 0" class="text-center text-gray-400 py-10">
            Aucun ouvrage trouvé.
        </div>
        <div v-else class="flex flex-col gap-4">
            <div
                v-for="book in filteredBooks"
                :key="book.id"
                class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden"
            >
                <div class="flex gap-4 p-4">
                    <!-- Couverture -->
                    <div class="shrink-0">
                        <img
                            v-if="book.cover_image"
                            :src="`/storage/${book.cover_image}`"
                            :alt="book.title"
                            class="w-24 h-32 object-cover rounded-lg"
                        />
                        <div v-else class="w-24 h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300">
                            <i class="pi pi-book text-3xl"></i>
                        </div>
                    </div>

                    <!-- Infos -->
                    <div class="flex flex-col flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ book.title }}</h3>
                                <p class="text-gray-500 text-sm mt-0.5">{{ book.author }}</p>
                            </div>
                            <!-- Actions staff -->
                            <div v-if="$hasPermission('can_update_books')" class="flex gap-1 shrink-0">
                                <p-button icon="pi pi-pencil" size="small" text rounded @click="editBook(book)" />
                                <p-button v-if="$hasPermission('can_delete_books')" icon="pi pi-trash" size="small" text rounded severity="danger" @click="confirmDeleteBook(book)" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-2">
                            <!-- Badge statut -->
                            <span :class="[
                                'px-3 py-0.5 rounded-full text-xs font-semibold',
                                book.available_stock > 0
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-500'
                            ]">
                                {{ book.available_stock > 0 ? 'En rayon' : 'Emprunté' }}
                            </span>
                            <span v-if="book.location" class="text-gray-400 text-sm">{{ book.location }}</span>
                        </div>

                        <p v-if="book.description" class="text-gray-500 text-sm mt-2 line-clamp-2">{{ book.description }}</p>

                        <!-- Bouton réserver -->
                        <div class="mt-3">
                            <button
                                v-if="book.available_stock > 0"
                                :class="[
                                    'w-full font-semibold py-2.5 rounded-xl transition-colors text-sm',
                                    isInCart(book)
                                        ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                        : 'bg-[#6CA885] hover:bg-[#5a9070] text-white'
                                ]"
                                @click="reserve(book)"
                            >
                                <i :class="isInCart(book) ? 'pi pi-shopping-bag' : 'pi pi-calendar-plus'" class="mr-2"></i>
                                {{ isInCart(book) ? 'Dans le panier' : 'Réserver (Click &amp; Collect)' }}
                            </button>
                            <button v-else disabled class="w-full bg-gray-100 text-gray-400 font-semibold py-2.5 rounded-xl text-sm cursor-not-allowed">
                                Indisponible
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog création / édition -->
        <p-dialog v-model:visible="bookDialog" :style="{ width: '55vw' }" :breakpoints="{ '1199px': '80vw', '575px': '95vw' }" header="Ouvrage" :modal="true">
            <form autocomplete="off" class="flex flex-col gap-1">
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <p-floatlabel class="mb-1">
                            <p-inputtext id="title" v-model.trim="book.title" :invalid="submitted && !book.title" fluid />
                            <label for="title">Titre *</label>
                        </p-floatlabel>
                        <small v-if="submitted && !book.title" class="text-red-500">Titre obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel class="mb-1">
                            <p-inputtext id="author" v-model.trim="book.author" :invalid="submitted && !book.author" fluid />
                            <label for="author">Auteur *</label>
                        </p-floatlabel>
                        <small v-if="submitted && !book.author" class="text-red-500">Auteur obligatoire.</small>
                    </div>
                    <div>
                        <p-floatlabel>
                            <p-inputtext id="isbn" v-model.trim="book.isbn" fluid />
                            <label for="isbn">ISBN</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel>
                            <p-inputtext id="publisher" v-model.trim="book.publisher" fluid />
                            <label for="publisher">Éditeur</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel>
                            <p-inputtext id="genre" v-model.trim="book.genre" fluid />
                            <label for="genre">Genre</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel>
                            <p-inputtext id="location" v-model.trim="book.location" fluid />
                            <label for="location">Emplacement (ex: Étagère A1)</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel>
                            <p-inputnumber id="published_year" v-model="book.published_year" :useGrouping="false" fluid />
                            <label for="published_year">Année de publication</label>
                        </p-floatlabel>
                    </div>
                    <div>
                        <p-floatlabel>
                            <p-inputnumber id="stock" v-model="book.stock" :min="0" fluid />
                            <label for="stock">Stock</label>
                        </p-floatlabel>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <p-floatlabel>
                        <p-textarea id="description" v-model="book.description" rows="3" fluid autoResize />
                        <label for="description">Synopsis</label>
                    </p-floatlabel>
                </div>

                <!-- Upload couverture -->
                <div class="mt-4">
                    <label class="block text-sm text-gray-500 mb-2">Couverture</label>
                    <div class="flex items-center gap-4">
                        <img
                            v-if="book.cover_image && !coverPreview"
                            :src="`/storage/${book.cover_image}`"
                            class="w-16 h-20 object-cover rounded"
                        />
                        <img
                            v-if="coverPreview"
                            :src="coverPreview"
                            class="w-16 h-20 object-cover rounded"
                        />
                        <input type="file" accept="image/jpeg,image/png,image/webp" @change="onCoverSelected" class="text-sm" />
                    </div>
                </div>
            </form>

            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="hideDialog" />
                <p-button label="Sauvegarder" icon="pi pi-check" @click="saveBook" :loading="load_button_save" />
            </template>
        </p-dialog>

        <!-- Dialog suppression -->
        <p-dialog v-model:visible="deleteBookDialog" :style="{ width: '450px' }" header="Confirmation" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle text-3xl text-red-400" />
                <span>Supprimer <b>{{ book.title }}</b> ?</span>
            </div>
            <template #footer>
                <p-button label="Annuler" icon="pi pi-times" text @click="deleteBookDialog = false" />
                <p-button label="Supprimer" icon="pi pi-trash" severity="danger" @click="deleteBook" :loading="load_button_delete" />
            </template>
        </p-dialog>
    </div>
</template>

<script>
import { useCartStore } from '@/store/cart';
export default {
    name: 'book-list',
    data() {
        return {
            books: [],
            book: {},
            search: '',
            selectedGenre: 'Tous',
            loading: true,
            load_button_save: false,
            load_button_delete: false,
            submitted: false,
            bookDialog: false,
            deleteBookDialog: false,
            coverFile: null,
            coverPreview: null,
        };
    },
    computed: {
        genres() {
            const unique = [...new Set(this.books.map(b => b.genre).filter(Boolean))].sort();
            return ['Tous', ...unique];
        },
        filteredBooks() {
            return this.books.filter(book => {
                const matchGenre = this.selectedGenre === 'Tous' || book.genre === this.selectedGenre;
                const q = this.search.toLowerCase();
                const matchSearch = !q
                    || book.title?.toLowerCase().includes(q)
                    || book.author?.toLowerCase().includes(q);
                return matchGenre && matchSearch;
            });
        },
    },
    mounted() {
        this.loadBooks();
    },
    methods: {
        loadBooks() {
            this.loading = true;
            axios.post('/api/books/search', {
                lazyEvent: JSON.stringify({ first: 0, rows: 1000, sortField: 'title', sortOrder: 1, filters: {} }),
            })
                .then(response => {
                    this.books = response.data.payload;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        openNew() {
            this.book = { stock: 1 };
            this.coverFile = null;
            this.coverPreview = null;
            this.submitted = false;
            this.bookDialog = true;
        },
        editBook(book) {
            this.book = { ...book };
            this.coverFile = null;
            this.coverPreview = null;
            this.submitted = false;
            this.bookDialog = true;
        },
        hideDialog() {
            this.submitted = false;
            this.bookDialog = false;
        },
        onCoverSelected(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.coverFile = file;
            this.coverPreview = URL.createObjectURL(file);
        },
        async saveBook() {
            this.submitted = true;
            if (!this.book?.title?.trim() || !this.book?.author?.trim()) return;

            this.load_button_save = true;
            try {
                let savedId = this.book.id;

                if (savedId) {
                    await axios.put(`/api/books/${savedId}`, { book: JSON.stringify(this.book) });
                } else {
                    const { data } = await axios.post('/api/books', { book: JSON.stringify(this.book) });
                    savedId = data.id;
                }

                if (this.coverFile && savedId) {
                    const form = new FormData();
                    form.append('cover', this.coverFile);
                    await axios.post(`/api/books/${savedId}/cover`, form, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    });
                }

                this.$toast.add({ severity: 'success', summary: 'Succès', detail: 'Ouvrage sauvegardé.', life: 3000 });
                this.loadBooks();
                this.bookDialog = false;
                this.book = {};
            } finally {
                this.load_button_save = false;
            }
        },
        confirmDeleteBook(book) {
            this.book = book;
            this.deleteBookDialog = true;
        },
        deleteBook() {
            this.load_button_delete = true;
            axios.delete(`/api/books/${this.book.id}`)
                .then(() => {
                    this.$toast.add({ severity: 'success', summary: 'Succès', detail: 'Ouvrage supprimé.', life: 3000 });
                    this.loadBooks();
                    this.deleteBookDialog = false;
                    this.book = {};
                })
                .finally(() => {
                    this.load_button_delete = false;
                });
        },
        reserve(book) {
            const cart = useCartStore();
            if (cart.hasBook(book.id)) {
                this.$toast.add({ severity: 'warn', summary: 'Déjà dans le panier', detail: `"${book.title}" est déjà dans votre panier.`, life: 3000 });
            } else {
                cart.addBook(book);
                this.$toast.add({ severity: 'success', summary: 'Ajouté au panier', detail: `"${book.title}" ajouté. Ouvrez votre panier pour confirmer.`, life: 4000 });
            }
        },
        isInCart(book) {
            return useCartStore().hasBook(book.id);
        },
    },
};
</script>
