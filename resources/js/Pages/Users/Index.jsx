import Can from "@/Components/Can";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";

export default function Index({ users }) {
    const destroyUser = (user) => {
        if (!window.confirm(`Delete ${user.name}?`)) {
            return;
        }

        router.delete(route("users.destroy", user.id));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Users
                </h2>
            }
        >
            <Head title="Users" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                    <div className="rounded-lg bg-card p-4 shadow-sm">
                        <div className="flex items-center justify-between gap-4">
                            <p className="text-sm text-muted-foreground">
                                Manage user access and role assignments.
                            </p>
                            <Can permission="USERS.CREATE">
                                <Button asChild>
                                    <Link href={route("users.create")}>
                                        New User
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
                                        Name
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                        Roles
                                    </th>
                                    <th className="px-4 py-3 text-right font-medium text-muted-foreground">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {users.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="px-4 py-8 text-center text-muted-foreground"
                                        >
                                            No users found.
                                        </td>
                                    </tr>
                                )}
                                {users.data.map((user) => (
                                    <tr
                                        key={user.id}
                                        className="transition-colors hover:bg-muted/30"
                                    >
                                        <td className="px-4 py-3 font-medium">
                                            {user.name}
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {user.email}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex flex-wrap gap-2">
                                                {user.roles.map((role) => (
                                                    <Badge
                                                        key={role.id}
                                                        variant="secondary"
                                                    >
                                                        {role.name}
                                                    </Badge>
                                                ))}
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex justify-end gap-2">
                                                <Can permission="USERS.UPDATE">
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        asChild
                                                    >
                                                        <Link
                                                            href={route(
                                                                "users.edit",
                                                                user.id,
                                                            )}
                                                        >
                                                            Edit
                                                        </Link>
                                                    </Button>
                                                </Can>
                                                <Can permission="USERS.DELETE">
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            destroyUser(user)
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
