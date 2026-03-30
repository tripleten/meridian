import { Head, Link, router, usePage } from '@inertiajs/react';
import { PlusCircle, ChevronRight } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { PageProps } from '@inertiajs/core';

interface Category {
    id: string;
    parent_id: string | null;
    name: string;
    url_key: string;
    is_active: boolean;
    depth: number;
    sort_order: number;
}

interface Props extends PageProps {
    categories: Category[];
}

export default function CategoriesIndex() {
    const { categories } = usePage<Props>().props;

    return (
        <>
            <Head title="Categories" />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Categories</h1>
                        <p className="text-muted-foreground text-sm">Manage product categories.</p>
                    </div>
                    <Button asChild>
                        <Link href="/admin/categories/create">
                            <PlusCircle className="mr-2 h-4 w-4" />New Category
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{categories.length} categor{categories.length !== 1 ? 'ies' : 'y'}</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b bg-muted/50 text-left">
                                    <th className="px-4 py-3 font-medium">Name</th>
                                    <th className="px-4 py-3 font-medium">URL Key</th>
                                    <th className="px-4 py-3 font-medium">Status</th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {categories.length === 0 && (
                                    <tr>
                                        <td colSpan={4} className="text-muted-foreground px-4 py-8 text-center">No categories yet.</td>
                                    </tr>
                                )}
                                {categories.map((cat) => (
                                    <tr key={cat.id} className="border-b last:border-0 hover:bg-muted/30">
                                        <td className="px-4 py-3 font-medium">
                                            <span style={{ paddingLeft: `${cat.depth * 20}px` }} className="flex items-center gap-1">
                                                {cat.depth > 0 && <ChevronRight className="text-muted-foreground h-3 w-3 shrink-0" />}
                                                {cat.name}
                                            </span>
                                        </td>
                                        <td className="text-muted-foreground px-4 py-3 font-mono text-xs">{cat.url_key}</td>
                                        <td className="px-4 py-3">
                                            <Badge variant={cat.is_active ? 'default' : 'secondary'}>
                                                {cat.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button asChild variant="ghost" size="sm">
                                                <Link href={`/admin/categories/${cat.id}/edit`}>Edit</Link>
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
