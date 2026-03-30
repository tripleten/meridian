import { Head, Link, usePage } from '@inertiajs/react';
import { PlusCircle } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { PageProps } from '@inertiajs/core';

interface CmsBlock {
    id: string;
    identifier: string;
    title: string;
    is_active: boolean;
    created_at: string;
}

interface Props extends PageProps {
    blocks: CmsBlock[];
}

export default function CmsBlocksIndex() {
    const { blocks } = usePage<Props>().props;

    return (
        <>
            <Head title="CMS Blocks" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Blocks</h1>
                        <p className="text-muted-foreground text-sm">Reusable content blocks for templates and widgets.</p>
                    </div>
                    <Button asChild>
                        <Link href="/admin/cms-blocks/create">
                            <PlusCircle className="mr-2 h-4 w-4" />New Block
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{blocks.length} block{blocks.length !== 1 ? 's' : ''}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Title</th>
                                    <th className="px-4 py-3 font-medium">Identifier</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {blocks.length === 0 && (
                                    <tr>
                                        <td colSpan={4} className="text-muted-foreground px-4 py-8 text-center">No blocks yet.</td>
                                    </tr>
                                )}
                                {blocks.map((block) => (
                                    <tr key={block.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">{block.title}</td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">{block.identifier}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={block.is_active ? 'default' : 'secondary'}>
                                                {block.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/cms-blocks/${block.id}/edit`}>Edit</Link>
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
