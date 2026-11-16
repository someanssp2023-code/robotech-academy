
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'របាយការណ៍' : 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<style>
.report-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden; margin-bottom:24px; }
.report-head { padding:16px 20px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; }
.report-head h6 { margin:0; font-size:13px; font-weight:700; color:#111; }
.summary-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:20px; }
.summary-lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin-bottom:6px; }
.summary-val { font-size:28px; font-weight:800; line-height:1; }
</style>


<div class="panel mb-4" style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
    <div class="report-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'តម្រងរបាយការណ៍' : 'Report Filter'); ?></h6>
    </div>
    <form method="GET" action="<?php echo e(route('admin.reports')); ?>" class="p-4" id="reportForm">

        
        <?php $currentType = request('report_type', 'sales'); ?>
        <input type="hidden" name="type" value="range"/>

        <div class="d-flex align-items-end gap-3 flex-wrap">

            
            <div>
                <label class="form-label fw-semibold small">
                    <?php echo e(app()->getLocale()=='km' ? 'ប្រភេទរបាយការណ៍' : 'Report Type'); ?>

                </label>
                <select name="report_type" id="reportTypeSelect" class="form-ctrl" onchange="toggleStockSub(this.value)" style="min-width:220px;">
                    <option value="sales"      <?php echo e($currentType==='sales'      ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍លក់'       : 'Sales Report'); ?></option>
                    <option value="products"   <?php echo e($currentType==='products'   ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍ផលិតផល'   : 'Stock Report'); ?></option>
                    <option value="categories" <?php echo e($currentType==='categories' ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍ប្រភេទ'   : 'Categories Report'); ?></option>
                    <option value="brands"     <?php echo e($currentType==='brands'     ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍ម៉ាក'     : 'Brands Report'); ?></option>
                    <option value="users"      <?php echo e($currentType==='users'      ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍អ្នកប្រើ' : 'Users Report'); ?></option>
                    <option value="stock"      <?php echo e($currentType==='stock'      ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍ស្តុក'    : 'History Report'); ?></option>
                </select>
            </div>

            
            <div id="stockSubRow" style="<?php echo e($currentType !== 'stock' ? 'display:none;' : ''); ?>">
                <label class="form-label fw-semibold small">
                    <?php echo e(app()->getLocale()=='km' ? 'ប្រភេទស្តុក' : 'Stock Type'); ?>

                </label>
                <select name="stock_type" class="form-ctrl" style="min-width:200px;">

                    <option value="restock" <?php echo e(request('stock_type')=='restock'    ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមស្តុក'     : 'Add Stock'); ?></option>
                    <option value="damaged" <?php echo e(request('stock_type')=='damaged'    ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'ខូចខាត'          : 'Damage'); ?></option>
                    <option value="returned"<?php echo e(request('stock_type')=='returned'   ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់មក'        : 'Return'); ?></option>
                </select>
            </div>

            
            <?php $showCatBrand = in_array($currentType, ['products','stock']); ?>
            <div id="categoryRow" style="<?php echo e(!$showCatBrand ? 'display:none;' : ''); ?>">
                <label class="form-label fw-semibold small">
                    <?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?>

                </label>
                <select name="category_id" class="form-ctrl" style="min-width:180px;">
                    <option value=""><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Categories'); ?></option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id')==$cat->id ? 'selected' : ''); ?>>
                            <?php echo e($cat->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div id="brandRow" style="<?php echo e(!$showCatBrand ? 'display:none;' : ''); ?>">
                <label class="form-label fw-semibold small">
                    <?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?>

                </label>
                <select name="brand_id" class="form-ctrl" style="min-width:180px;">
                    <option value=""><?php echo e(app()->getLocale()=='km' ? 'ម៉ាកទាំងអស់' : 'All Brands'); ?></option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand_id')==$brand->id ? 'selected' : ''); ?>>
                            <?php echo e($brand->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div id="dateRangeRow">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <div>
                        <label class="form-label fw-semibold small"><?php echo e(app()->getLocale()=='km' ? 'ពីថ្ងៃ' : 'Date From'); ?></label>
                        <input type="date" name="date_from" class="form-ctrl"
                               value="<?php echo e(request('date_from', date('Y-m-01'))); ?>" style="min-width:160px;"/>
                    </div>
                    <div style="padding-bottom:9px;color:#6b7280;font-weight:600;font-size:13px;">—</div>
                    <div>
                        <label class="form-label fw-semibold small"><?php echo e(app()->getLocale()=='km' ? 'ដល់ថ្ងៃ' : 'Date To'); ?></label>
                        <input type="date" name="date_to" class="form-ctrl"
                               value="<?php echo e(request('date_to', date('Y-m-d'))); ?>" style="min-width:160px;"/>
                    </div>
                </div>
            </div>

            
            <div id="timeRangeRow" style="<?php echo e($currentType !== 'sales' ? 'display:none;' : ''); ?>">
                <div class="d-flex align-items-end gap-3 flex-wrap" style="margin-top:12px;">
                    <div>
                        <label class="form-label fw-semibold small"><?php echo e(app()->getLocale()=='km' ? 'ពីម៉ោង' : 'Time From'); ?></label>
                        <input type="time" name="time_from" class="form-ctrl"
                               value="<?php echo e(request('time_from', '00:00')); ?>" style="min-width:140px;"/>
                    </div>
                    <div style="padding-bottom:9px;color:#6b7280;font-weight:600;font-size:13px;">—</div>
                    <div>
                        <label class="form-label fw-semibold small"><?php echo e(app()->getLocale()=='km' ? 'ដល់ម៉ោង' : 'Time To'); ?></label>
                        <input type="time" name="time_to" class="form-ctrl"
                               value="<?php echo e(request('time_to', '23:59')); ?>" style="min-width:140px;"/>
                    </div>
                </div>
            </div>

            
            <div>
                <label class="form-label fw-semibold small d-block" style="visibility:hidden;">.</label>
                <button type="submit" class="qa-btn qa-primary" style="padding:9px 22px;white-space:nowrap;">
                    <?php echo e(app()->getLocale()=='km' ? 'បង្កើតរបាយការណ៍' : 'Generate Report'); ?>

                </button>
            </div>

        </div>

        <script>
        function toggleStockSub(val) {
            document.getElementById('timeRangeRow').style.display  = (val === 'sales') ? 'block' : 'none';
            document.getElementById('stockSubRow').style.display   = (val === 'stock') ? 'block' : 'none';
            // Category & Brand filters visible for Products and Stock
            var showCatBrand = (val === 'products' || val === 'stock');
            document.getElementById('categoryRow').style.display   = showCatBrand ? 'block' : 'none';
            document.getElementById('brandRow').style.display      = showCatBrand ? 'block' : 'none';
            // Clear stock_type when switching away from stock report
            if (val !== 'stock') {
                var st = document.querySelector('select[name="stock_type"]');
                if (st) st.value = '';
            }
            // Clear category/brand when switching to a type that doesn't use them
            if (!showCatBrand) {
                var cat = document.querySelector('select[name="category_id"]');
                var brd = document.querySelector('select[name="brand_id"]');
                if (cat) cat.value = '';
                if (brd) brd.value = '';
            }
        }
        </script>

    </form>
</div>


<div class="d-flex justify-content-between align-items-center mb-4">
    <div style="font-size:13px;color:#6b7280;">
        <?php if(isset($reportData)): ?>
            <?php echo e(app()->getLocale()=='km' ? 'របាយការណ៍' : 'Report'); ?>:
            <strong style="color:#111;"><?php echo e($reportData['period_label']); ?></strong>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.reports.excel', array_merge(request()->all(), ['locale' => app()->getLocale()]))); ?>"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">⬇ Excel</a>
        <a href="<?php echo e(route('admin.reports.pdf', request()->all())); ?>"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">⬇ PDF</a>
    </div>
</div>

<?php if(isset($reportData)): ?>




<?php if($reportType === 'sales'): ?>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ចំណូលសរុប' : 'Total Revenue'); ?></div>
            <div class="summary-val" style="color:#111;">$<?php echo e(number_format($reportData['total_revenue'], 2)); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញសរុប' : 'Total Orders'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['total_orders'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផលបានលក់' : 'Products Sold'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['total_items'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'មធ្យមការបញ្ជាទិញ' : 'Avg Order Value'); ?></div>
            <div class="summary-val" style="color:#111;">$<?php echo e(number_format($reportData['avg_order'], 2)); ?></div>
        </div>
    </div>
</div>




<div class="report-card">
    <div class="report-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ផលិតផលបានលក់' : 'Products Sold'); ?> — <?php echo e($reportData['period_label']); ?></h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'បានលក់' : 'Units Sold'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ចំណូល' : 'Revenue'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'តម្លៃមធ្យម' : 'Avg Price'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;"><?php echo e($i+1); ?></td>
                    <td><div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($p->product_name); ?></div></td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($p->product->category->name ?? '—'); ?></td>
                    <td>
                        <span style="font-weight:600;color:#374151;"><?php echo e(number_format($p->total_sold)); ?></span>
                        <span style="color:#9ca3af;font-size:11px;"><?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'units'); ?></span>
                    </td>
                    <td style="font-weight:600;color:#374151;">$<?php echo e(number_format($p->total_revenue, 2)); ?></td>
                    <td style="padding-right:20px;text-align:right;font-size:13px;color:#374151;">
                        $<?php echo e(number_format($p->total_revenue / max($p->total_sold, 1), 2)); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានទិន្នន័យ' : 'No data for this period.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="report-card">
    <div class="report-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders'); ?> — <?php echo e($reportData['period_label']); ?></h6>
        <span style="font-size:12px;color:#9ca3af;"><?php echo e($reportData['total_orders']); ?> <?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'orders'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'លេខ' : 'Order'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'Items'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['orders']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sc = match($order->status){
                        'Delivered'  => 'pill-delivered',
                        'Cancelled'  => 'pill-cancelled',
                        'Shipped'    => 'pill-shipped',
                        'Processing' => 'pill-processing',
                        default      => 'pill-pending'
                    };
                    $statusLabels = [
                        'Pending'    => app()->getLocale()=='km' ? 'រង់ចាំ'         : 'Pending',
                        'Processing' => app()->getLocale()=='km' ? 'កំពុងដំណើរការ' : 'Processing',
                        'Shipped'    => app()->getLocale()=='km' ? 'កំពុងដឹក'      : 'Shipped',
                        'Delivered'  => app()->getLocale()=='km' ? 'បានដឹកជញ្ជូន' : 'Delivered',
                        'Cancelled'  => app()->getLocale()=='km' ? 'បានបោះបង់'     : 'Cancelled',
                    ];
                ?>
                <tr>
                    <td style="padding-left:20px;font-weight:700;color:#111;">RA-<?php echo e($order->id); ?></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($order->user->name); ?></div>
                        <div style="color:#9ca3af;font-size:11px;"><?php echo e($order->user->email); ?></div>
                    </td>
                    <td style="color:#9ca3af;font-size:12px;"><?php echo e($order->created_at->format('M d, Y h:i A')); ?></td>
                    <td style="font-size:13px;color:#374151;"><?php echo e($order->orderItems->count()); ?></td>
                    <td style="font-weight:600;color:#374151;">$<?php echo e(number_format($order->total_amount, 2)); ?></td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($order->payment_method); ?></td>
                    <td><span class="pill <?php echo e($sc); ?>"><?php echo e($statusLabels[$order->status] ?? $order->status); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានការបញ្ជាទិញ' : 'No orders for this period.'); ?>

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
new Chart(document.getElementById('revenueChart'), {
    type:'bar',
    data:{
        labels: <?php echo json_encode($reportData['chart_labels'], 15, 512) ?>,
        datasets:[
            {
                type:'bar',
                label:'<?php echo e(app()->getLocale()=="km" ? "ការបញ្ជាទិញ" : "Orders"); ?>',
                data:<?php echo json_encode($reportData['chart_orders'], 15, 512) ?>,
                backgroundColor:'rgba(42,120,214,0.25)',
                borderRadius:4, barThickness:20, yAxisID:'y1'
            },
            {
                type:'line',
                label:'<?php echo e(app()->getLocale()=="km" ? "ចំណូល" : "Revenue"); ?>',
                data:<?php echo json_encode($reportData['chart_revenue'], 15, 512) ?>,
                borderColor:'#eb6834', borderWidth:2,
                pointRadius:3, pointBackgroundColor:'#eb6834',
                tension:0.4, fill:false, yAxisID:'y2'
            }
        ]
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{
            x:{ ticks:{font:{size:11}}, grid:{display:false} },
            y1:{ position:'left',  ticks:{font:{size:11},stepSize:1}, grid:{color:'#f3f4f6'} },
            y2:{ position:'right', ticks:{font:{size:11},callback:v=>'$'+v.toLocaleString()}, grid:{display:false} }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>





<?php elseif($reportType === 'products'): ?>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផលសរុប' : 'Total Products'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['total'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['active'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'អស់ស្ទុក' : 'Out of Stock'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['out_of_stock'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ស្ទុកតិច' : 'Low Stock'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['low_stock'])); ?></div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> <?php echo e(app()->getLocale()=='km' ? 'ផលិតផលទាំងអស់' : 'All Products'); ?> (<?php echo e($reportData['total']); ?>)</h6>
        <span style="font-size:12px;color:#6b7280;">
            <?php echo e(app()->getLocale()=='km' ? 'តម្លៃស្ទុកសរុប' : 'Total Stock Value'); ?>:
            <strong style="color:#374151;">$<?php echo e(number_format($reportData['total_value'], 2)); ?></strong>
        </span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'តម្លៃ' : 'Price'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ទុក' : 'Stock'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Created'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;"><?php echo e($i+1); ?></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($p->name); ?></div>
                        <div style="color:#9ca3af;font-size:11px;"><?php echo e($p->sku ?? ''); ?></div>
                    </td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($p->category->name ?? '—'); ?></td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($p->brand->name ?? '—'); ?></td>
                    <td style="font-weight:600;color:#374151;">$<?php echo e(number_format($p->price, 2)); ?></td>
                    <td><span style="font-weight:700;font-size:13px;color:#374151;"><?php echo e($p->stock); ?></span></td>
                    <td>
                        <?php if($p->is_active): ?>
                            <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></span>
                        <?php else: ?>
                            <span class="pill pill-cancelled"><?php echo e(app()->getLocale()=='km' ? 'បិទ' : 'Inactive'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding-right:20px;text-align:right;font-size:12px;color:#9ca3af;">
                        <?php echo e($p->created_at->format('M d, Y')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានផលិតផល' : 'No products found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>





<?php elseif($reportType === 'categories'): ?>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទសរុប' : 'Total Categories'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['total'])); ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['active'])); ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'មានផលិតផល' : 'Has Products'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['with_products'])); ?></div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> <?php echo e(app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Categories'); ?> (<?php echo e($reportData['total']); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ឈ្មោះ' : 'Name'); ?></th>

                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'លំដាប់' : 'Sort Order'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;"><?php echo e($i+1); ?></td>
                    <td style="font-weight:600;font-size:13px;color:#111;"><?php echo e($c->name); ?></td>

                    <td>
                        <span style="font-weight:600;color:#374151;"><?php echo e($c->products_count); ?></span>
                        <span style="color:#9ca3af;font-size:11px;"><?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'items'); ?></span>
                    </td>
                    <td style="font-size:13px;color:#374151;"><?php echo e($c->sort_order ?? 0); ?></td>
                    <td style="padding-right:20px;text-align:right;">
                        <?php if($c->is_active ?? true): ?>
                            <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></span>
                        <?php else: ?>
                            <span class="pill pill-cancelled"><?php echo e(app()->getLocale()=='km' ? 'បិទ' : 'Inactive'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានប្រភេទ' : 'No categories found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>





<?php elseif($reportType === 'brands'): ?>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'ម៉ាកសរុប' : 'Total Brands'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['total'])); ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['active'])); ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e($reportData['total'] - $reportData['active']); ?></div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> <?php echo e(app()->getLocale()=='km' ? 'ម៉ាកទាំងអស់' : 'All Brands'); ?> (<?php echo e($reportData['total']); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></th>

                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការពិពណ៌នា' : 'Description'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;"><?php echo e($i+1); ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <?php if($b->logo_url ?? null): ?>
                                <img src="<?php echo e($b->logo_url); ?>" style="width:32px;height:32px;object-fit:contain;border-radius:6px;border:1px solid #e5e7eb;" alt="">
                            <?php else: ?>
                                <div style="width:32px;height:32px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;"></div>
                            <?php endif; ?>
                            <span style="font-weight:600;font-size:13px;color:#111;"><?php echo e($b->name); ?></span>
                        </div>
                    </td>

                    <td>
                        <span style="font-weight:600;color:#374151;"><?php echo e($b->products_count); ?></span>
                        <span style="color:#9ca3af;font-size:11px;"><?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'items'); ?></span>
                    </td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e(\Illuminate\Support\Str::limit($b->description ?? '—', 40)); ?></td>
                    <td style="padding-right:20px;text-align:right;">
                        <?php if($b->is_active): ?>
                            <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></span>
                        <?php else: ?>
                            <span class="pill pill-cancelled"><?php echo e(app()->getLocale()=='km' ? 'បិទ' : 'Inactive'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានម៉ាក' : 'No brands found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>





<?php elseif($reportType === 'users'): ?>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'អ្នកប្រើសរុប' : 'Total Users'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['total'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customers'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['customers'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'អ្នកគ្រប់គ្រង' : 'Admins'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['admins'])); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl"><?php echo e(app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive'); ?></div>
            <div class="summary-val" style="color:#111;"><?php echo e(number_format($reportData['inactive'])); ?></div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> <?php echo e(app()->getLocale()=='km' ? 'អ្នកប្រើទាំងអស់' : 'All Users'); ?> (<?php echo e($reportData['total']); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អ្នកប្រើ' : 'User'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អ៊ីមែល' : 'Email'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'តួនាទី' : 'Role'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'ចូលរួម' : 'Joined'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $roleColor = match($u->role ?? 'Customer'){
                        'Admin'  => '#dc2626',
                        'Staff'  => '#7c3aed',
                        default  => '#2563eb',
                    };
                ?>
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;"><?php echo e($i+1); ?></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($u->name); ?></div>
                        <?php if($u->phone ?? null): ?>
                        <div style="color:#9ca3af;font-size:11px;"><?php echo e($u->phone); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($u->email); ?></td>
                    <td>
                        <span style="font-size:11px;font-weight:700;color:<?php echo e($roleColor); ?>;background:<?php echo e($roleColor); ?>18;padding:3px 8px;border-radius:20px;">
                            <?php echo e($u->role ?? 'Customer'); ?>

                        </span>
                    </td>
                    <td>
                        <?php if($u->is_active): ?>
                            <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></span>
                        <?php else: ?>
                            <span class="pill pill-cancelled"><?php echo e(app()->getLocale()=='km' ? 'ហាមឃាត់' : 'Banned'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding-right:20px;text-align:right;font-size:12px;color:#9ca3af;">
                        <?php echo e($u->created_at->format('M d, Y')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានអ្នកប្រើ' : 'No users found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>





<?php elseif($reportType === 'stock'): ?>

<?php
    $stockSub = $reportData['stock_sub'] ?? request('stock_type', '');
    $stockSubLabel = match($stockSub) {
        'restock'  => (app()->getLocale()=='km' ? 'បន្ថែមស្តុក' : 'Add Stock'),
        'damaged'  => (app()->getLocale()=='km' ? 'ខូចខាត'       : 'Damage'),
        'returned' => (app()->getLocale()=='km' ? 'ត្រឡប់មក'     : 'Return'),

    };
?>







<?php if($stockSub === '__inventory__'): ?>

<div class="report-card">
    <div class="report-head">
        <h6>📦 <?php echo e(app()->getLocale()=='km' ? 'ផលិតផលទាំងអស់' : 'All Products'); ?> (<?php echo e(number_format($reportData['total_products'] ?? 0)); ?>)</h6>
        <span style="font-size:12px;color:#6b7280;">
            <?php echo e(app()->getLocale()=='km' ? 'តម្លៃស្ទុករួម' : 'Total Stock Value'); ?>:
            <strong style="color:#374151;">$<?php echo e(number_format($reportData['total_value'] ?? 0, 2)); ?></strong>
            &nbsp;|&nbsp;
            <span style="color:#ef4444;"><?php echo e(app()->getLocale()=='km' ? 'អស់ស្ទុក' : 'Out of stock'); ?>: <strong><?php echo e($reportData['out_of_stock'] ?? 0); ?></strong></span>
            &nbsp;|&nbsp;
            <span style="color:#f59e0b;"><?php echo e(app()->getLocale()=='km' ? 'ស្ទុកតិច' : 'Low stock'); ?>: <strong><?php echo e($reportData['low_stock'] ?? 0); ?></strong></span>
        </span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></th>
                    <th style="text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'តម្លៃ' : 'Price'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ស្ទុក' : 'Stock'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['products'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;"><?php echo e($i + 1); ?></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($p->name); ?></div>
                        <?php if($p->sku): ?>
                        <div style="color:#9ca3af;font-size:11px;"><?php echo e($p->sku); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($p->category->name ?? '—'); ?></td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($p->brand->name ?? '—'); ?></td>
                    <td style="text-align:right;font-weight:600;color:#374151;">$<?php echo e(number_format($p->price, 2)); ?></td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;font-size:14px;color:#374151;"><?php echo e($p->stock); ?></span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <?php if($p->is_active): ?>
                            <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></span>
                        <?php else: ?>
                            <span class="pill pill-cancelled"><?php echo e(app()->getLocale()=='km' ? 'បិទ' : 'Inactive'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនមានផលិតផល' : 'No products found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>




<?php else: ?>

<div class="report-card">
    <div class="report-head">
        <h6>
            <?php if($stockSub === '' || $stockSub === null): ?>
                <span style="color:#6366f1;">📋 <?php echo e(app()->getLocale()=='km' ? 'ប្រវត្តិស្ទុកទាំងអស់' : 'Total Stock History (All)'); ?></span>
            <?php elseif($stockSub === 'restock'): ?>
                <span style="color:#16a34a;">＋ <?php echo e(app()->getLocale()=='km' ? 'ប្រវត្តិបន្ថែមស្តុក' : 'Add Stock History'); ?></span>
            <?php elseif($stockSub === 'damaged'): ?>
                <span style="color:#dc2626;">🔴 <?php echo e(app()->getLocale()=='km' ? 'ប្រវត្តិខូចខាត' : 'Damage History'); ?></span>
            <?php elseif($stockSub === 'returned'): ?>
                <span style="color:#2563eb;">↩ <?php echo e(app()->getLocale()=='km' ? 'ប្រវត្តិត្រឡប់មក' : 'Return History'); ?></span>
            <?php endif; ?>
        </h6>
        <span style="font-size:12px;color:#9ca3af;"><?php echo e(count($reportData['items'] ?? [])); ?> <?php echo e(app()->getLocale()=='km' ? 'កំណត់ត្រា' : 'records'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Type'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'មុន' : 'Before'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរ' : 'Change'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ក្រោយ' : 'After'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ដោយ' : 'By'); ?></th>
                    <th style="padding-right:20px;"><?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reportData['items'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isAdd       = ($h->stock_after >= $h->stock_before);
                    $diff        = abs($h->stock_after - $h->stock_before);
                    $change      = $isAdd ? '+' . $diff : '−' . $diff;
                    $reason = $h->reason
                        ?? ($h->adj_type === 'add'      ? 'restock'
                          : ($h->adj_type === 'subtract' ? 'damaged'
                          : ($isAdd                      ? 'restock' : 'damaged')));
                    $typeLabel = match($reason) {
                        'damaged'  => ['icon'=>'🔴', 'label'=> app()->getLocale()=='km' ? 'ខូចខាត'       : 'Damage',    'color'=>'#dc2626', 'bg'=>'#fee2e2'],
                        'returned' => ['icon'=>'↩',  'label'=> app()->getLocale()=='km' ? 'ត្រឡប់មក'     : 'Return',    'color'=>'#2563eb', 'bg'=>'#dbeafe'],
                        default    => ['icon'=>'＋',  'label'=> app()->getLocale()=='km' ? 'បន្ថែមស្តុក' : 'Add Stock', 'color'=>'#16a34a', 'bg'=>'#dcfce7'],
                    };
                ?>
                <tr>
                    <td style="padding-left:20px;font-size:12px;color:#6b7280;">
                        <?php echo e($h->created_at->format('d M Y')); ?><br>
                        <span style="font-size:11px;color:#9ca3af;"><?php echo e($h->created_at->format('h:i A')); ?></span>
                    </td>
                    <td style="font-weight:600;font-size:13px;color:#111;"><?php echo e(\Illuminate\Support\Str::limit($h->product->name ?? '—', 28)); ?></td>
                    <td style="text-align:center;">
                        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:<?php echo e($typeLabel['bg']); ?>;color:<?php echo e($typeLabel['color']); ?>;">
                            <?php echo e($typeLabel['icon']); ?> <?php echo e($typeLabel['label']); ?>

                        </span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:#6b7280;font-weight:600;"><?php echo e($h->stock_before); ?></td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#374151;font-size:14px;"><?php echo e($change); ?></span>
                    </td>
                    <td style="text-align:center;font-weight:700;font-size:14px;color:#111;"><?php echo e($h->stock_after); ?></td>
                    <td style="font-size:13px;color:#374151;"><?php echo e($h->addedBy->name ?? '—'); ?></td>
                    <td style="padding-right:20px;font-size:12px;color:#9ca3af;"><?php echo e($h->note ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានប្រវត្តិ' : 'No history for this period.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>


<?php endif; ?>


<?php endif; ?>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/report.blade.php ENDPATH**/ ?>