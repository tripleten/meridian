import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface CartRule { id: string; name: string; discount_type_label: string; discount_amount: number; is_active: boolean; valid_until: string | null; sort_order: number; }
interface PaginatedRules { data: CartRule[]; }
interface Props { rules: PaginatedRules; }

export default function CartRulesIndex({ rules }: Props) {
    return (
        <AdminLayout>
            <Head title="Cart Rules" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Cart Rules</h1>
                <Button asChild><Link href="/admin/promotions/cart-rules/create"><Plus className="h-4 w-4 mr-1" />New Rule</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow><TableHead>Name</TableHead><TableHead>Discount</TableHead><TableHead className="text-right">Amount</TableHead><TableHead>Expires</TableHead><TableHead>Status</TableHead><TableHead className="w-14" /></TableRow>
                    </TableHeader>
                    <TableBody>
                        {rules.data.length === 0 ? (
                            <TableRow><TableCell colSpan={6} className="text-center py-10 text-muted-foreground">No cart rules yet.</TableCell></TableRow>
                        ) : rules.data.map(r => (
                            <TableRow key={r.id}>
                                <TableCell className="font-medium">{r.name}</TableCell>
                                <TableCell>{r.discount_type_label}</TableCell>
                                <TableCell className="text-right">{r.discount_amount}</TableCell>
                                <TableCell className="text-sm">{r.valid_until ? new Date(r.valid_until).toLocaleDateString() : '—'}</TableCell>
                                <TableCell><Badge variant={r.is_active ? 'default' : 'secondary'}>{r.is_active ? 'Active' : 'Inactive'}</Badge></TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/promotions/cart-rules/${r.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
