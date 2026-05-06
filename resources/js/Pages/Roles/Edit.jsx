import Checkbox from "@/Components/Checkbox";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

function groupPermissions(permissions) {
    return permissions.reduce((groups, permission) => {
        const [group] = permission.name.split(".");

        groups[group] ??= [];
        groups[group].push(permission);

        return groups;
    }, {});
}

export default function Edit({ role, permissions }) {
    const form = useForm({
        name: role.name,
        permissions: role.permissions,
    });

    const groupedPermissions = groupPermissions(permissions);

    const togglePermission = (permissionId) => {
        form.setData(
            "permissions",
            form.data.permissions.includes(permissionId)
                ? form.data.permissions.filter(
                      (value) => value !== permissionId,
                  )
                : [...form.data.permissions, permissionId],
        );
    };

    const submit = (event) => {
        event.preventDefault();
        form.put(route("roles.update", role.id));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Edit Role
                </h2>
            }
        >
            <Head title="Edit Role" />

            <div className="py-12">
                <div className="mx-auto max-w-4xl sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="space-y-5 rounded-lg bg-card p-6 shadow-sm"
                    >
                        <div className="space-y-1.5">
                            <Label htmlFor="name">Role Name</Label>
                            <Input
                                id="name"
                                value={form.data.name}
                                onChange={(event) =>
                                    form.setData("name", event.target.value)
                                }
                            />
                            {form.errors.name && (
                                <p className="text-xs text-destructive">
                                    {form.errors.name}
                                </p>
                            )}
                        </div>

                        <div className="space-y-4">
                            <Label>Permissions</Label>
                            {Object.entries(groupedPermissions).map(
                                ([groupName, groupPermissions]) => (
                                    <div
                                        key={groupName}
                                        className="space-y-3 rounded-md border border-border p-4"
                                    >
                                        <h3 className="text-sm font-semibold text-foreground">
                                            {groupName}
                                        </h3>
                                        <div className="grid gap-3 sm:grid-cols-2">
                                            {groupPermissions.map(
                                                (permission) => (
                                                    <label
                                                        key={permission.id}
                                                        className="flex items-center gap-3 rounded-md border border-border px-3 py-2"
                                                    >
                                                        <Checkbox
                                                            checked={form.data.permissions.includes(
                                                                permission.id,
                                                            )}
                                                            onChange={() =>
                                                                togglePermission(
                                                                    permission.id,
                                                                )
                                                            }
                                                        />
                                                        <span className="text-sm font-medium">
                                                            {permission.name}
                                                        </span>
                                                    </label>
                                                ),
                                            )}
                                        </div>
                                    </div>
                                ),
                            )}
                            {form.errors.permissions && (
                                <p className="text-xs text-destructive">
                                    {form.errors.permissions}
                                </p>
                            )}
                        </div>

                        <div className="flex items-center justify-end gap-3">
                            <Button variant="ghost" asChild>
                                <Link href={route("roles.index")}>Cancel</Link>
                            </Button>
                            <Button type="submit" disabled={form.processing}>
                                Save
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
