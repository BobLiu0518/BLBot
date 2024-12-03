import DefaultTheme from 'vitepress/theme-without-fonts';
import LvBadge from '../../components/LvBadge.vue';
import './index.css';

export default {
    extends: DefaultTheme,
    enhanceApp({ app }) {
        app.component('LvBadge', LvBadge);
    },
};
