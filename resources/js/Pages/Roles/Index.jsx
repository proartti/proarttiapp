import Can from "@/Components/Can";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";

export default function Index({ roles }) {
    const destroyRole = (role) => {
        if (!window.confirm(`Delete ${role.name}?`)) {
            return;
        }

        router.delete(route("roles.destroy", role.id));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Roles
                </h2>
            }
        >
            <Head title="Roles" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                    <div className="rounded-lg bg-card p-4 shadow-sm">
                        <div className="flex items-center justify-between gap-4">
                            <p className="text-sm text-muted-foreground">
                                Create and maintain role definitions and their
                                permissions.
                            </p>
                            <Can permission="ROLES.CREATE">
                                <Button asChild>
                                    <Link href={route("roles.create")}>
                                        New Role
                                    </Link>
                                </Button>
                            </Can>
                        </div>
                    </div>

                    <div className="overflow-hidden rounded-lg bg-card shadow-sm">
                        <table className="min-w-full divide-y divide-border text-sm">
                            <thead className="bg-muted/50">
                                <tr>
                                    <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                        Role
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                        Permissions
                                    </th>
                                    <th className="px-4 py-3 text-right font-medium text-muted-foreground">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {roles.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={3}
                                            className="px-4 py-8 text-center text-muted-foreground"
                                        >
                                            No roles found.
                                        </td>
                                    </tr>
                                )}
                                {roles.data.map((role) => (
                                    <tr
                                        key={role.id}
                                        className="transition-colors hover:bg-muted/30"
                                    >
                                        <td className="px-4 py-3 font-medium">
                                            {role.name}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge variant="secondary">
                                                {role.permissions_count}{" "}
                                                permissions
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex justify-end gap-2">
                                                <Can permission="ROLES.UPDATE">
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        asChild
                                                    >
                                                        <Link
                                                            href={route(
                                                                "roles.edit",
                                                                role.id,
                                                            )}
                                                        >
                                                            Edit
                                                        </Link>
                                                    </Button>
                                                </Can>
                                                <Can permission="ROLES.DELETE">
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            destroyRole(role)
                                                        }
                                                    >
                                                        Delete
                                                    </Button>
                                                </Can>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
