import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit } from 'lucide-react';

interface PaymentMethod {
    id: string;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
}

interface Props {
    methods: PaymentMethod[];
}

export default function PaymentMethodsIndex({ methods }: Props) {
    return (
        <AdminLayout>
            <Head title="Payment Methods" />

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Payment Methods</h1>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Code</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead className="text-right">Sort</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead className="w-14" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {methods.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={6} className="text-center py-10 text-muted-foreground">
                                    No payment methods found.
                                </TableCell>
                            </TableRow>
                        ) : methods.map(m => (
                            <TableRow key={m.id}>
                                <TableCell className="font-medium">{m.name}</TableCell>
                                <TableCell className="font-mono text-sm">{m.code}</TableCell>
                                <TableCell className="text-muted-foreground text-sm">{m.description ?? '—'}</TableCell>
                                <TableCell className="text-right">{m.sort_order}</TableCell>
                                <TableCell>
                                    <Badge variant={m.is_active ? 'default' : 'secondary'}>
                                        {m.is_active ? 'Active' : 'Inactive'}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Button variant="ghost" size="icon" asChild>
                                        <Link href={`/admin/payments/methods/${m.id}/edit`}>
                                            <Edit className="h-4 w-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
