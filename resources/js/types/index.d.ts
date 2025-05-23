import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
}

export interface User {
    id: number;
    name: string;
    email: string;
    direccion?: string;
    telefono?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    avatar?: string;
}

export interface Plan {
    id: number;
    nombre: string;
    descripcion?: string;
    duracion: number;
    max_publicaciones: number;
    precio: number;
    destacar: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
