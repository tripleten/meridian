import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface TaxRate { id: string; name: string; code: string; tax_zone_name: string | null; rate: number; type: string; is_shipping_taxable: boolean; }
interface Props { rates: TaxRate[]; }

export default function TaxRatesIndex({ rates }: Props) {
    return (
        <AdminLayout>
            <Head title="Tax Rates" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Tax Rates</h1>
                <Button asChild><Link href="/admin/tax/rates/create"><Plus className="h-4 w-4 mr-1" />New Rate</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Zone</TableHead>
                            <TableHead className="text-right">Rate</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Shipping</TableHead>
                            <TableHead className="w-14" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {rates.length === 0 ? (
                            <TableRow><TableCell colSpan={6} className="text-center py-10 text-muted-foreground">No tax rates yet.</TableCell></TableRow>
                        ) : rates.map(r => (
                            <TableRow key={r.id}>
                                <TableCell>
                                    <div className="font-medium">{r.name}</div>
                                    <div className="text-xs text-muted-foreground font-mono">{r.code}</div>
                                </TableCell>
                                <TableCell>{r.tax_zone_name ?? '—'}</TableCell>
                                <TableCell className="text-right font-medium">{(r.rate * 100).toFixed(1)}%</TableCell>
                                <TableCell><Badge variant="outline">{r.type}</Badge></TableCell>
                                <TableCell>{r.is_shipping_taxable ? <Badge variant="secondary">Yes</Badge> : '—'}</TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/tax/rates/${r.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
