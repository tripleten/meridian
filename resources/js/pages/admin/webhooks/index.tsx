import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, List, Plus, Trash2 } from 'lucide-react';

interface Endpoint {
    id: string;
    url: string;
    is_active: boolean;
    subscribed_events: string[];
    delivery_count: number;
    created_at: string;
}

interface Props {
    endpoints: Endpoint[];
}

export default function WebhooksIndex({ endpoints }: Props) {
    function destroy(id: string) {
        if (!confirm('Delete this webhook endpoint?')) return;
        router.delete(`/admin/webhooks/${id}`);
    }

    return (
        <AdminLayout>
            <Head title="Webhooks" />

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Webhook Endpoints</h1>
                <Button asChild>
                    <Link href="/admin/webhooks/create">
                        <Plus className="h-4 w-4 mr-1" /> New Endpoint
                    </Link>
                </Button>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>URL</TableHead>
                            <TableHead>Events</TableHead>
                            <TableHead className="text-right">Deliveries</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead className="w-28" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {endpoints.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={5} className="text-center py-10 text-muted-foreground">
                                    No endpoints yet.
                                </TableCell>
                            </TableRow>
                        ) : endpoints.map(e => (
                            <TableRow key={e.id}>
                                <TableCell className="font-mono text-sm break-all max-w-xs">{e.url}</TableCell>
                                <TableCell>
                                    <div className="flex flex-wrap gap-1">
                                        {e.subscribed_events.slice(0, 3).map(ev => (
                                            <Badge key={ev} variant="outline" className="text-xs">{ev}</Badge>
                                        ))}
                                        {e.subscribed_events.length > 3 && (
                                            <Badge variant="outline" className="text-xs">+{e.subscribed_events.length - 3}</Badge>
                                        )}
                                    </div>
                                </TableCell>
                                <TableCell className="text-right">{e.delivery_count}</TableCell>
                                <TableCell>
                                    <Badge variant={e.is_active ? 'default' : 'secondary'}>
                                        {e.is_active ? 'Active' : 'Inactive'}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div className="flex items-center gap-1">
                                        <Button variant="ghost" size="icon" asChild title="Deliveries">
                                            <Link href={`/admin/webhooks/${e.id}/deliveries`}>
                                                <List className="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="icon" asChild title="Edit">
                                            <Link href={`/admin/webhooks/${e.id}/edit`}>
                                                <Edit className="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="icon" onClick={() => destroy(e.id)} title="Delete">
                                            <Trash2 className="h-4 w-4 text-destructive" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
