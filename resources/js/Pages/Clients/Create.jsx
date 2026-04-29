import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Create() {
    const form = useForm({
        name: "",
        email: "",
        phone: "",
        status: "active",
        notes: "",
    });

    const submit = (event) => {
        event.preventDefault();
        form.post(route("clients.store"));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create Client
                </h2>
            }
        >
            <Head title="Create Client" />

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
                        />
                        <input
                            type="email"
                            className="w-full rounded-md border-gray-300"
                            placeholder="Email"
                            value={form.data.email}
                            onChange={(event) =>
                                form.setData("email", event.target.value)
                            }
                        />
                        <input
                            type="text"
                            className="w-full rounded-md border-gray-300"
                            placeholder="Phone"
                            value={form.data.phone}
                            onChange={(event) =>
                                form.setData("phone", event.target.value)
                            }
                        />
                        <select
                            className="w-full rounded-md border-gray-300"
                            value={form.data.status}
                            onChange={(event) =>
                                form.setData("status", event.target.value)
                            }
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
                        />

                        <div className="flex items-center justify-end gap-3">
                            <Link
                                href={route("clients.index")}
                                className="text-sm text-gray-600"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
