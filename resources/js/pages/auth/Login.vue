<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/login';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Iniciar sesión"
        description="Ingresa tu usuario y contraseña para acceder al sistema"
    >
        <Head title="Iniciar sesión" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="username">Usuario</Label>
                    <Input
                        id="username"
                        type="text"
                        name="username"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="username"
                        placeholder="Ingresa tu usuario"
                    />
                    <InputError :message="errors.username" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Contraseña</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Recordarme</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    :tabindex="4"
                    :disabled="processing"
                >
                    <Spinner v-if="processing" />
                    Ingresar
                </Button>
            </div>
        </Form>
    </AuthBase>
</template>
