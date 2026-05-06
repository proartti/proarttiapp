import Checkbox from "@/Components/Checkbox";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Create({ roles }) {
    const form = useForm({
        name: "",
        email: "",
        roles: [],
        send_invite: false,
    });

    const toggleRole = (roleId) => {
        form.setData(
            "roles",
            form.data.roles.includes(roleId)
                ? form.data.roles.filter((value) => value !== roleId)
                : [...form.data.roles, roleId],
        );
    };

    const submit = (event) => {
        event.preventDefault();
        form.post(route("users.store"));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create User
                </h2>
            }
        >
            <Head title="Create User" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="space-y-5 rounded-lg bg-card p-6 shadow-sm"
                    >
                        <div className="space-y-1.5">
                            <Label htmlFor="name">Name</Label>
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

                        <div className="space-y-1.5">
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                value={form.data.email}
                                onChange={(event) =>
                                    form.setData("email", event.target.value)
                                }
                            />
                            {form.errors.email && (
                                <p className="text-xs text-destructive">
                                    {form.errors.email}
                                </p>
                            )}
                        </div>

                        <div className="space-y-3">
                            <Label>Roles</Label>
                            <div className="grid gap-3 sm:grid-cols-2">
                                {roles.map((role) => (
                                    <label
                                        key={role.id}
                                        className="flex items-center gap-3 rounded-md border border-border px-3 py-2"
                                    >
                                        <Checkbox
                                            checked={form.data.roles.includes(
                                                role.id,
                                            )}
                                            onChange={() => toggleRole(role.id)}
                                        />
                                        <span className="text-sm font-medium">
                                            {role.name}
                                        </span>
                                    </label>
                                ))}
                            </div>
                            {form.errors.roles && (
                                <p className="text-xs text-destructive">
                                    {form.errors.roles}
                                </p>
                            )}
                        </div>

                        <label className="flex items-start gap-3 rounded-md border border-border px-3 py-3">
                            <Checkbox
                                checked={form.data.send_invite}
                                onChange={(event) =>
                                    form.setData(
                                        "send_invite",
                                        event.target.checked,
                                    )
                                }
                            />
                            <span>
                                <span className="block text-sm font-medium text-foreground">
                                    Send invitation email with magic link
                                </span>
                                <span className="block text-sm text-muted-foreground">
                                    Disabled by default. Enable it to email a
                                    sign-in link immediately after the user is
                                    created.
                                </span>
                            </span>
                        </label>

                        <div className="flex items-center justify-end gap-3">
                            <Button variant="ghost" asChild>
                                <Link href={route("users.index")}>Cancel</Link>
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
