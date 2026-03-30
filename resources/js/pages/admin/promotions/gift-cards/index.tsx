import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Plus } from 'lucide-react';

interface GiftCard { id: string; code: string; state: string; state_label: string; state_badge: string; initial_balance: number; remaining_balance: number; currency_code: string; expires_at: string | null; }
interface PaginatedGiftCards { data: GiftCard[]; }
interface Props { giftCards: PaginatedGiftCards; }

const BADGE: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = { default: 'default', secondary: 'secondary', destructive: 'destructive', outline: 'outline' };

function fmt(amount: number, currency: string) { return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount / 100); }

export default function GiftCardsIndex({ giftCards }: Props) {
    return (
        <AdminLayout>
            <Head title="Gift Cards" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold">Gift Cards</h1>
                <Button asChild><Link href="/admin/promotions/gift-cards/create"><Plus className="h-4 w-4 mr-1" />New Gift Card</Link></Button>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader><TableRow><TableHead>Code</TableHead><TableHead>State</TableHead><TableHead className="text-right">Initial</TableHead><TableHead className="text-right">Remaining</TableHead><TableHead>Expires</TableHead><TableHead className="w-14" /></TableRow></TableHeader>
                    <TableBody>
                        {giftCards.data.length === 0 ? (
                            <TableRow><TableCell colSpan={6} className="text-center py-10 text-muted-foreground">No gift cards yet.</TableCell></TableRow>
                        ) : giftCards.data.map(g => (
                            <TableRow key={g.id}>
                                <TableCell className="font-mono font-medium">{g.code}</TableCell>
                                <TableCell><Badge variant={BADGE[g.state_badge] ?? 'secondary'}>{g.state_label}</Badge></TableCell>
                                <TableCell className="text-right">{fmt(g.initial_balance, g.currency_code)}</TableCell>
                                <TableCell className="text-right">{fmt(g.remaining_balance, g.currency_code)}</TableCell>
                                <TableCell className="text-sm">{g.expires_at ? new Date(g.expires_at).toLocaleDateString() : '—'}</TableCell>
                                <TableCell><Button variant="ghost" size="icon" asChild><Link href={`/admin/promotions/gift-cards/${g.id}/edit`}><Edit className="h-4 w-4" /></Link></Button></TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AdminLayout>
    );
}
