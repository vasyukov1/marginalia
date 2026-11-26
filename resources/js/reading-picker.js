export default function readingPicker(books, authors, initialBookId, initialTitle = '', initialAuthor = '') {
    return {
        title: initialTitle,
        author: initialAuthor,
        bookId: '',
        authorId: null,
        activeField: null,
        activeIndex: -1,

        init() {
            const book = books.find((item) => item.id === Number(initialBookId));
            if (book) {
                this.choose('title', book);
            }
        },

        matches(text, query) {
            return text.toLocaleLowerCase().includes(query.trim().toLocaleLowerCase());
        },

        suggestions(field) {
            if (field === 'author') {
                return authors.filter((item) => this.matches(item.search, this.author));
            }

            return books.filter((item) => {
                const matchesAuthor = this.authorId !== null
                    ? item.author_id === this.authorId
                    : this.matches(item.author_search, this.author);

                return matchesAuthor && this.matches(item.search, this.title);
            });
        },

        open(field) {
            this.activeField = field;
            this.activeIndex = -1;
        },

        close() {
            this.activeField = null;
            this.activeIndex = -1;
        },

        edit(field, value) {
            // После изменения текста старый выбор книги больше не действителен.
            if (this.bookId) {
                this.title = '';
                this.author = '';
                this.authorId = null;
            }
            this.bookId = '';
            this[field] = value;
            if (field === 'author') {
                this.authorId = null;
            }
            this.open(field);
        },

        choose(field, item) {
            if (field === 'title') {
                this.bookId = item.id;
                this.title = item.label;
                this.author = item.author;
                this.authorId = item.author_id;
            } else {
                this.bookId = '';
                this.title = '';
                this.author = item.label;
                this.authorId = item.id;
            }
            this.close();
        },

        move(field, step) {
            if (this.activeField !== field) {
                this.open(field);
            }
            const count = this.suggestions(field).length;
            if (count === 0) {
                return;
            }
            this.activeIndex = (this.activeIndex + step + count) % count;
            this.$nextTick(() => {
                document.getElementById(`${field}-option-${this.activeIndex}`)?.scrollIntoView({ block: 'nearest' });
            });
        },

        chooseActive(field) {
            const item = this.suggestions(field)[this.activeIndex];
            if (item) {
                this.choose(field, item);
            }
        },
    };
}
