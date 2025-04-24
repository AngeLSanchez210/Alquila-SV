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
    title: 'Configuración',
    href: '/admin/settings',
    icon: Settings,
  },
  {
    title: 'Planes',
    href: '/planes',
    icon: Tickets,
  },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
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
