import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface Coupon {
    id: string; code: string; type_label: string; times_used: number;
    usage_limit: number | null; is_active: boolean; valid_until: string | null;
}
interface PaginatedCoupons { data: Coupon[]; links: { url: string | null; label: string; active: boolean }[]; }
interface Props { coupons: PaginatedCoupons; }

export default function CouponsIndex({ coupons }: Props) {
    return (
        <AdminLayout>
            <Head title="Coupons" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Coupons</h1>
                <Button asChild><Link href="/admin/promotions/coupons/create"><Plus className="h-4 w-4 mr-1" />New Coupon</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Code</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead className="text-right">Uses</TableHead>
                            <TableHead>Expires</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead className="w-14" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {coupons.data.length === 0 ? (
                            <TableRow><TableCell colSpan={6} className="text-center py-10 text-muted-foreground">No coupons yet.</TableCell></TableRow>
                        ) : coupons.data.map(c => (
                            <TableRow key={c.id}>
                                <TableCell className="font-mono font-medium">{c.code}</TableCell>
                                <TableCell>{c.type_label}</TableCell>
                                <TableCell className="text-right">{c.times_used}{c.usage_limit ? ` / ${c.usage_limit}` : ''}</TableCell>
                                <TableCell className="text-sm">{c.valid_until ? new Date(c.valid_until).toLocaleDateString() : '—'}</TableCell>
                                <TableCell><Badge variant={c.is_active ? 'default' : 'secondary'}>{c.is_active ? 'Active' : 'Inactive'}</Badge></TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/promotions/coupons/${c.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
