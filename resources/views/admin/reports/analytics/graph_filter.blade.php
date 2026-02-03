@php
    $defaultFrom = null;
    $defaultTo = null;
@endphp
<div class="row">
    <div class="col-md-12" style="margin-top: 5px; padding-left: 0px;">
        <p class="footnote">Track all transactions made in one place</p>
    </div>
</div>

<div class="row" style="margin-bottom:30px">
    <div class="col-12">
        <div>
            <form class="row graph-submit">
                <div class="row">
                    <!-- Business Developer -->
                    <div class="col-md-2 mb-2 mt-2">
                        <label for="business_developer_id" class="form-label">Business Developer</label>
                        <select name="business_developer_id" class="form-control">
                        </select>
                    </div>

                    <!-- Customer Email -->
                    <div class="col-md-2 mb-2 mt-2">
                        <label for="email" class="form-label">Customer Email</label>
                        <input type="email" name="email" class="form-control" value="{{ request('email') }}">
                    </div>

                    <!-- Phone -->
                    <div class="col-md-2 mb-2 mt-2">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ request('phone') }}">
                    </div>

                    <div class="col-md-4 mb-2 mt-2">
                        <label for="date_from" class="form-label">Date</label>
                        <div class="input-group input-daterange" id="date-range">
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', $defaultFrom) }}" id="date_from">
                            <span class="input-group-addon">To</span>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', $defaultTo) }}" id="date_to">
                        </div>
                        <div id="date-range-errors" class="text-danger"></div>
                    </div>

                    <input type="hidden" name="filter_period" value="">

                    <div class="col-md-2 mb-2 mt-2 filter-actions d-flex flex-column justify-content-end">
                        <div class="d-flex align-items-end action-row">
                            <button type="submit" class="btn btn-success apply-btn">Apply</button>

                            <a href="javascript:void(0)" id="resetFilters" class="reset-link text-danger ms-2">
                                <i class="fa fa-refresh" style="margin-right:3px;"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row card-body" style="margin-top: 40px">
    <!-- Chart -->
    <div class="col-md-9 qwsjqWEd" style=" padding-top: 20px;">
        <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between;">
            <p class="text-muted" style="font-size: 15px"><span id="graph-period">{{ ucfirst(request()->filter_period) }}</span> Transaction Insight</p>
        </div>
        <div class="position-relative">
            <div id="graph-loader" style="
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: none;
                z-index: 10;
            ">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <canvas id="transactionGraph" height="180"></canvas>
        </div>
    </div>

    <div class="col-md-3">
        <div class="" style="height: 400px;">
            <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                <strong style="margin-bottom:4px;">{{ $legendName }}</strong>
                @if($allowProductCollapse)
                    <span
                        id="toggleProductCollapse"
                        data-collapsed="0"
                        style="
                            font-size: 12px;
                            cursor: pointer;
                            color: #0d6efd;
                            user-select: none;
                            white-space: nowrap;
                        "
                    >
                        Collapse Products
                    </span>
                @endif
            </div>

            <div class="card-body product-legend" data-simplebar  data-simplebar-auto-hide="false"  id="legendContainer">

                @if (empty($datasets))
                    <p style="font-weight: normal;font-size: 12px; margin-top: 10px;">No data available for this filter</p>
                @else
                    <div style="font-size: 12px; margin-left: 13px;">
                        <input type="checkbox" id="checkAllProducts" checked>
                        <label for="checkAllProducts" style="cursor:pointer;">All</label>
                    </div>

                    @foreach($legends as $index => $legend)
                        @php
                            $dataset = collect($datasets)->first(fn($d) => $d['product_id'] == $legend->id);
                            $color = $dataset['backgroundColor'] ?? '#ccc';
                            // dd($color, $datasets, $legend);
                        @endphp
                        <div class="legend-item mb-1" style="display: flex; align-items: flex-start;">
                            <!-- Left: color + checkbox -->
                            <div style="display:flex; align-items:flex-start; flex-shrink:0; margin-right:6px;">
                                <span class="dot-qwcQEWD" style="background-color: {{ $color }}; border-color: {{ $color }};"></span>

                                <input type="checkbox"
                                    class="product-checkbox"
                                    value="{{ $legend->id }}"
                                    checked
                                    id="product-{{ $legend->id }}"
                                    style="margin-right: 5px; margin-top: 0px;">
                            </div>

                            <!-- Right: label aligned with checkbox -->
                            <label for="product-{{ $legend->id }}"
                                style="margin:0; cursor:pointer; font-weight:100; font-size:12px; line-height:1.25; flex:1; padding-top:1px;">
                                {{ ucfirst($legend->name) }}
                            </label>
                        </div>

                    @endforeach

                @endif
            </div>
            @if (count($legends) >= 13)
                <p style="font-weight: normal;font-size: 12px; margin-top: 10px;">Scroll to see more</p>
            @endif

        </div>

    </div>
    @php
        $type = $type ?? 'nill';
    @endphp

    <div style="margin-top: 40px;" class="col-md-9">
        <div style="display: flex; gap: 20px; align-items: center; justify-content: center">
            <button class="btn btn-success insight-btn" id="openPeriodModal" style="position: initial; margin-right: 0px;">
                Customize
            </button>
            <span class="level">{{ ucfirst(request()->level) }}</span>
        </div>
    </div>
</div>
