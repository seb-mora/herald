/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      backgroundImage: {
        'wallpaper': "url('/public/img/imgFond.jpg')",
      }
    },
  },
  plugins: [require("daisyui")],
}

