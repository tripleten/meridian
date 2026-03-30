import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface PriceList {
    id: string;
    name: string;
    code: string;
    currency_code: string;
    is_default: boolean;
    is_active: boolean;
    customer_group_name: string | null;
    item_count: number;
}

interface Props {
    priceLists: PriceList[];
}

export default function PriceListsIndex({ priceLists }: Props) {
    return (
        <AdminLayout>
            <Head title="Price Lists" />

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Price Lists</h1>
                <Button asChild>
                    <Link href="/admin/pricing/price-lists/create">
                        <Plus className="h-4 w-4 mr-1" /> New Price List
                    </Link>
                </Button>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Code</TableHead>
                            <TableHead>Currency</TableHead>
                            <TableHead>Customer Group</TableHead>
                            <TableHead className="text-right">Items</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead className="w-14" />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {priceLists.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={7} className="text-center py-10 text-muted-foreground">
                                    No price lists yet. Create one to get started.
                                </TableCell>
                            </TableRow>
                        ) : priceLists.map(pl => (
                            <TableRow key={pl.id}>
                                <TableCell>
                                    <div className="flex items-center gap-2">
                                        <span className="font-medium">{pl.name}</span>
                                        {pl.is_default && <Badge variant="default" className="text-xs">Default</Badge>}
                                    </div>
                                </TableCell>
                                <TableCell className="font-mono text-sm">{pl.code}</TableCell>
                                <TableCell>{pl.currency_code}</TableCell>
                                <TableCell>{pl.customer_group_name ?? <span className="text-muted-foreground">All customers</span>}</TableCell>
                                <TableCell className="text-right">{pl.item_count}</TableCell>
                                <TableCell>
                                    <Badge variant={pl.is_active ? 'default' : 'secondary'}>
                                        {pl.is_active ? 'Active' : 'Inactive'}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Button variant="ghost" size="icon" asChild>
                                        <Link href={`/admin/pricing/price-lists/${pl.id}/edit`}>
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
