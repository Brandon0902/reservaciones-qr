<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Users</h2>

      <a href="{{ route('admin.users.create') }}"
         class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New user
      </a>
    </div>
  </x-slot>

  <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
    @if(session('success'))
      <div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
        {{ session('success') }}
      </div>
    @endif

    @php
      $badge = function ($role) {
        $map = [
          'admin'     => 'bg-rose-100 text-rose-700 ring-rose-200 dark:bg-rose-900/40 dark:text-rose-200 dark:ring-rose-800',
          'validator' => 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-800',
          'customer'  => 'bg-indigo-100 text-indigo-800 ring-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-200 dark:ring-indigo-800',
        ];
        $cls = $map[$role] ?? 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700';
        return "<span class=\"inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 $cls\">$role</span>";
      };
    @endphp

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
        {{ $users->total() }} results
      </div>

      <ul class="divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($users as $u)
          <li class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2">
                  <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $u->full_name }}</p>
                  {!! $badge(is_string($u->role) ? $u->role : $u->role->value) !!}
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                  {{ $u->email }} · Phone: {{ $u->phone ?? '—' }}
                </p>
              </div>

              <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit',$u) }}"
                   class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                  Edit
                </a>
                <form method="POST" action="{{ route('admin.users.destroy',$u) }}"
                      onsubmit="return confirm('Delete user?')">
                  @csrf @method('DELETE')
                  <button
                    class="rounded-md bg-rose-600 px-3 py-1.5 text-sm text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    Delete
                  </button>
                </form>
              </div>
            </div>
          </li>
        @empty
          <li class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No users found.</li>
        @endforelse
      </ul>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
  </div>
</x-app-layout>
