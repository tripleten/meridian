import { Link } from '@inertiajs/react';
import {
    BarChart3,
    Box,
    CreditCard,
    FileText,
    FolderTree,
    LayoutDashboard,
    List,
    Megaphone,
    Package,
    Settings,
    ShoppingCart,
    Tag,
    Truck,
    UserSquare,
    Users,
    Warehouse,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';

interface NavSection {
    label: string;
    items: { title: string; href: string; icon: React.ElementType }[];
}

const adminNav: NavSection[] = [
    {
        label: 'Overview',
        items: [
            { title: 'Dashboard', href: '/admin', icon: LayoutDashboard },
        ],
    },
    {
        label: 'Catalog',
        items: [
            { title: 'Products',   href: '/admin/products',   icon: Box },
            { title: 'Categories', href: '/admin/categories', icon: FolderTree },
            { title: 'Brands',     href: '/admin/brands',     icon: Tag },
        ],
    },
    {
        label: 'Customers',
        items: [
            { title: 'Customers', href: '/admin/customers', icon: UserSquare },
        ],
    },
    {
        label: 'Content',
        items: [
            { title: 'Pages',  href: '/admin/cms-pages',  icon: FileText },
            { title: 'Blocks', href: '/admin/cms-blocks', icon: Package },
        ],
    },
    {
        label: 'Sales',
        items: [
            { title: 'Orders',        href: '/admin/orders',                    icon: ShoppingCart },
            { title: 'Inventory',     href: '/admin/inventory/stock',            icon: Warehouse },
            { title: 'Sources',       href: '/admin/inventory/sources',          icon: Package },
            { title: 'Price Lists',   href: '/admin/pricing/price-lists',        icon: List },
            { title: 'Payments',      href: '/admin/payments/methods',           icon: CreditCard },
        ],
    },
    {
        label: 'Marketing',
        items: [
            { title: 'Promotions', href: '/admin/promotions', icon: Megaphone },
        ],
    },
    {
        label: 'Reports',
        items: [
            { title: 'Analytics', href: '/admin/reports', icon: BarChart3 },
        ],
    },
    {
        label: 'System',
        items: [
            { title: 'Admin Users', href: '/admin/users',    icon: Users },
            { title: 'Settings',    href: '/admin/settings', icon: Settings },
        ],
    },
];

export function AdminSidebar() {
    const { isCurrentUrl } = useCurrentUrl();

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/admin" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {adminNav.map((section) => (
                    <SidebarGroup key={section.label} className="px-2 py-0">
                        <SidebarGroupLabel>{section.label}</SidebarGroupLabel>
                        <SidebarMenu>
                            {section.items.map((item) => (
                                <SidebarMenuItem key={item.title}>
                                    <SidebarMenuButton
                                        asChild
                                        isActive={isCurrentUrl(item.href)}
                                        tooltip={{ children: item.title }}
                                    >
                                        <Link href={item.href} prefetch>
                                            <item.icon />
                                            <span>{item.title}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroup>
                ))}
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
