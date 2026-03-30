import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft } from 'lucide-react';

interface Props {
    knownEvents: string[];
    generatedSecret: string;
}

export default function WebhookCreate({ knownEvents, generatedSecret }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        url:               '',
        secret:            generatedSecret,
        is_active:         true as boolean,
        subscribed_events: [] as string[],
    });

    function toggleEvent(ev: string) {
        setData('subscribed_events',
            data.subscribed_events.includes(ev)
                ? data.subscribed_events.filter(e => e !== ev)
                : [...data.subscribed_events, ev]
        );
    }

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/admin/webhooks');
    }

    return (
        <AdminLayout>
            <Head title="New Webhook Endpoint" />

            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild>
                    <Link href="/admin/webhooks"><ArrowLeft className="h-4 w-4" /></Link>
                </Button>
                <h1 className="text-2xl font-semibold">New Webhook Endpoint</h1>
            </div>

            <form onSubmit={submit} className="max-w-xl space-y-5">
                <div className="space-y-1">
                    <Label>URL *</Label>
                    <Input
                        value={data.url}
                        onChange={e => setData('url', e.target.value)}
                        placeholder="https://your-site.com/webhooks"
                        required
                    />
                    {errors.url && <p className="text-sm text-destructive">{errors.url}</p>}
                </div>

                <div className="space-y-1">
                    <Label>Signing Secret *</Label>
                    <Input
                        value={data.secret}
                        onChange={e => setData('secret', e.target.value)}
                        placeholder="Min 16 characters"
                        required
                    />
                    <p className="text-xs text-muted-foreground">Used to sign payloads with HMAC-SHA256. Store this securely — it won't be shown again.</p>
                    {errors.secret && <p className="text-sm text-destructive">{errors.secret}</p>}
                </div>

                <div className="space-y-2">
                    <Label>Subscribed Events *</Label>
                    {errors.subscribed_events && (
                        <p className="text-sm text-destructive">{errors.subscribed_events as unknown as string}</p>
                    )}
                    <div className="border rounded-md divide-y">
                        {knownEvents.map(ev => (
                            <label key={ev} className="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-muted/50">
                                <input
                                    type="checkbox"
                                    checked={data.subscribed_events.includes(ev)}
                                    onChange={() => toggleEvent(ev)}
                                    className="h-4 w-4"
                                />
                                <span className="font-mono text-sm">{ev}</span>
                            </label>
                        ))}
                    </div>
                </div>

                <div className="flex items-center gap-3">
                    <Switch checked={data.is_active} onCheckedChange={v => setData('is_active', v)} id="is_active" />
                    <Label htmlFor="is_active">Active</Label>
                </div>

                <div className="flex gap-3 pt-2">
                    <Button type="submit" disabled={processing}>Create Endpoint</Button>
                    <Button variant="outline" asChild>
                        <Link href="/admin/webhooks">Cancel</Link>
                    </Button>
                </div>
            </form>
        </AdminLayout>
    );
}
