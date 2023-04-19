const defaultTheme = require("tailwindcss/defaultTheme");
const colors = require("tailwindcss/colors");

module.exports = {
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter var", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                danger: colors.rose,
                primary: colors.cyan,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ["active"],
        },
    },
    content: [
        "./app/**/*.php",
        "./resources/**/*.html",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.ts",
        "./resources/**/*.tsx",
        "./resources/**/*.php",
        "./resources/**/*.vue",
        "./resources/**/*.twig",
        "./resources/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    darkMode: "class",
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
