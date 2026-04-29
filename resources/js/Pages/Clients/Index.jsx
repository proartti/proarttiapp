import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";

export default function Index({ clients, filters }) {
    const onSearch = (event) => {
        router.get(
            route("clients.index"),
            { search: event.target.value },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Clients
                </h2>
            }
        >
            <Head title="Clients" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                    <div className="rounded-lg bg-white p-4 shadow-sm">
                        <div className="flex items-center justify-between gap-4">
                            <input
                                type="text"
                                className="w-full max-w-sm rounded-md border-gray-300 shadow-sm"
                                defaultValue={filters.search ?? ""}
                                placeholder="Search by name or email"
                                onChange={onSearch}
                            />
                            <Link
                                href={route("clients.create")}
                                className="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white"
                            >
                                New Client
                            </Link>
                        </div>
                    </div>

                    <div className="overflow-hidden rounded-lg bg-white shadow-sm">
                        <table className="min-w-full divide-y divide-gray-200 text-sm">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-3 text-left font-medium text-gray-600">
                                        Name
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-600">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-600">
                                        Status
                                    </th>
                                    <th className="px-4 py-3 text-right font-medium text-gray-600">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 bg-white">
                                {clients.data.map((client) => (
                                    <tr key={client.id}>
                                        <td className="px-4 py-3 text-gray-900">
                                            {client.name}
                                        </td>
                                        <td className="px-4 py-3 text-gray-600">
                                            {client.email ?? "-"}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                                {client.status}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route(
                                                    "clients.edit",
                                                    client.id,
                                                )}
                                                className="text-sm font-medium text-gray-900"
                                            >
                                                Edit
                                            </Link>
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
