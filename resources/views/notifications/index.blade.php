@extends('layouts.dashboard')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-1">Pemberitahuan dan pengingat untuk Anda</p>
        </div>
        
        @if($notifications->where('is_read', false)->count() > 0)
        <form action="{{ url('/notifications/mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-blue-600 hover:text-blue-800 font-semibold">
                <i class="fas fa-check-double mr-1"></i>Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    @if($notifications->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <div class="w-20 h-20 rounded-full empty-state-icon flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-bell-slash text-accent-600 text-3xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">Tidak Ada Notifikasi</h3>
        <p class="text-slate-500 text-sm">Anda belum memiliki notifikasi apapun</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($notifications as $notification)
        <div class="bg-white rounded-xl shadow-md p-5 {{ !$notification->is_read ? 'border-l-4 border-blue-500' : '' }} hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        @switch($notification->type)
                            @case('loan')
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-hand-holding text-green-600"></i>
                                </div>
                                @break
                            @case('return')
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-undo text-blue-600"></i>
                                </div>
                                @break
                            @case('reminder')
                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                                @break
                            @case('overdue')
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                @break
                            @default
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-info text-gray-600"></i>
                                </div>
                        @endswitch
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 {{ !$notification->is_read ? 'font-bold' : '' }}">
                            {{ $notification->title }}
                        </h4>
                        <p class="text-gray-600 text-sm mt-1">{{ $notification->message }}</p>
                        <p class="text-gray-400 text-xs mt-2">
                            <i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                
                @if(!$notification->is_read)
                <form action="{{ url('/notifications/' . $notification->id . '/read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-blue-500 hover:text-blue-700" title="Tandai dibaca">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
