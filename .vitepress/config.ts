import { defineConfig } from 'vitepress';

export default defineConfig({
    title: 'BLBot',
    description: '实用性和娱乐性兼顾的 QQ 机器人',
    head: [['link', { rel: 'icon', href: '/logo.svg' }]],
    lang: 'zh-CN',
    themeConfig: {
        logo: '/logo.svg',
        nav: [
            { text: '首页', link: '/' },
            { text: '用户手册', link: '/manual/', activeMatch: '/manual/' },
            { text: '部署', link: '/deploy/', activeMatch: '/deploy/' },
            { text: '关于', link: '/about', activeMatch: '/about' },
        ],
        sidebar: {
            '/manual/': [
                { text: '开始使用', link: '/manual/' },
                {
                    text: '基础模块',
                    items: [
                        { text: '金币系统', link: '/manual/credit' },
                        { text: '经验系统', link: '/manual/exp' },
                        { text: '别名系统', link: '/manual/alias' },
                        { text: '功能管理', link: '/manual/config' },
                    ],
                },
                {
                    text: '实用功能',
                    items: [
                        { text: '待办事项', link: '/manual/ddl' },
                        { text: '垃圾分类', link: '/manual/trash' },
                        { text: '课程表', link: '/manual/schedule' },
                        { text: '洗手间位置', link: '/manual/toilet' },
                        { text: '国铁车次', link: '/manual/cr' },
                        { text: '金山铁路车次', link: '/manual/jsr' },
                        { text: '拼音查询', link: '/manual/pinyin' },
                    ],
                },
                {
                    text: '娱乐功能',
                    items: [
                        { text: '赛马', link: '/manual/rh' },
                        { text: '今日人品', link: '/manual/jrrp' },
                        { text: '打劫群友', link: '/manual/attack' },
                        { text: '随机数', link: '/manual/roll' },
                        { text: '随机选择', link: '/manual/choose' },
                        { text: '明日方舟', link: '/manual/arknights' },
                    ],
                },
                {
                    text: '个性化',
                    items: [
                        { text: '昵称', link: '/manual/nickname' },
                        { text: '个性签名', link: '/manual/motto' },
                    ],
                },
                { text: '指令一览', link: '/manual/overview' },
            ],
            '/deploy/': [{ text: '页面施工中', link: '/deploy/' }],
        },
        socialLinks: [
            {
                icon: {
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 288 288"><path fill="currentColor" d="M256.64252,180.38794c-2.56592-8.25726-6.06891-17.88702-9.61597-27.13641l-12.94812-32.31781c.01105-.37762.1709-6.73865.1709-10.02112C234.24933,55.64758,208.16028.11249,143.9986.10974c-64.15887.00275-90.24792,55.53784-90.24792,110.80286,0,3.28247.15985,9.64349.1709,10.02112l-12.94812,32.31781c-3.54706,9.24939-7.05005,18.87915-9.61597,27.13641-12.237,39.37891-8.271,55.67566-5.25311,56.04218,6.47681.78271,25.21265-29.64441,25.21265-29.64441,0,17.61871,9.07056,40.61023,28.69781,57.2132-7.34259,2.26489-16.33875,5.74438-22.11902,10.01593-5.198,3.83649-4.55029,7.74738-3.61322,9.3266,4.11206,6.93982,70.54474,4.43176,89.7243,2.271,19.17957,2.16077,85.61224,4.66882,89.7243-2.271.93707-1.57922,1.58472-5.49011-3.61047-9.3266-5.78607-4.27386-14.78888-7.75482-22.13129-10.01953,19.62427-16.60272,28.69354-39.59216,28.69354-57.20959,0,0,18.73584,30.42712,25.21265,29.64441,3.01788-.36652,6.98389-16.66327-5.25311-56.04218Z" /></svg>',
                },
                link: 'https://jq.qq.com/?_wv=1027&k=5FBe63r',
            },
            { icon: 'github', link: 'https://github.com/BobLiu0518/BLBot' },
        ],
        footer: {
            message:
                '<a href="https://beian.miit.gov.cn/" style="text-decoration: none;">沪ICP备20015150号-1</a>',
        },
        lastUpdated: {
            text: '更新于 ',
            formatOptions: {
                dateStyle: 'short',
                timeStyle: 'medium',
            },
        },
        search: { provider: 'local' },
        docFooter: {
            prev: '上一篇',
            next: '下一篇',
        },
        outline: {
            label: '页面导航',
        },
        darkModeSwitchLabel: '主题',
        lightModeSwitchTitle: '切换到浅色模式',
        darkModeSwitchTitle: '切换到深色模式',
        sidebarMenuLabel: '目录',
        returnToTopLabel: '回到顶部',
        externalLinkIcon: true,
    },
    markdown: {
        container: {
            tipLabel: '提示',
            warningLabel: '注意',
            dangerLabel: '警告',
            infoLabel: '信息',
            detailsLabel: '详细信息',
        },
    },
    srcDir: './src',
    cleanUrls: true,
});
