import './bootstrap';
import Alpine from 'alpinejs';



Alpine.data('themeSelector', () => ({
    theme: 'auto',
    dropdownOpen: false,
    init() {
        this.$watch('theme', (value) => {
            this.updateTheme();
        })
        if (window.localStorage.getItem('themeMain')) {
            this.theme = window.localStorage.getItem('themeMain');
            this.updateTheme();
        } else {
            this.updateTheme();
        }

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', this.updateTheme);
    },

    updateTheme() {
        let value = this.theme;
        let isDark = false;
        window.localStorage.setItem('themeMain', value);
        if (value === 'auto') {
            isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        } else if (value === 'dark') {
            isDark = true;
        }
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }


}))

Alpine.start();
