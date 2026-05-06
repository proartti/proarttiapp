import { usePage } from "@inertiajs/react";

export function useCan(permission) {
    const permissions = usePage().props.auth?.permissions ?? [];

    if (!permission) {
        return true;
    }

    return permissions.includes(permission);
}
