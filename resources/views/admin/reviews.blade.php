@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'មតិ' : 'Reviews')

@section('content')

<form method="GET" action="{{ route('admin.reviews') }}" class="panel p-3 mb-4">
    <div class="d-flex gap-2 align-items-center">
        <select name="status" class="form-ctrl" style="width:180px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'មតិទាំងអស់' : 'All Reviews' }}</option>
            <option value="pending"  {{ request('status')=='pending'  ? 'selected' : '' }}>{{ app()->getLocale()=='km' ? 'រង់ចាំការអនុម័ត' : 'Pending Approval' }}</option>
            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>{{ app()->getLocale()=='km' ? 'បានអនុម័ត' : 'Approved' }}</option>
        </select>
        @if(request('status'))
            <a href="{{ route('admin.reviews') }}" class="qa-btn">{{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}</a>
        @endif
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6>{{ app()->getLocale()=='km' ? 'មតិ' : 'Reviews' }} ({{ $reviews->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ការវាយតម្លៃ' : 'Rating' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'មតិ' : 'Comment' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td style="padding-left:20px;font-weight:600;font-size:13px;color:#111;">{{ $review->user->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#374151;">{{ Str::limit($review->product->name ?? '—', 25) }}</td>
                    <td><span style="font-size:13px;font-weight:600;color:#374151;">{{ $review->rating }}/5</span></td>
                    <td style="font-size:12px;color:#6b7280;max-width:200px;">{{ Str::limit($review->comment ?? '—', 50) }}</td>
                    <td style="font-size:12px;color:#9ca3af;">{{ $review->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="pill {{ $review->is_approved ? 'pill-delivered' : 'pill-pending' }}">
                            {{ $review->is_approved ? (app()->getLocale()=='km' ? 'បានអនុម័ត' : 'Approved') : (app()->getLocale()=='km' ? 'រង់ចាំ' : 'Pending') }}
                        </span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            @if(!$review->is_approved)
                            <form action="{{ route('admin.review.approve', $review->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="view-btn" style="border-color:#bbf7d0;color:#16a34a;background:#fff;">
                                    {{ app()->getLocale()=='km' ? 'អនុម័ត' : 'Approve' }}
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.review.delete', $review->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale()=='km' ? 'លប់ចោលមតិ?' : 'Delete review?' }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="view-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                                    {{ app()->getLocale()=='km' ? 'លប់' : 'Delete' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានមតិ' : 'No reviews found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        {{ $reviews->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection