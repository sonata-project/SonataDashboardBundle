module.exports = {
    source: './src',
    dest:   '../public',
    groups: {
        js: {
            front: ['js/dashboard.js', 'js/ajax-block.js'],
            back:  ['js/composer.js', 'js/ajax-block.js']
        },
        css: {
            front: ['dashboard.scss', 'default.scss'],
            back:  ['composer.scss']
        }
    }
};