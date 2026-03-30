import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface TaxRule { id: string; name: string; priority: number; tax_class_ids: string[]; tax_zone_ids: string[]; tax_rate_ids: string[]; is_active: boolean; }
interface Props { rules: TaxRule[]; }

export default function TaxRulesIndex({ rules }: Props) {
    return (
        <AdminLayout>
            <Head title="Tax Rules" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Tax Rules</h1>
                <Button asChild><Link href="/admin/tax/rules/create"><Plus className="h-4 w-4 mr-1" />New Rule</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead className="text-right">Priority</TableHead>
                            <TableHead>Classes</TableHead>
                            <TableHead>Zones</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead className="w-14" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {rules.length === 0 ? (
                            <TableRow><TableCell colSpan={6} className="text-center py-10 text-muted-foreground">No tax rules yet.</TableCell></TableRow>
                        ) : rules.map(r => (
                            <TableRow key={r.id}>
                                <TableCell className="font-medium">{r.name}</TableCell>
                                <TableCell className="text-right">{r.priority}</TableCell>
                                <TableCell className="text-sm text-muted-foreground">{r.tax_class_ids.length} class(es)</TableCell>
                                <TableCell className="text-sm text-muted-foreground">{r.tax_zone_ids.length} zone(s)</TableCell>
                                <TableCell><Badge variant={r.is_active ? 'default' : 'secondary'}>{r.is_active ? 'Active' : 'Inactive'}</Badge></TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/tax/rules/${r.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
