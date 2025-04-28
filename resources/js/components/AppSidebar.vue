<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3'; // Usamos Link de Inertia.js
import { Users, Box, Settings, Tickets } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

// Menú principal
const mainNavItems: NavItem[] = [
  {
    title: 'Usuarios',
    href: route('admin.users'), 
    icon: Users,
  },
  {
    title: 'Artículos',
    href: route('admin.items'), 
    icon: Box,
  },
  {
    title: 'Planes',
    href: '/planes',
    icon: Tickets,
  },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset" class="bg-slate-800 text-white border-r border-slate-700">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <div class="ml-2 flex items-center gap-3 lg:ml-0">
                <img
                  src="/img/logofinal.png"
                  alt="Alquila SV Logo"
                  class="h-10 w-auto object-cover" 
                />
                <span class="text-xl font-bold tracking-wide text-white">ALQUILA SV</span>
              </div>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <slot />
</template>
