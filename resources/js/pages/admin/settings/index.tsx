import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/lib/utils';

interface Field {
    key: string;
    label: string;
    type: 'text' | 'email' | 'url' | 'number' | 'textarea' | 'boolean' | 'code';
}

interface Props {
    groups: Record<string, string>;
    currentGroup: string;
    currentLabel: string;
    settings: Record<string, string>;
    groupFields: Field[];
}

export default function SettingsIndex({ groups, currentGroup, currentLabel, settings, groupFields }: Props) {
    const initial: Record<string, string | boolean> = {};
    groupFields.forEach(f => {
        if (f.type === 'boolean') {
            initial[f.key] = settings[f.key] === 'true' || settings[f.key] === '1';
        } else {
            initial[f.key] = settings[f.key] ?? '';
        }
    });

    const { data, setData, put, processing } = useForm<Record<string, string | boolean>>(initial);

    function submit(e: React.FormEvent) {
        e.preventDefault();
        put(`/admin/settings/${currentGroup}`);
    }

    return (
        <AdminLayout>
            <Head title="Settings" />

            <div className="flex items-start gap-6">
                {/* Sidebar nav */}
                <nav className="w-48 shrink-0 space-y-1">
                    {Object.entries(groups).map(([key, label]) => (
                        <Link
                            key={key}
                            href={key === 'general' ? '/admin/settings' : `/admin/settings/${key}`}
                            className={cn(
                                'block px-3 py-2 rounded text-sm',
                                currentGroup === key
                                    ? 'bg-primary text-primary-foreground font-medium'
                                    : 'hover:bg-muted',
                            )}
                        >
                            {label}
                        </Link>
                    ))}
                </nav>

                {/* Form */}
                <div className="flex-1">
                    <h1 className="text-2xl font-semibold mb-6">{currentLabel} Settings</h1>

                    <form onSubmit={submit} className="max-w-xl space-y-5">
                        {groupFields.map(field => (
                            <div key={field.key} className="space-y-1">
                                <Label htmlFor={field.key}>{field.label}</Label>

                                {field.type === 'boolean' ? (
                                    <div className="flex items-center gap-3 pt-1">
                                        <Switch
                                            id={field.key}
                                            checked={!!data[field.key]}
                                            onCheckedChange={v => setData(field.key, v)}
                                        />
                                    </div>
                                ) : field.type === 'textarea' || field.type === 'code' ? (
                                    <Textarea
                                        id={field.key}
                                        value={String(data[field.key] ?? '')}
                                        onChange={e => setData(field.key, e.target.value)}
                                        rows={field.type === 'code' ? 8 : 3}
                                        className={field.type === 'code' ? 'font-mono text-xs' : ''}
                                    />
                                ) : (
                                    <Input
                                        id={field.key}
                                        type={field.type}
                                        value={String(data[field.key] ?? '')}
                                        onChange={e => setData(field.key, e.target.value)}
                                    />
                                )}
                            </div>
                        ))}

                        {groupFields.length === 0 && (
                            <p className="text-muted-foreground text-sm">No settings in this group yet.</p>
                        )}

                        <div className="pt-2">
                            <Button type="submit" disabled={processing}>Save Settings</Button>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
