import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface TaxZone { id: string; name: string; code: string; countries: string[]; rate_count: number; }
interface Props { zones: TaxZone[]; }

export default function TaxZonesIndex({ zones }: Props) {
    return (
        <AdminLayout>
            <Head title="Tax Zones" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Tax Zones</h1>
                <Button asChild><Link href="/admin/tax/zones/create"><Plus className="h-4 w-4 mr-1" />New Zone</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader><TableRow><TableHead>Name</TableHead><TableHead>Code</TableHead><TableHead>Countries</TableHead><TableHead className="text-right">Rates</TableHead><TableHead className="w-14" /></TableRow></TableHeader>
                    <TableBody>
                        {zones.length === 0 ? (
                            <TableRow><TableCell colSpan={5} className="text-center py-10 text-muted-foreground">No tax zones yet.</TableCell></TableRow>
                        ) : zones.map(z => (
                            <TableRow key={z.id}>
                                <TableCell className="font-medium">{z.name}</TableCell>
                                <TableCell className="font-mono text-sm">{z.code}</TableCell>
                                <TableCell className="text-sm">{z.countries.join(', ')}</TableCell>
                                <TableCell className="text-right">{z.rate_count}</TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/tax/zones/${z.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
