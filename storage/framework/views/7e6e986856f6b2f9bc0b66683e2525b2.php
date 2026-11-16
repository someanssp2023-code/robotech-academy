
<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<style>body, * { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
<style>
.stat-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 20px;
    transition: box-shadow .2s, transform .2s;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,.07);
}
.stat-lbl {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .7px;
    color: #24793d; margin-bottom: 6px;
}
.stat-val {
    font-size: 32px; font-weight: 800;
    color: #000000; line-height: 1;
}
.stat-footer {
    margin-top: 10px; padding-top: 10px;
    border-top: 1px solid #f3f4f6;
    font-size: 11px; color: #9ca3af;
    display: flex; align-items: center; justify-content: space-between;
}
.stat-footer a { color: #6b7280; text-decoration: none; font-size: 11px; }
.stat-footer a:hover { color: #111; }
.panel {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}
.panel-head {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex; align-items: center; justify-content: space-between;
}
.panel-head h6 { margin: 0; font-size: 13px; font-weight: 700; color: #111; }
.qa-btn {
    display: inline-flex; align-items: center;
    padding: 8px 14px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    text-decoration: none; transition: all .15s;
    border: 1px solid #e5e7eb; color: #374151;
    background: #fff;
}
.qa-btn:hover { background: #f9fafb; border-color: #d1d5db; color: #111; }
.qa-btn.qa-primary { background: #111; color: #fff; border-color: #111; }
.qa-btn.qa-primary:hover { background: #1f2937; }
.ra-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.ra-table thead th {
    padding: 10px 14px; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
    color: #9ca3af; background: #fafafa;
    border-bottom: 1px solid #f3f4f6; white-space: nowrap;
}
.ra-table tbody tr { border-bottom: 1px solid #f9fafb; transition: background .1s; }
.ra-table tbody tr:hover { background: #fafafa; }
.ra-table tbody td { padding: 12px 14px; vertical-align: middle; color: #374151; }
.ra-table tbody tr:last-child { border-bottom: none; }
.av {
    width: 30px; height: 30px; border-radius: 50%;
    background: #6366f1;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.pill-pending    { background:#fef3c7; color:#92400e; }
.pill-processing { background:#dbeafe; color:#1e40af; }
.pill-shipped    { background:#e0e7ff; color:#3730a3; }
.pill-delivered  { background:#d1fae5; color:#065f46; }
.pill-cancelled  { background:#fee2e2; color:#991b1b; }
.view-btn {
    font-size: 11px; font-weight: 600; padding: 5px 12px;
    border-radius: 7px; border: 1px solid #e5e7eb;
    background: #fff; color: #374151; text-decoration: none;
    transition: all .15s;
}
.view-btn:hover { background: #f3f4f6; border-color: #d1d5db; }
</style>


<div class="row g-3 mb-4">
    <?php
    $stats = [
        ['label'=> __('app.products'),        'value'=>$totalProducts,   'route'=>'admin.products'],
        ['label'=> __('app.categories'),      'value'=>$totalCategories, 'route'=>'admin.categories'],
        ['label'=> __('app.brands'),          'value'=>$totalBrands,     'route'=>'admin.brands'],
        ['label'=> app()->getLocale()=='km' ? 'ចំណូល' : 'Revenue', 'value'=>'$'.number_format($totalRevenue), 'route'=>'admin.orders'],
        ['label'=> __('app.orders'),          'value'=>$totalOrders,     'route'=>'admin.orders'],
        ['label'=> __('app.customers'),       'value'=>$totalUsers,      'route'=>'admin.users'],
        ['label'=> app()->getLocale()=='km' ? 'មតិរង់ចាំ' : 'Pending Reviews', 'value'=>$pendingReviews, 'route'=>'admin.reviews'],
        ['label'=> app()->getLocale()=='km' ? 'ស្តុកទាប' : 'Low Stock', 'value'=>$lowStock, 'route'=>'admin.stock'],
    ];
    ?>
    <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-lbl"><?php echo e($s['label']); ?></div>
            <div class="stat-val"><?php echo e($s['value']); ?></div>
            <div class="stat-footer">
                <a href="<?php echo e(route($s['route'])); ?>"><?php echo e(app()->getLocale()=='km' ? 'មើល' : 'View'); ?></a>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>



<div class="row g-4 mb-4">

    
    <div class="col-lg-5">
        <div class="panel h-100">
            <div class="panel-head">
                <h6><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញតាមស្ថានភាព' : 'Orders by Status'); ?></h6>
            </div>
            <div class="p-4">
                <?php
                    $statusCounts = [
                        'Pending'    => \App\Models\Order::where('status','Pending')->count(),
                        'Processing' => \App\Models\Order::where('status','Processing')->count(),
                        'Shipped'    => \App\Models\Order::where('status','Shipped')->count(),
                        'Delivered'  => \App\Models\Order::where('status','Delivered')->count(),
                        'Cancelled'  => \App\Models\Order::where('status','Cancelled')->count(),
                    ];
                ?>
                <div style="position:relative;height:220px;">
                    <canvas id="donutChart" role="img" aria-label="Donut chart showing orders by status">Orders by status breakdown</canvas>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:16px;justify-content:center;">
                    <span style="display:flex;align-items:center;gap:5px;font-size:12px;"><span style="width:10px;height:10px;border-radius:2px;background:#eda100;display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'រង់ចាំ' : 'Pending'); ?> (<?php echo e($statusCounts['Pending']); ?>)</span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:12px;"><span style="width:10px;height:10px;border-radius:2px;background:#2a78d6;display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'កំពុងដំណើរការ' : 'Processing'); ?> (<?php echo e($statusCounts['Processing']); ?>)</span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:12px;"><span style="width:10px;height:10px;border-radius:2px;background:#4a3aa7;display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'កំពុងដឹក' : 'Shipped'); ?> (<?php echo e($statusCounts['Shipped']); ?>)</span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:12px;"><span style="width:10px;height:10px;border-radius:2px;background:#1baf7a;display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'បានដឹកជញ្ជូន' : 'Delivered'); ?> (<?php echo e($statusCounts['Delivered']); ?>)</span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:12px;"><span style="width:10px;height:10px;border-radius:2px;background:#e34948;display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'បានបោះបង់' : 'Cancelled'); ?> (<?php echo e($statusCounts['Cancelled']); ?>)</span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-7">
        <div class="panel h-100">
            <div class="panel-head">
                <h6><?php echo e(app()->getLocale()=='km' ? 'ចំណូល & ការបញ្ជាទិញ — ' : 'Revenue & Orders — '); ?><?php echo e(now()->year); ?></h6>
            </div>
            <div class="p-4">
                <?php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    $revenueData = [];
                    $ordersData  = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $revenueData[] = (float) \App\Models\Order::whereYear('created_at', now()->year)
                            ->whereMonth('created_at', $m)
                            ->whereNotIn('status', ['Cancelled'])
                            ->sum('total_amount');
                        $ordersData[] = \App\Models\Order::whereYear('created_at', now()->year)
                            ->whereMonth('created_at', $m)
                            ->count();
                    }
                ?>
                <div style="display:flex;gap:16px;margin-bottom:12px;font-size:12px;">
                    <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:2px;background:rgba(42,120,214,0.4);display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders'); ?></span>
                    <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:3px;background:#eb6834;display:inline-block;"></span><?php echo e(app()->getLocale()=='km' ? 'ចំណូល ($)' : 'Revenue ($)'); ?></span>
                </div>
                <div style="position:relative;height:220px;">
                    <canvas id="comboChart" role="img" aria-label="Combined bar and line chart showing monthly revenue and orders">Monthly revenue and orders</canvas>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="panel mb-4">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ផលិតផលលក់ដាច់ 5 ចំណាត់' : 'Top 5 Best Selling Products'); ?></h6>
        <a href="<?php echo e(route('admin.products')); ?>" class="qa-btn" style="font-size:11px;padding:5px 12px;"><?php echo e(app()->getLocale()=='km' ? 'មើលទាំងអស់' : 'View All'); ?></a>
    </div>
    <?php
        $topProducts = \App\Models\OrderItem::select('product_id',
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'),
                \Illuminate\Support\Facades\DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product.category')
            ->get();
        $maxSold = $topProducts->max('total_sold') ?: 1;
    ?>
    <div class="p-4">
        <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $product = $item->product; ?>
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:<?php echo e($loop->last ? '0' : '16px'); ?>;">
            <div style="width:24px;height:24px;border-radius:50%;background:<?php echo e($i === 0 ? '#f59e0b' : ($i === 1 ? '#9ca3af' : ($i === 2 ? '#d97706' : '#f3f4f6'))); ?>;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:<?php echo e($i <= 2 ? '#fff' : '#6b7280'); ?>;flex-shrink:0;">
                <?php echo e($i + 1); ?>

            </div>
            <img src="<?php echo e($product->image_url ?? ''); ?>"
                 onerror="this.style.background='#f3f4f6'"
                 style="width:36px;height:36px;object-fit:contain;border-radius:8px;background:#f9fafb;padding:3px;flex-shrink:0;"/>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:600;font-size:13px;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?php echo e($product->name ?? 'Unknown Product'); ?>

                </div>
                <div style="font-size:11px;color:#9ca3af;"><?php echo e($product->category->name ?? ''); ?></div>
                <div style="height:4px;background:#f3f4f6;border-radius:4px;margin-top:6px;overflow:hidden;">
                    <div style="height:100%;width:<?php echo e(round(($item->total_sold / $maxSold) * 100)); ?>%;background:<?php echo e($i === 0 ? '#f59e0b' : '#6366f1'); ?>;border-radius:4px;"></div>
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-weight:700;font-size:13px;color:#111;"><?php echo e(number_format($item->total_sold)); ?> sold</div>
                <div style="font-size:11px;color:#f59e0b;font-weight:600;">$<?php echo e(number_format($item->total_revenue, 2)); ?></div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">No sales data yet.</div>
        <?php endif; ?>
    </div>
</div>


<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញថ្មីៗ' : 'Recent Orders'); ?></h6>
        <a href="<?php echo e(route('admin.orders')); ?>" class="qa-btn" style="font-size:11px;padding:5px 12px;"><?php echo e(app()->getLocale()=='km' ? 'មើលទាំងអស់' : 'View All'); ?></a>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Order'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sc = match($order->status) {
                        'Delivered'  => 'pill-delivered',
                        'Cancelled'  => 'pill-cancelled',
                        'Shipped'    => 'pill-shipped',
                        'Processing' => 'pill-processing',
                        default      => 'pill-pending',
                    };
                ?>
                <tr>
                    <td style="padding-left:20px;font-weight:700;color:#111;">
                        RA-<?php echo e($order->id); ?>

                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="av"><?php echo e(strtoupper(substr($order->user->name ?? '?', 0, 1))); ?></div>
                            <div>
                                <div style="font-weight:600;font-size:12px;color:#111;"><?php echo e($order->user->name); ?></div>
                                <div style="color:#9ca3af;font-size:11px;"><?php echo e($order->user->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#9ca3af;font-size:12px;"><?php echo e($order->created_at->format('M d, Y')); ?></td>
                    <td style="font-weight:700;color:#f59e0b;">$<?php echo e(number_format($order->total_amount, 2)); ?></td>
                    <td style="color:#6b7280;font-size:12px;"><?php echo e($order->payment_method); ?></td>
                    <td><span class="pill <?php echo e($sc); ?>"><?php echo e($order->status); ?></span></td>
                    <td style="padding-right:20px;text-align:right;">
                        <a href="<?php echo e(route('admin.order.detail', $order->id)); ?>" class="view-btn"><?php echo e(app()->getLocale()=='km' ? 'មើល' : 'View'); ?></a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានការបញ្ជាទិញ' : 'No orders yet.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const months = <?php echo json_encode($months, 15, 512) ?>;
const revenueData = <?php echo json_encode($revenueData, 15, 512) ?>;
const ordersData  = <?php echo json_encode($ordersData, 15, 512) ?>;
const statusData  = <?php echo json_encode(array_values($statusCounts), 15, 512) ?>;

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending','Processing','Shipped','Delivered','Cancelled'],
        datasets: [{
            data: statusData,
            backgroundColor: ['#eda100','#2a78d6','#4a3aa7','#1baf7a','#e34948'],
            borderWidth: 3,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('comboChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            {
                type: 'bar',
                label: 'Orders',
                data: ordersData,
                backgroundColor: 'rgba(42,120,214,0.25)',
                borderRadius: 4,
                barThickness: 16,
                yAxisID: 'y1',
            },
            {
                type: 'line',
                label: 'Revenue',
                data: revenueData,
                borderColor: '#eb6834',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#eb6834',
                tension: 0.4,
                yAxisID: 'y2',
                fill: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                ticks: { font: { size: 10 } },
                grid: { display: false }
            },
            y1: {
                position: 'left',
                ticks: { font: { size: 10 }, stepSize: 1 },
                grid: { color: '#f3f4f6' },
                title: { display: false }
            },
            y2: {
                position: 'right',
                ticks: {
                    font: { size: 10 },
                    callback: v => '$' + v.toLocaleString()
                },
                grid: { display: false }
            }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>