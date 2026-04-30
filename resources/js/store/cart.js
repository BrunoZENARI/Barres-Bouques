import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
    state: () => ({ items: [] }),
    getters: {
        count: (state) => state.items.length,
        hasBook: (state) => (bookId) => state.items.some(i => i.id === bookId),
    },
    actions: {
        addBook(book) {
            if (!this.hasBook(book.id)) this.items.push(book);
        },
        removeBook(bookId) {
            this.items = this.items.filter(i => i.id !== bookId);
        },
        clear() { this.items = []; },
    },
    persist: { key: 'library_cart', storage: localStorage },
});
