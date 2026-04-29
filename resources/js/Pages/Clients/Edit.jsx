import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

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
        if (readonly) {
            return;
        }

        form.put(route("clients.update", client.id));
    };

    const archiveClient = () => {
        if (readonly) {
            return;
        }

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
                        className="space-y-4 rounded-lg bg-white p-6 shadow-sm"
                    >
                        <input
                            type="text"
                            className="w-full rounded-md border-gray-300"
                            placeholder="Client name"
                            value={form.data.name}
                            onChange={(event) =>
                                form.setData("name", event.target.value)
                            }
                            disabled={readonly}
                        />
                        <input
                            type="email"
                            className="w-full rounded-md border-gray-300"
                            placeholder="Email"
                            value={form.data.email}
                            onChange={(event) =>
                                form.setData("email", event.target.value)
                            }
                            disabled={readonly}
                        />
                        <input
                            type="text"
                            className="w-full rounded-md border-gray-300"
                            placeholder="Phone"
                            value={form.data.phone}
                            onChange={(event) =>
                                form.setData("phone", event.target.value)
                            }
                            disabled={readonly}
                        />
                        <select
                            className="w-full rounded-md border-gray-300"
                            value={form.data.status}
                            onChange={(event) =>
                                form.setData("status", event.target.value)
                            }
                            disabled={readonly}
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <textarea
                            className="w-full rounded-md border-gray-300"
                            rows={4}
                            placeholder="Notes"
                            value={form.data.notes}
                            onChange={(event) =>
                                form.setData("notes", event.target.value)
                            }
                            disabled={readonly}
                        />

                        <div className="flex items-center justify-between gap-3">
                            <div>
                                {!readonly && (
                                    <button
                                        type="button"
                                        onClick={archiveClient}
                                        className="rounded-md border border-red-200 px-4 py-2 text-sm text-red-600"
                                    >
                                        Archive
                                    </button>
                                )}
                            </div>
                            <div className="flex items-center gap-3">
                                <Link
                                    href={route("clients.index")}
                                    className="text-sm text-gray-600"
                                >
                                    Back
                                </Link>
                                {!readonly && (
                                    <button
                                        type="submit"
                                        disabled={form.processing}
                                        className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white"
                                    >
                                        Save
                                    </button>
                                )}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
