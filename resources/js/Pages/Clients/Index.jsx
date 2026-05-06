import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { useCan } from "@/lib/can";

export default function Index({ clients, filters }) {
    const canCreateClients = useCan("CLIENTS.CREATE");
    const canUpdateClients = useCan("CLIENTS.UPDATE");

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
                <div className="mx-auto space-y-4 max-w-7xl sm:px-6 lg:px-8">
                    <div className="p-4 rounded-lg shadow-sm bg-card">
                        <div className="flex items-center justify-between gap-4">
                            <Input
                                type="text"
                                className="max-w-sm"
                                defaultValue={filters.search ?? ""}
                                placeholder="Search by name or email"
                                onChange={onSearch}
                            />
                            {canCreateClients && (
                                <Button asChild>
                                    <Link href={route("clients.create")}>
                                        New Client
                                    </Link>
                                </Button>
                            )}
                        </div>
                    </div>

                    <div className="overflow-hidden rounded-lg shadow-sm bg-card">
                        <table className="min-w-full text-sm divide-y divide-border">
                            <thead className="bg-muted/50">
                                <tr>
                                    <th className="px-4 py-3 font-medium text-left text-muted-foreground">
                                        Name
                                    </th>
                                    <th className="px-4 py-3 font-medium text-left text-muted-foreground">
                                        Email
                                    </th>
                                    <th className="px-4 py-3 font-medium text-left text-muted-foreground">
                                        Status
                                    </th>
                                    <th className="px-4 py-3 font-medium text-right text-muted-foreground">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {clients.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="px-4 py-8 text-center text-muted-foreground"
                                        >
                                            No clients found.
                                        </td>
                                    </tr>
                                )}
                                {clients.data.map((client) => (
                                    <tr
                                        key={client.id}
                                        className="transition-colors hover:bg-muted/30"
                                    >
                                        <td className="px-4 py-3 font-medium">
                                            {client.name}
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {client.email ?? "-"}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge
                                                variant={
                                                    client.status === "active"
                                                        ? "default"
                                                        : "secondary"
                                                }
                                            >
                                                {client.status}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={route(
                                                        canUpdateClients
                                                            ? "clients.edit"
                                                            : "clients.show",
                                                        client.id,
                                                    )}
                                                >
                                                    {canUpdateClients
                                                        ? "Edit"
                                                        : "View"}
                                                </Link>
                                            </Button>
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
