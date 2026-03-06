<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    full_name: string;
    username: string;
    email: string;
    cedula: string;
    phone: string;
    role: string;
    created_at: string;
}

const props = defineProps<{ users: User[] }>();

const search = ref('');

const filtered = () =>
    props.users.filter((u) => {
        const q = search.value.toLowerCase();
        return (
            u.full_name.toLowerCase().includes(q) ||
            u.username.toLowerCase().includes(q) ||
            u.cedula.toLowerCase().includes(q) ||
            u.email.toLowerCase().includes(q)
        );
    });

const roleBadge: Record<string, string> = {
    Administrador: 'bg-red-100 text-red-800',
    Vigilante: 'bg-blue-100 text-blue-800',
    Propietario: 'bg-green-100 text-green-800',
    Residente: 'bg-amber-100 text-amber-800',
};

function destroy(id: number, name: string) {
    if (!confirm(`¿Eliminar al usuario "${name}"?`)) return;
    router.delete(`/admin/users/${id}`, { preserveScroll: true });
}
</script>

<template>
    <AppLayout>
        <Head title="Usuarios" />
        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Usuarios</h1>
                    <p class="text-sm text-muted-foreground">
                        Gestión de cuentas del sistema
                    </p>
                </div>
                <Link href="/admin/users/create">
                    <Button>+ Nuevo usuario</Button>
                </Link>
            </div>

            <Input
                v-model="search"
                placeholder="Buscar por nombre, usuario, cédula o correo..."
                class="w-full sm:max-w-sm"
            />

            <div class="overflow-x-auto rounded-xl border bg-card shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">
                                Nombre
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Usuario
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Cédula
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Correo
                            </th>
                            <th class="px-5 py-3 text-left font-medium">Rol</th>
                            <th class="px-5 py-3 text-left font-medium">
                                Creado
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="u in filtered()"
                            :key="u.id"
                            class="border-t hover:bg-muted/30"
                        >
                            <td class="px-5 py-3 font-medium">
                                {{ u.full_name }}
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ u.username }}
                            </td>
                            <td class="px-5 py-3">{{ u.cedula }}</td>
                            <td class="px-5 py-3">{{ u.email }}</td>
                            <td class="px-5 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-medium',
                                        roleBadge[u.role] ??
                                            'bg-muted text-muted-foreground',
                                    ]"
                                >
                                    {{ u.role }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ u.created_at }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex gap-2">
                                    <Link :href="`/admin/users/${u.id}/edit`">
                                        <Button variant="outline" size="sm"
                                            >Editar</Button
                                        >
                                    </Link>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="destroy(u.id, u.full_name)"
                                    >
                                        Eliminar
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filtered().length === 0">
                            <td
                                colspan="7"
                                class="px-5 py-8 text-center text-muted-foreground"
                            >
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
