module.exports = {
    source: './src',
    dest:   '../public',
    groups: {
        js: {
            front: ['js/dashboard.js'],
            back:  ['js/composer.js']
        },
        css: {
            front: ['dashboard.scss', 'default.scss'],
            back:  ['composer.scss']
        }
    }
};