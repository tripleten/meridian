import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft } from 'lucide-react';

interface GiftCard { id: string; code: string; state: string; state_badge: string; initial_balance: number; remaining_balance: number; currency_code: string; expires_at: string | null; }
interface StateOption { value: string; label: string; }
interface Props { giftCard: GiftCard; stateOptions: StateOption[]; }

const BADGE: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = { default: 'default', secondary: 'secondary', destructive: 'destructive', outline: 'outline' };

function fmt(amount: number, currency: string) { return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount / 100); }

export default function GiftCardEdit({ giftCard, stateOptions }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        state: giftCard.state,
        expires_at: giftCard.expires_at?.substring(0, 10) ?? '',
    });

    return (
        <AdminLayout>
            <Head title={`Edit ${giftCard.code}`} />
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/admin/promotions/gift-cards"><ArrowLeft className="h-4 w-4" /></Link></Button>
                <h1 className="text-2xl font-semibold">Edit Gift Card</h1>
            </div>
            <div className="max-w-md space-y-5">
                {/* Info card */}
                <div className="border rounded-md p-4 space-y-2 text-sm bg-muted/30">
                    <div className="flex justify-between"><span className="text-muted-foreground">Code</span><span className="font-mono font-medium">{giftCard.code}</span></div>
                    <div className="flex justify-between"><span className="text-muted-foreground">Initial Balance</span><span>{fmt(giftCard.initial_balance, giftCard.currency_code)}</span></div>
                    <div className="flex justify-between"><span className="text-muted-foreground">Remaining</span><span className="font-medium">{fmt(giftCard.remaining_balance, giftCard.currency_code)}</span></div>
                    <div className="flex justify-between"><span className="text-muted-foreground">State</span><Badge variant={BADGE[giftCard.state_badge] ?? 'secondary'}>{giftCard.state}</Badge></div>
                </div>

                <form onSubmit={e => { e.preventDefault(); put(`/admin/promotions/gift-cards/${giftCard.id}`); }} className="space-y-4">
                    <div className="space-y-1">
                        <Label>State *</Label>
                        <Select value={data.state} onValueChange={v => setData('state', v)}>
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>{stateOptions.map(s => <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>)}</SelectContent>
                        </Select>
                        {errors.state && <p className="text-sm text-destructive">{errors.state}</p>}
                    </div>
                    <div className="space-y-1">
                        <Label>Expires At</Label>
                        <Input type="date" value={data.expires_at} onChange={e => setData('expires_at', e.target.value)} />
                    </div>
                    <div className="flex gap-3 pt-2">
                        <Button type="submit" disabled={processing}>Save Changes</Button>
                        <Button variant="outline" asChild><Link href="/admin/promotions/gift-cards">Cancel</Link></Button>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
