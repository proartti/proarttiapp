import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select } from "@/components/ui/select";

export default function Edit({ client, readonly = false }) {
    const form = useForm({
        name: client.name ?? "",
        email: client.email ?? "",
        phone: client.phone ?? "",
        status: client.status ?? "active",
        notes: client.notes ?? "",
    });

    const submit = (event) => {
        event.preventDefault();
        if (readonly) return;
        form.put(route("clients.update", client.id));
    };

    const archiveClient = () => {
        if (readonly) return;
        form.delete(route("clients.destroy", client.id));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Edit Client
                </h2>
            }
        >
            <Head title="Edit Client" />

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
                                type="text"
                                placeholder="Client name"
                                value={form.data.name}
                                onChange={(e) =>
                                    form.setData("name", e.target.value)
                                }
                                disabled={readonly}
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
                                placeholder="email@example.com"
                                value={form.data.email}
                                onChange={(e) =>
                                    form.setData("email", e.target.value)
                                }
                                disabled={readonly}
                            />
                            {form.errors.email && (
                                <p className="text-xs text-destructive">
                                    {form.errors.email}
                                </p>
                            )}
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="phone">Phone</Label>
                            <Input
                                id="phone"
                                type="text"
                                placeholder="+1 555 000 0000"
                                value={form.data.phone}
                                onChange={(e) =>
                                    form.setData("phone", e.target.value)
                                }
                                disabled={readonly}
                            />
                            {form.errors.phone && (
                                <p className="text-xs text-destructive">
                                    {form.errors.phone}
                                </p>
                            )}
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="status">Status</Label>
                            <Select
                                id="status"
                                value={form.data.status}
                                onChange={(e) =>
                                    form.setData("status", e.target.value)
                                }
                                disabled={readonly}
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </Select>
                            {form.errors.status && (
                                <p className="text-xs text-destructive">
                                    {form.errors.status}
                                </p>
                            )}
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="notes">Notes</Label>
                            <Textarea
                                id="notes"
                                rows={4}
                                placeholder="Optional notes"
                                value={form.data.notes}
                                onChange={(e) =>
                                    form.setData("notes", e.target.value)
                                }
                                disabled={readonly}
                            />
                            {form.errors.notes && (
                                <p className="text-xs text-destructive">
                                    {form.errors.notes}
                                </p>
                            )}
                        </div>

                        <div className="flex items-center justify-between gap-3">
                            <div>
                                {!readonly && (
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={archiveClient}
                                        className="border-destructive text-destructive hover:bg-destructive/10"
                                    >
                                        Archive
                                    </Button>
                                )}
                            </div>
                            <div className="flex items-center gap-3">
                                <Button variant="ghost" asChild>
                                    <Link href={route("clients.index")}>
                                        Back
                                    </Link>
                                </Button>
                                {!readonly && (
                                    <Button
                                        type="submit"
                                        disabled={form.processing}
                                    >
                                        Save
                                    </Button>
                                )}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
