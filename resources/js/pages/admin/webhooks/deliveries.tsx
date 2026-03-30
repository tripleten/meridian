import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft } from 'lucide-react';

interface Delivery { id: string; event_type: string; state: string; response_status: number | null; attempt_count: number; created_at: string; }
interface Endpoint { id: string; url: string; }
interface PaginatedDeliveries { data: Delivery[]; }
interface Props { endpoint: Endpoint; deliveries: PaginatedDeliveries; }

const STATE_BADGE: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = { delivered: 'default', pending: 'secondary', failed: 'destructive' };

export default function WebhookDeliveries({ endpoint, deliveries }: Props) {
    return (
        <AdminLayout>
            <Head title="Webhook Deliveries" />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/webhooks"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <div>
                    <h1 className="text-2xl font-semibold">Deliveries</h1>
                    <p className="text-sm text-muted-foreground font-mono">{endpoint.url}</p>
                </div>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader><TableRow><TableHead>Event</TableHead><TableHead>State</TableHead><TableHead className="text-right">HTTP</TableHead><TableHead className="text-right">Attempts</TableHead><TableHead>Date</TableHead></TableRow></TableHeader>
                    <TableBody>
                        {deliveries.data.length === 0 ? (
                            <TableRow><TableCell colSpan={5} className="text-center py-10 text-muted-foreground">No deliveries yet.</TableCell></TableRow>
                        ) : deliveries.data.map(d => (
                            <TableRow key={d.id}>
                                <TableCell className="font-mono text-sm">{d.event_type}</TableCell>
                                <TableCell><Badge variant={STATE_BADGE[d.state] ?? 'secondary'}>{d.state}</Badge></TableCell>
                                <TableCell className="text-right">{d.response_status ?? '—'}</TableCell>
                                <TableCell className="text-right">{d.attempt_count}</TableCell>
                                <TableCell className="text-sm text-muted-foreground">{new Date(d.created_at).toLocaleString()}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
