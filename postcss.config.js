module.exports = {
    // syntax: 'postcss-scss',
    parser: 'postcss-scss',
    plugins: {
        'postcss-import': {},
        'tailwindcss/nesting': {},
        tailwindcss: {},
        // 'postcss-nested': {},
        autoprefixer: {},
    }
}