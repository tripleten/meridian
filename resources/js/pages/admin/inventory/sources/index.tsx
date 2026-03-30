import { Head, Link, usePage } from '@inertiajs/react';
import { PlusCircle } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { PageProps } from '@inertiajs/core';

interface Source {
    id: string;
    name: string;
    code: string;
    type: string;
    city: string | null;
    country_code: string | null;
    is_active: boolean;
    is_default: boolean;
    priority: number;
}

interface Option { value: string; label: string; }
interface Props extends PageProps { sources: Source[]; typeOptions: Option[]; }

export default function InventorySourcesIndex() {
    const { sources } = usePage<Props>().props;

    return (
        <>
            <Head title="Inventory Sources" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Inventory Sources</h1>
                        <p className="text-muted-foreground text-sm">Warehouses, stores, and dropship locations.</p>
                    </div>
                    <Button asChild>
                        <Link href="/admin/inventory/sources/create">
                            <PlusCircle className="mr-2 h-4 w-4" />New Source
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{sources.length} source{sources.length !== 1 ? 's' : ''}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Name</th>
                                    <th className="px-4 py-3 font-medium">Code</th>
                                    <th className="px-4 py-3 font-medium">Type</th>
                                    <th className="px-4 py-3 font-medium">Location</th>
                                    <th className="px-4 py-3 font-medium">Priority</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {sources.length === 0 && (
                                    <tr><td colSpan={7} className="text-muted-foreground px-4 py-8 text-center">No sources yet.</td></tr>
                                )}
                                {sources.map((s) => (
                                    <tr key={s.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">
                                            {s.name}
                                            {s.is_default && <span className="text-muted-foreground ml-2 text-xs">(default)</span>}
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">{s.code}</td>
                                        <td className="px-4 py-3 capitalize text-xs">{s.type}</td>
                                        <td className="text-muted-foreground px-4 py-3 text-xs">
                                            {[s.city, s.country_code].filter(Boolean).join(', ') || '—'}
                                        </td>
                                        <td className="px-4 py-3 text-xs">{s.priority}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={s.is_active ? 'default' : 'secondary'}>
                                                {s.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/inventory/sources/${s.id}/edit`}>Edit</Link>
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
