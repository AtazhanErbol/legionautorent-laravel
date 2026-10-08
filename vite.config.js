import {defineConfig} from 'vite';
export default defineConfig({publicDir:false,base:'/static/build/',build:{outDir:'public/static/build',emptyOutDir:true,manifest:true,target:'es2022',minify:'terser',terserOptions:{compress:{passes:3}},rollupOptions:{input:{main:'frontend/main.js'}}}});
