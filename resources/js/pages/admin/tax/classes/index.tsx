import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface TaxClass { id: string; name: string; code: string; created_at: string; }
interface Props { classes: TaxClass[]; }

export default function TaxClassesIndex({ classes }: Props) {
    return (
        <AdminLayout>
            <Head title="Tax Classes" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Tax Classes</h1>
                <Button asChild><Link href="/admin/tax/classes/create"><Plus className="h-4 w-4 mr-1" />New Class</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader><TableRow><TableHead>Name</TableHead><TableHead>Code</TableHead><TableHead className="w-14" /></TableRow></TableHeader>
                    <TableBody>
                        {classes.length === 0 ? (
                            <TableRow><TableCell colSpan={3} className="text-center py-10 text-muted-foreground">No tax classes yet.</TableCell></TableRow>
                        ) : classes.map(c => (
                            <TableRow key={c.id}>
                                <TableCell className="font-medium">{c.name}</TableCell>
                                <TableCell className="font-mono text-sm">{c.code}</TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/tax/classes/${c.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
