// vite.config.ts
import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "file:///G:/www/niucloud-admin-niushop-v6/admin/node_modules/vite/dist/node/index.js";
import vue from "file:///G:/www/niucloud-admin-niushop-v6/admin/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import AutoImport from "file:///G:/www/niucloud-admin-niushop-v6/admin/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///G:/www/niucloud-admin-niushop-v6/admin/node_modules/unplugin-vue-components/dist/vite.mjs";
import { ElementPlusResolver } from "file:///G:/www/niucloud-admin-niushop-v6/admin/node_modules/unplugin-vue-components/dist/resolvers.mjs";
var __vite_injected_original_import_meta_url = "file:///G:/www/niucloud-admin-niushop-v6/admin/vite.config.ts";
var vite_config_default = defineConfig({
  base: "",
  server: {
    host: "0.0.0.0"
  },
  plugins: [
    vue(),
    AutoImport({
      resolvers: [ElementPlusResolver()]
    }),
    Components({
      resolvers: [ElementPlusResolver()]
    })
  ],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", __vite_injected_original_import_meta_url)),
      "assets": fileURLToPath(new URL("./src/assets", __vite_injected_original_import_meta_url)),
      "vue-i18n": "vue-i18n/dist/vue-i18n.cjs.js"
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJHOlxcXFx3d3dcXFxcbml1Y2xvdWQtYWRtaW4tbml1c2hvcC12NlxcXFxhZG1pblwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiRzpcXFxcd3d3XFxcXG5pdWNsb3VkLWFkbWluLW5pdXNob3AtdjZcXFxcYWRtaW5cXFxcdml0ZS5jb25maWcudHNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0c6L3d3dy9uaXVjbG91ZC1hZG1pbi1uaXVzaG9wLXY2L2FkbWluL3ZpdGUuY29uZmlnLnRzXCI7aW1wb3J0IHsgZmlsZVVSTFRvUGF0aCwgVVJMIH0gZnJvbSBcIm5vZGU6dXJsXCJcclxuaW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcclxuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnXHJcbmltcG9ydCBBdXRvSW1wb3J0IGZyb20gJ3VucGx1Z2luLWF1dG8taW1wb3J0L3ZpdGUnXHJcbmltcG9ydCBDb21wb25lbnRzIGZyb20gJ3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3ZpdGUnXHJcbmltcG9ydCB7IEVsZW1lbnRQbHVzUmVzb2x2ZXIgfSBmcm9tICd1bnBsdWdpbi12dWUtY29tcG9uZW50cy9yZXNvbHZlcnMnXHJcblxyXG4vLyBodHRwczovL3ZpdGVqcy5kZXYvY29uZmlnL1xyXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xyXG4gICAgYmFzZTogJycsXHJcbiAgICBzZXJ2ZXI6IHtcclxuICAgICAgICBob3N0OiAnMC4wLjAuMCdcclxuICAgIH0sXHJcbiAgICBwbHVnaW5zOiBbXHJcbiAgICAgICAgdnVlKCksXHJcbiAgICAgICAgQXV0b0ltcG9ydCh7XHJcbiAgICAgICAgICAgIHJlc29sdmVyczogW0VsZW1lbnRQbHVzUmVzb2x2ZXIoKV1cclxuICAgICAgICB9KSxcclxuICAgICAgICBDb21wb25lbnRzKHtcclxuICAgICAgICAgICAgcmVzb2x2ZXJzOiBbRWxlbWVudFBsdXNSZXNvbHZlcigpXVxyXG4gICAgICAgIH0pXHJcbiAgICBdLFxyXG4gICAgcmVzb2x2ZToge1xyXG4gICAgICAgIGFsaWFzOiB7XHJcbiAgICAgICAgICAgICdAJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3NyYycsIGltcG9ydC5tZXRhLnVybCkpLFxyXG4gICAgICAgICAgICAnYXNzZXRzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3NyYy9hc3NldHMnLCBpbXBvcnQubWV0YS51cmwpKSxcclxuICAgICAgICAgICAgJ3Z1ZS1pMThuJzogJ3Z1ZS1pMThuL2Rpc3QvdnVlLWkxOG4uY2pzLmpzJ1xyXG4gICAgICAgIH1cclxuICAgIH1cclxufSlcclxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUE0UyxTQUFTLGVBQWUsV0FBVztBQUMvVSxTQUFTLG9CQUFvQjtBQUM3QixPQUFPLFNBQVM7QUFDaEIsT0FBTyxnQkFBZ0I7QUFDdkIsT0FBTyxnQkFBZ0I7QUFDdkIsU0FBUywyQkFBMkI7QUFMdUosSUFBTSwyQ0FBMkM7QUFRNU8sSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsTUFBTTtBQUFBLEVBQ04sUUFBUTtBQUFBLElBQ0osTUFBTTtBQUFBLEVBQ1Y7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLElBQUk7QUFBQSxJQUNKLFdBQVc7QUFBQSxNQUNQLFdBQVcsQ0FBQyxvQkFBb0IsQ0FBQztBQUFBLElBQ3JDLENBQUM7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNQLFdBQVcsQ0FBQyxvQkFBb0IsQ0FBQztBQUFBLElBQ3JDLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDTCxPQUFPO0FBQUEsTUFDSCxLQUFLLGNBQWMsSUFBSSxJQUFJLFNBQVMsd0NBQWUsQ0FBQztBQUFBLE1BQ3BELFVBQVUsY0FBYyxJQUFJLElBQUksZ0JBQWdCLHdDQUFlLENBQUM7QUFBQSxNQUNoRSxZQUFZO0FBQUEsSUFDaEI7QUFBQSxFQUNKO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
