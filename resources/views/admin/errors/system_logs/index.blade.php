@extends('layouts.dashboard')

@section('title', 'System Health')

@section('item')
<li class="breadcrumb-item">System Logs</li>
@endsection

@section('active')
<li class="breadcrumb-item active">Database Logs</li>
@endsection

@section('content')
<style>
    .system-logs { --log-ink:#172b4d; --log-muted:#718096; --log-line:#e7edf3; --log-blue:#2866c5; color:var(--log-ink); }
    .system-logs__hero { display:flex; justify-content:space-between; align-items:flex-end; gap:24px; margin-bottom:22px; padding:28px 30px; border-radius:16px; color:#fff; background:linear-gradient(120deg,#142c4e,#1d5278 72%,#287b8c); box-shadow:0 12px 30px rgba(23,52,84,.13); }
    .system-logs__eyebrow { display:flex; align-items:center; gap:8px; margin-bottom:9px; color:#c5e0f5; font-size:10px; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
    .system-logs__eyebrow i { color:#ffb9a8; }
    .system-logs__hero h1 { margin:0; color:#fff; font-size:clamp(26px,3vw,36px); font-weight:800; letter-spacing:-.03em; }
    .system-logs__hero p { max-width:590px; margin:8px 0 0; color:#d8e5f0; font-size:13px; }
    .system-logs__actions { display:flex; flex-wrap:wrap; gap:9px; }
    .system-logs__actions .btn { white-space:nowrap; }
    .system-logs__stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; margin-bottom:20px; }
    .system-logs__stat { display:flex; align-items:center; gap:14px; min-height:92px; padding:17px; border:1px solid var(--log-line); border-radius:13px; background:#fff; box-shadow:0 4px 16px rgba(22,43,77,.035); }
    .system-logs__stat-icon { display:grid; flex:0 0 42px; width:42px; height:42px; place-items:center; border-radius:12px; color:#2866c5; background:#eaf2ff; font-size:17px; }
    .system-logs__stat--danger .system-logs__stat-icon { color:#b84942; background:#fff0ed; }
    .system-logs__stat--amber .system-logs__stat-icon { color:#a16c1b; background:#fff6df; }
    .system-logs__stat--teal .system-logs__stat-icon { color:#187b73; background:#e6f7f3; }
    .system-logs__stat-label { display:block; color:var(--log-muted); font-size:10px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .system-logs__stat-value { display:block; margin-top:3px; color:var(--log-ink); font-size:21px; font-weight:800; line-height:1.2; }
    .system-logs__stat-value--date { font-size:14px; }
    .system-logs__panel { overflow:hidden; margin-bottom:18px; border:1px solid var(--log-line); border-radius:14px; background:#fff; box-shadow:0 5px 20px rgba(22,43,77,.04); }
    .system-logs__panel-head { display:flex; justify-content:space-between; align-items:center; gap:15px; padding:18px 20px; border-bottom:1px solid var(--log-line); }
    .system-logs__panel-head h2 { margin:0; color:var(--log-ink); font-size:15px; font-weight:800; }
    .system-logs__panel-head p { margin:4px 0 0; color:var(--log-muted); font-size:11px; }
    .system-logs__recurring { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; padding:16px 20px; }
    .system-logs__recurring-item { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:12px 14px; border:1px solid #edf1f5; border-radius:10px; background:#fafbfd; }
    .system-logs__recurring-message { overflow:hidden; color:#30435e; font-size:11px; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
    .system-logs__recurring-count { flex:0 0 auto; padding:4px 8px; border-radius:99px; color:#a23e39; background:#fff0ed; font-size:10px; font-weight:800; }
    .system-logs__filters { display:flex; flex-wrap:wrap; align-items:end; gap:10px; padding:16px 20px; border-bottom:1px solid var(--log-line); background:#fbfcfe; }
    .system-logs__filters label { display:block; margin-bottom:5px; color:#64748b; font-size:10px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
    .system-logs__filters .form-control { min-width:180px; height:38px; border-color:#dce4ec; border-radius:8px; font-size:12px; }
    .system-logs__table-wrap { overflow-x:auto; }
    .system-logs__table { width:100%; min-width:850px; margin:0; border-collapse:collapse; }
    .system-logs__table th { padding:11px 14px; border-bottom:1px solid var(--log-line); color:#718096; background:#fff; font-size:9px; font-weight:800; letter-spacing:.09em; text-align:left; text-transform:uppercase; white-space:nowrap; }
    .system-logs__table td { padding:14px; border-bottom:1px solid #edf1f5; color:#52627a; font-size:11px; vertical-align:top; }
    .system-logs__table tbody tr:last-child td { border-bottom:0; }
    .system-logs__table tbody tr:hover { background:#fbfdff; }
    .system-logs__level { display:inline-flex; align-items:center; gap:6px; padding:5px 8px; border-radius:99px; font-size:9px; font-weight:850; letter-spacing:.04em; text-transform:uppercase; }
    .system-logs__level:before { width:6px; height:6px; border-radius:50%; background:currentColor; content:""; }
    .system-logs__level--error,.system-logs__level--critical,.system-logs__level--alert,.system-logs__level--emergency { color:#a43d36; background:#fff0ed; }
    .system-logs__message { max-width:520px; color:#253952; font-size:12px; font-weight:650; line-height:1.5; overflow-wrap:anywhere; }
    .system-logs__source { display:inline-block; padding:4px 7px; border:1px solid #e2e9f0; border-radius:6px; color:#60728a; background:#f8fafc; font-family:monospace; font-size:10px; }
    .system-logs__time { color:#64748b; white-space:nowrap; }
    .system-logs__details { margin-top:8px; }
    .system-logs__details summary { width:max-content; color:var(--log-blue); font-size:10px; font-weight:750; cursor:pointer; }
    .system-logs__details pre { max-width:580px; max-height:250px; overflow:auto; margin:8px 0 0; padding:11px; border:1px solid #e4eaf0; border-radius:8px; color:#d8e5f0; background:#172b42; font-size:10px; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; }
    .system-logs__empty { padding:45px 18px!important; color:#728096!important; text-align:center; }
    .system-logs__pagination { padding:0 18px 16px; }
    @media(max-width:1000px) { .system-logs__stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:700px) { .system-logs__hero { align-items:flex-start; flex-direction:column; padding:22px; } .system-logs__recurring { grid-template-columns:1fr; } .system-logs__panel-head { align-items:flex-start; flex-direction:column; } }
    @media(max-width:480px) { .system-logs__stats { grid-template-columns:1fr; } .system-logs__filters>* { width:100%; } .system-logs__filters .form-control { width:100%; } }
</style>

<div class="content-body system-logs">
    <section>
        <div class="system-logs__hero">
            <div>
                <div class="system-logs__eyebrow"><i class="fa fa-circle" aria-hidden="true"></i> Operations · Monitoring</div>
                <h1>System health</h1>
                <p>Review application errors, spot repeated failures, and open the full context when you need to investigate.</p>
            </div>
            <div class="system-logs__actions">
                <a href="{{ route('error-files.index') }}" class="btn btn-light"><i class="fa fa-file-text-o mr-1"></i> Log files</a>
                <a href="{{ route('errors.index') }}" class="btn btn-outline-light"><i class="fa fa-refresh mr-1"></i> Refresh</a>
                <form action="{{ route('errors.clear') }}" method="POST" onsubmit="return confirm('Clear all database error logs? This cannot be undone.')">
                    @csrf
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash-o mr-1"></i> Clear logs</button>
                </form>
            </div>
        </div>

        @include('includes.alerts')

        <div class="system-logs__stats">
            <div class="system-logs__stat">
                <span class="system-logs__stat-icon"><i class="fa fa-list-alt"></i></span>
                <div><span class="system-logs__stat-label">Total log entries</span><strong class="system-logs__stat-value">{{ number_format($stats['total']) }}</strong></div>
            </div>
            <div class="system-logs__stat system-logs__stat--danger">
                <span class="system-logs__stat-icon"><i class="fa fa-exclamation-triangle"></i></span>
                <div><span class="system-logs__stat-label">Critical severity</span><strong class="system-logs__stat-value">{{ number_format($stats['critical']) }}</strong></div>
            </div>
            <div class="system-logs__stat system-logs__stat--amber">
                <span class="system-logs__stat-icon"><i class="fa fa-repeat"></i></span>
                <div><span class="system-logs__stat-label">Recurring patterns</span><strong class="system-logs__stat-value">{{ number_format($stats['recurring']) }}</strong></div>
            </div>
            <div class="system-logs__stat system-logs__stat--teal">
                <span class="system-logs__stat-icon"><i class="fa fa-clock-o"></i></span>
                <div><span class="system-logs__stat-label">Latest entry</span><strong class="system-logs__stat-value system-logs__stat-value--date">{{ $stats['latest'] ? \Illuminate\Support\Carbon::parse($stats['latest'])->format('M j, Y · g:i A') : 'No entries yet' }}</strong></div>
            </div>
        </div>

        <div class="system-logs__panel">
            <div class="system-logs__panel-head">
                <div><h2>Recurring errors</h2><p>Messages seen most often across recent application failures.</p></div>
                <span class="text-muted small">Top {{ $recurring->count() }}</span>
            </div>
            @if($recurring->isNotEmpty())
                <div class="system-logs__recurring">
                    @foreach($recurring as $error)
                        <div class="system-logs__recurring-item">
                            <span class="system-logs__recurring-message" title="{{ $error->message }}">{{ \Illuminate\Support\Str::limit($error->message, 150) }}</span>
                            <span class="system-logs__recurring-count">{{ number_format($error->total) }}×</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 text-muted small">No recurring errors recorded yet.</div>
            @endif
        </div>

        <div class="system-logs__panel">
            <div class="system-logs__panel-head">
                <div><h2>Recent error entries</h2><p>{{ number_format($logs->total()) }} matching records · newest first</p></div>
            </div>
            <form method="GET" action="{{ route('errors.index') }}" class="system-logs__filters">
                <div>
                    <label for="log-search">Search message or source</label>
                    <input id="log-search" name="q" value="{{ $search }}" class="form-control" placeholder="e.g. replyTo, EmailService">
                </div>
                <div>
                    <label for="log-level">Severity</label>
                    <select id="log-level" name="level" class="form-control">
                        <option value="">All levels</option>
                        @foreach(['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'] as $option)
                            <option value="{{ $option }}" @selected($level === $option)>{{ ucfirst(strtolower($option)) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Filter</button>
                @if($search || $level)<a href="{{ route('errors.index') }}" class="btn btn-light border">Reset</a>@endif
            </form>

            <div class="system-logs__table-wrap">
                <table class="system-logs__table">
                    <thead><tr><th>Severity</th><th>Error</th><th>Source</th><th>Logged at</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php($levelClass = strtolower($log->level))
                            <tr>
                                <td><span class="system-logs__level system-logs__level--{{ $levelClass }}">{{ $log->level }}</span></td>
                                <td>
                                    <div class="system-logs__message">{{ $log->message }}</div>
                                    @if(!empty($log->context))
                                        <details class="system-logs__details">
                                            <summary>View context</summary>
                                            <pre>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @endif
                                    @if($log->stack_trace)
                                        <details class="system-logs__details">
                                            <summary>View stack trace</summary>
                                            <pre>{{ $log->stack_trace }}</pre>
                                        </details>
                                    @endif
                                </td>
                                <td><span class="system-logs__source">{{ $log->source ?: 'app' }}</span></td>
                                <td class="system-logs__time">{{ optional($log->logged_at)->format('M j, Y · g:i:s A') }}</td>
                                <td><a href="{{ route('error.delete', $log->id) }}" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Delete this log entry?')" aria-label="Delete log entry"><i class="fa fa-trash-o"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="system-logs__empty"><i class="fa fa-check-circle fa-2x d-block mb-2 text-success"></i>No error logs match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())<div class="system-logs__pagination">{{ $logs->links() }}</div>@endif
        </div>
    </section>
</div>
@endsection
