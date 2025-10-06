<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-500 text-white p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold">Total Users</h3>
                                    <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
                                </div>
                                <div class="text-4xl opacity-75">
                                    👥
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-500 text-white p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold">Total Roles</h3>
                                    <p class="text-3xl font-bold">{{ $stats['total_roles'] }}</p>
                                </div>
                                <div class="text-4xl opacity-75">
                                    🛡️
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-500 text-white p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold">Total Permissions</h3>
                                    <p class="text-3xl font-bold">{{ $stats['total_permissions'] }}</p>
                                </div>
                                <div class="text-4xl opacity-75">
                                    🔑
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($stats['recent_users'] as $user)
                                <div class="px-6 py-4 flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                    <div class="ml-auto text-sm text-gray-500">
                                        {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-4 text-center text-gray-500">
                                    No users found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>