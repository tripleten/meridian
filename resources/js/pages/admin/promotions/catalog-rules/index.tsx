import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface Rule { id: string; name: string; discount_type_label: string; discount_amount: number; priority: number; is_active: boolean; valid_until: string | null; }
interface Props { rules: Rule[]; }

export default function CatalogRulesIndex({ rules }: Props) {
    return (
        <AdminLayout>
            <Head title="Catalog Price Rules" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Catalog Price Rules</h1>
                <Button asChild><Link href="/admin/promotions/catalog-rules/create"><Plus className="h-4 w-4 mr-1" />New Rule</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader><TableRow><TableHead>Name</TableHead><TableHead>Discount</TableHead><TableHead className="text-right">Amount</TableHead><TableHead className="text-right">Priority</TableHead><TableHead>Status</TableHead><TableHead className="w-14" /></TableRow></TableHeader>
                    <TableBody>
                        {rules.length === 0 ? (
                            <TableRow><TableCell colSpan={6} className="text-center py-10 text-muted-foreground">No catalog rules yet.</TableCell></TableRow>
                        ) : rules.map(r => (
                            <TableRow key={r.id}>
                                <TableCell className="font-medium">{r.name}</TableCell>
                                <TableCell>{r.discount_type_label}</TableCell>
                                <TableCell className="text-right">{r.discount_amount}</TableCell>
                                <TableCell className="text-right">{r.priority}</TableCell>
                                <TableCell><Badge variant={r.is_active ? 'default' : 'secondary'}>{r.is_active ? 'Active' : 'Inactive'}</Badge></TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/promotions/catalog-rules/${r.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
