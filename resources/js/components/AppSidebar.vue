<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ClipboardList, FileText, LayoutGrid, LogIn, LogOut, Shield, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { Auth, NavItem } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;
const role = auth?.user?.role ?? '';

const navByRole: Record<string, NavItem[]> = {
    Administrador: [
        { title: 'Dashboard', href: '/admin/dashboard', icon: LayoutGrid },
        { title: 'Usuarios',  href: '/admin/users',     icon: Users },
        { title: 'Ingresos',  href: '/admin/entries',   icon: ClipboardList },
    ],
    Vigilante: [
        { title: 'Dashboard',           href: '/vigilante/dashboard',      icon: LayoutGrid },
        { title: 'Registrar Ingreso',   href: '/vigilante/entries/create', icon: LogIn },
        { title: 'Monitor de Ingresos', href: '/vigilante/entries',        icon: ClipboardList },
        { title: 'Registrar Salida',    href: '/vigilante/exits',          icon: LogOut },
        { title: 'Autorizaciones',      href: '/vigilante/authorizations', icon: Shield },
        { title: 'Reportes',            href: '/vigilante/reports',        icon: FileText },
    ],
    Propietario: [
        { title: 'Dashboard',          href: '/propietario/dashboard',      icon: LayoutGrid },
        { title: 'Mis Autorizaciones', href: '/propietario/authorizations', icon: Shield },
    ],
    Residente: [
        { title: 'Dashboard', href: '/residente/dashboard', icon: LayoutGrid },
    ],
};

const mainNavItems: NavItem[] = navByRole[role] ?? [
    { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
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
