@extends('backend.layout.app')

@section('title', 'User Login Activity')

@section('page-title', 'User Login Activity')

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}" class="text-slate-600 hover:text-slate-900">Users</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-1 text-slate-400"></i>
    <span class="font-medium text-slate-900">Login Activity</span>
@endsection

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-subtle overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center">
                        <i data-lucide="history" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">User Login/Logout Report</h2>
                        <p class="text-sm text-slate-500">Track login and logout time for each user</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Users
                </a>
            </div>

            <form method="GET" action="{{ route('admin.users.login-activity') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label for="user_id" class="block text-xs font-semibold text-slate-700 mb-1">User</label>
                    <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (string) $selectedUserId === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-xs font-semibold text-slate-700 mb-1">From Date</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="date_to" class="block text-xs font-semibold text-slate-700 mb-1">To Date</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">Filter</button>
                    <a href="{{ route('admin.users.login-activity') }}" class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 transition-all">Reset</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Login Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Logout Time</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">Duration</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-700 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($activities as $index => $activity)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-700">{{ $activities->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="text-slate-900 font-medium">{{ $activity->user_name ?? 'Unknown User' }}</div>
                            <div class="text-slate-500 text-xs">{{ $activity->user_email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ $activity->user_role === 'SF001' ? 'SF1' : ($activity->user_role === 'SF002' ? 'SF2' : ($activity->user_role === 'SF003' ? 'SF3' : ($activity->user_role ?? 'N/A'))) }}
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ \Carbon\Carbon::parse($activity->login_at)->format('M d, Y h:i:s A') }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            @if($activity->logout_at)
                                {{ \Carbon\Carbon::parse($activity->logout_at)->format('M d, Y h:i:s A') }}
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">Active Session</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            @if($activity->logout_at)
                                {{ \Carbon\Carbon::parse($activity->login_at)->diffForHumans(\Carbon\Carbon::parse($activity->logout_at), true) }}
                            @else
                                {{ \Carbon\Carbon::parse($activity->login_at)->diffForHumans(now(), true) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $activity->ip_address ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                                <p class="text-slate-600 font-medium">No login activity found</p>
                                <p class="text-slate-500 text-sm">Try changing filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
